<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://inmansports.com
 * @since      1.0.0
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/public
 * @author     Andrew Inman <andy@inmansports.com>
 */
class Inman_Sports_Volleyball_Ranks_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/inman-sports-volleyball-ranks-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/inman-sports-volleyball-ranks-public.js', array( 'jquery' ), $this->version, false );

	}


    public function wpmem_adjust_payment_options(){
        $arr = array(
            'option_name' => 'player',
            'tier_2_val'  => 'subscription_player',
            'tier_2_name' => 'Player',
            'tier_2_cost' => '19.99',
        );

        return $arr;

    }



    public function wpmem_adjust_payment_button( $args ){

        extract( $this->wpmem_adjust_payment_options() );

        // handle new registration
        global $wpmem_regchk;
        if ( $wpmem_regchk == 'success' ) {

            // we just registered a user, get the $_POST
            if ( isset( $_POST[$option_name] ) && $_POST[$option_name] == $tier_2_val ) {

                // change to the higher value option
                $args['subscription_name'] = $tier_2_name;
                $args['subscription_cost'] = $tier_2_cost;
                return $args;

            }

        }

        // handle renewal
        if ( isset( $_GET['a'] ) && $_GET['a'] == 'renew' ) {

            // get the user's subscription level for renewal
            $subscription_type = get_user_meta( get_current_user_id(), $option_name, true );

            // if they are the subscription level 2
            if ( $subscription_type == $tier_2_val ) {

                // change to the higher value option
                $args['subscription_name'] = $tier_2_name;
                $args['subscription_cost'] = $tier_2_cost;
                return $args;

            }
        }

        // if we get here, return $args unchanged
        return $args;
    }



    public function wpmem_adjust_payment_form( $arr ) {

        extract( wpmem_adjust_payment_options() );
        $subscription_type = get_user_meta( get_current_user_id(), $option_name, true );

        if ( $subscription_type == $tier_2_val ) {
            $arr['subscription_cost'] = $tier_2_cost;
        }

        return $arr;
    }



}
