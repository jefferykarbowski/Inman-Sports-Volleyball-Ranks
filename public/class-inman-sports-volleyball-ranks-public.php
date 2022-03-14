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

    /**
     * Add the shortcode to get the player rankings
     * @param $attr
     * @return void
     */
    public function player_previous_ranks($atts) {

        $a = shortcode_atts( array(
            'rank_type' => 'national',
        ), $atts );

        global $post;

        $player_id = $post->ID;

        $graduating_class = wp_get_post_terms($player_id, 'graduating_class', array("fields" => "names"));

        if ($a['rank_type'] == 'national') {
            $rankings = get_post_meta($player_id, 'national_rank_history', true);
        } else {
            $rankings = get_post_meta($player_id, 'class_rank_history', true);
        }

        if (empty($rankings)) {
            return;
        }

        $rankings = unserialize($rankings);
        $rankings = array_reverse($rankings);

        ob_start();

        echo '<ul class="elementor-icon-list-items ranking-list">';

        foreach ($rankings as $i => $rank) {

            echo '<li class="elementor-icon-list-item">';
            echo '<span class="elementor-icon-list-icon">';

            if ($rankings[$i+1]['rank']) {
                if ($rank['rank'] > $rankings[$i + 1]['rank']) {
                    echo '<i aria-hidden="true" class="fas fa-angle-double-down"></i>';
                } elseif ($rank['rank'] < $rankings[$i + 1]['rank']) {
                    echo '<i aria-hidden="true" class="fas fa-angle-double-up"></i>';
                } else {
                    echo '<i class="fas fa-arrows-alt-h"></i>';
                }
            }

            echo '</span>';
            echo '<span class="elementor-icon-list-text">';

            // format $rank['date'] to m-d-Y
            $date = date('m-d-Y', strtotime($rank['date']));

            if ($a['rank_type'] == 'national') {
                echo $date . ': #' . $rank['rank'] . ' National Ranking';
            } else {
                echo $date . ': #' . $rank['rank'] . ' Class of ' . $graduating_class[0] . ' Ranking';
            }

            echo '</span>';
            echo '</li>';
        }

        echo '</ul>';

        return ob_get_clean();

    }




}
