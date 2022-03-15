<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://inmansports.com
 * @since      1.0.0
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/admin
 * @author     Andrew Inman <andy@inmansports.com>
 */
class Inman_Sports_Volleyball_Ranks_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/inman-sports-volleyball-ranks-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/inman-sports-volleyball-ranks-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function rename_admin_menu_items($translated_text, $text, $domain ) {

        switch ( $translated_text ) {
            case 'Re-Order' :
                $translated_text = __( 'Ranking', 'inman-sports-volleyball-ranks' );
                break;
        }
        return $translated_text;

    }


    public function save_previous_sort_order($sort_view_id) {

        global $wpdb;

        $settings  = APTO_functions::get_sort_settings( $sort_view_id );

        $sort_list_table_rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}apto_sort_list WHERE sort_view_id=%d", $sort_view_id));

        $sort_list_table_rows_array = array();
        foreach ($sort_list_table_rows as $key => $sort_list_table_row) {
            if ($sort_view_id === 672) {
                update_post_meta( $sort_list_table_row->object_id, 'national_rank', $key + 1 );
                $national_rank_history = get_post_meta($sort_list_table_row->object_id, 'national_rank_history', true);
                if (!$national_rank_history) {
                    $national_rank_history = array();
                } else {
                    $national_rank_history = unserialize($national_rank_history);
                }
                if (count($national_rank_history) > 4) {
                    $national_rank_history = array_slice($national_rank_history, -4);
                }
                $national_rank_history[] = array(
                    'date' => date('Y-m-d H:i:s'),
                    'rank' => $key + 1
                );
                update_post_meta( $sort_list_table_row->object_id, 'national_rank_history', serialize($national_rank_history) );

            } else {
                update_post_meta( $sort_list_table_row->object_id, 'class_rank', $key + 1 );
                $class_rank_history = get_post_meta($sort_list_table_row->object_id, 'class_rank_history', true);
                if (!$class_rank_history) {
                    $class_rank_history = array();
                } else {
                    $class_rank_history = unserialize($class_rank_history);
                }
                if (count($class_rank_history) > 4) {
                    $class_rank_history = array_slice($class_rank_history, -4);
                }
                $class_rank_history[] = array(
                    'date' => date('Y-m-d H:i:s'),
                    'rank' => $key + 1
                );
                update_post_meta( $sort_list_table_row->object_id, 'class_rank_history', serialize($class_rank_history) );
            }
            $sort_list_table_rows_array[] = $sort_list_table_row->object_id;
        }

        $previous_rankings = $wpdb->prefix . 'previous_rankings';

        $rules = get_post_meta($sort_view_id - 1, '_rules');


        $wpdb->insert(
            $previous_rankings,
            array(
                'time' => current_time( 'mysql' ),
                'sort_view_id' => $sort_view_id,
                'tax_id' => $settings['_term_id'],
                'rankings' => maybe_serialize($sort_list_table_rows_array),
            )
        );

    }



    public function setup_subscription_and_push_to_paypal( $fields ) {

        global $wpmem, $wpmem_pp_sub;

        $membership_type = get_field('membership_type', 'user_'.$fields['ID']);
        $player_affiliation = get_field('player', 'user_'.$fields['ID']);

        $product_meta = '';

        if ( $membership_type == 'coachscout' ) {
            $item_name = 'Coach/Scout Membership';
            $subscription_cost = '99.99';
            $subscription_num = '1';
            $subscription_per = 'y';
            $product_meta = 'coach-scout';
        } elseif ( $membership_type == 'player' ) {
            $item_name = 'Player Membership';
            $subscription_cost = '19.99';
            $subscription_num = '1';
            $subscription_per = 'm';
            $product_meta = 'player';
            if ($player_affiliation) {
                update_field( 'player_claimed', 1, $player_affiliation);
                update_field('player_claimed_user', get_user_by('id', $fields['ID']), $player_affiliation);
            }
        }

        wpmem_set_user_product( $product_meta, $fields['ID'] );

        // Get the PayPal Settings.
        $arr = $wpmem_pp_sub->subscriptions['default'];

        // Set up defaults.
        $button_args = array(
            "cmd"           => ( ! $wpmem_pp_sub->paypal_cmd ) ? '_xclick' : $wpmem_pp_sub->paypal_cmd,
            "business"      => $wpmem_pp_sub->paypal_id,
            "item_name"     => $item_name,
            "no_shipping"   => '',
            "return"        => add_query_arg( array( 'a'=>'renew','msg'=>'thankyou' ), $wpmem->user_pages['profile'] ),
            "notify_url"    => $wpmem_pp_sub->paypal_ipn,
            "no_note"       => '1',
            "currency_code" => $wpmem_pp_sub->subscriptions['default']['currency'],
            "rm"            => '2',
            "custom"        => $fields['ID'],
        );

        // Add the user ID.
        $button_args['custom'] = $fields['ID'];

        // Handle regular vs recurring &amp; recurring with trial.
        if ( $button_args['cmd'] === '_xclick' ) {

            $button_args['amount'] = $subscription_cost;

        } else {
            $button_args['a3']  = $subscription_cost;
            $button_args['p3']  = $subscription_num;
            $button_args['t3']  = strtoupper( $subscription_per );
            $button_args['src'] = "1";
            $button_args['sra'] = "1";

        }

        // Set the transaction price for IPN validation.
        $amount = ( isset( $button_args['amount'] ) ) ? $button_args['amount'] : ( ( isset( $button_args['a1'] ) && $button_args['a1'] > 0 ) ? $button_args['a1'] : $button_args['a3'] );
        update_user_meta( $fields['ID'], 'wpmem_paypal_txn_amount', $amount );

        // Build and output the form so it can be submitted.
        echo '<form name="paypalform" action="' . $wpmem_pp_sub->paypal_url . '" method="post">';
        foreach ( $button_args as $key => $val ) {
            echo '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
        echo '</form>';

        // Submit the form with JS.
        echo '<script>document.paypalform.submit();</script>';

        // Exit so no screen output.
        exit();
    }




    public function wpmem_adjust_payment_button( $args ){
        $membership_type = get_field('membership_type', 'user_'.get_current_user_id());

        if ( $membership_type == 'coachscout' ) {
            $args['subscription_name'] = 'Coach/Scout Membership';
            $args['subscription_cost'] = '99.99';
            $args['subscription_num'] = '1';
            $args['subscription_per'] = 'Y';
        } elseif ( $membership_type == 'player' ) {
            $args['subscription_name'] = 'Player Membership';
            $args['subscription_cost'] = '19.99';
            $args['subscription_num'] = '1';
            $args['subscription_per'] = 'M';
        }

        return $args;
    }


    public function change_sort_order_tabs( $tabs, $sort_view_ID ) {
        unset($tabs['auto']);
        return $tabs;
    }


    public function change_sort_taxonomies($taxonomies, $sortID) {
        $taxonomies = array('graduating_class');
        return $taxonomies;
    }

    public function add_additional_details_to_reorder_item($additional_details, $post_data) {
        $graduating_class = get_the_terms($post_data->ID, 'graduating_class');
        $national_rank = get_field( 'national_rank', $post_data->ID);
        $class_rank = get_field( 'class_rank', $post_data->ID);
        $additional_details .= ' | National Rank: ' . $national_rank . ' | Class Rank ('. $graduating_class[0]->name .'): ' . $class_rank;

        return $additional_details;
    }


}
