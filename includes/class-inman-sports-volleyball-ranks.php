<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://inmansports.com
 * @since      1.0.0
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/includes
 * @author     Andrew Inman <andy@inmansports.com>
 */



class Inman_Sports_Volleyball_Ranks {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Inman_Sports_Volleyball_Ranks_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'INMAN_SPORTS_VOLLEYBALL_RANKS_VERSION' ) ) {
			$this->version = INMAN_SPORTS_VOLLEYBALL_RANKS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'inman_sports_volleyball_ranks';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Inman_Sports_Volleyball_Ranks_Loader. Orchestrates the hooks of the plugin.
	 * - Inman_Sports_Volleyball_Ranks_i18n. Defines internationalization functionality.
	 * - Inman_Sports_Volleyball_Ranks_Admin. Defines all hooks for the admin area.
	 * - Inman_Sports_Volleyball_Ranks_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-inman-sports-volleyball-ranks-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-inman-sports-volleyball-ranks-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-inman-sports-volleyball-ranks-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-inman-sports-volleyball-ranks-public.php';

		$this->loader = new Inman_Sports_Volleyball_Ranks_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Inman_Sports_Volleyball_Ranks_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Inman_Sports_Volleyball_Ranks_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Inman_Sports_Volleyball_Ranks_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action( 'gettext', $plugin_admin, 'rename_admin_menu_items', 20, 3 );

        $this->loader->add_action( 'apto/reorder-interface/order_update_complete', $plugin_admin, 'save_previous_sort_order' );

        $this->loader->add_action( 'wpmem_register_redirect', $plugin_admin, 'setup_subscription_and_push_to_paypal' );

        $this->loader->add_filter( 'wpmem_payment_button_args', $plugin_admin, 'wpmem_adjust_payment_button' );

        $this->loader->add_filter( 'apto/admin/sort-order-tabs', $plugin_admin, 'change_sort_order_tabs', 10, 2 );

        $this->loader->add_filter( 'apto/admin/sort-taxonomies', $plugin_admin, 'change_sort_taxonomies', 10, 2 );

        $this->loader->add_filter( 'apto/reorder_item_additional_details', $plugin_admin, 'add_additional_details_to_reorder_item', 10, 2 );

        $this->loader->add_action( 'elementor/dynamic_tags/register', $plugin_admin, 'register_user_access_dynamic_tag', 20 );

        $this->loader->add_action( 'elementor/query/player_news', $plugin_admin, 'update_player_news_query', 10, 2 );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_items' );

        $this->loader->add_filter( 'acf/load_field/name=recruiting_school', $plugin_admin, 'set_recruiting_school_default_value');

        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );

        $this->loader->add_action( 'query_vars', $plugin_admin, 'add_query_vars' );

        $this->loader->add_action( 'init', $plugin_admin, 'add_endpoints' );

        $this->loader->add_action( 'template_redirect', $plugin_admin, 'template_redirect' );

        $this->loader->add_filter( 'acf/fields/relationship/result/name=player_affiliation', $plugin_admin, 'update_player_affiliation_result_value', 10, 4 );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Inman_Sports_Volleyball_Ranks_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        $this->loader->add_shortcode( 'player_previous_ranks', $plugin_public, 'player_previous_ranks' );

        $this->loader->add_shortcode( 'player_video_gallery', $plugin_public, 'player_video_gallery_function' );

        $this->loader->add_shortcode( 'premium_content', $plugin_public, 'premium_content_shortcode_function' );

        $this->loader->add_action( 'acfe/form/submit/post/action=create-player-news', $plugin_public, 'create_player_news_post_args', 10, 5 );

        $this->loader->add_filter( 'posts_table_acf_value', $plugin_public, 'posts_table_acf_value', 10, 3 );

        $this->loader->add_filter( 'posts_table_data_custom_taxonomy_recruiting_school', $plugin_public, 'posts_table_data_custom_taxonomy_recruiting_school', 10, 2 );

        $this->loader->add_action( 'acfe/form/submit/post/form=create-player', $plugin_public, 'create_player_function', 10, 5);

        $this->loader->add_shortcode( 'assign_user_to_player', $plugin_public, 'assign_user_to_player_shortcode' );

        $this->loader->add_action( 'acfe/form/submit/form=claim-your-player', $plugin_public, 'claim_your_player_submit_function', 10, 2 );

        $this->loader->add_filter( 'acf/fields/post_object/query/name=player', $plugin_public, 'player_post_object_query', 10, 3 );

        $this->loader->add_action( 'wp_loaded', $plugin_public, 'add_multiple_to_cart_action', 20 );

        $this->loader->add_shortcode( 'claimed_player_success', $plugin_public, 'claimed_player_success_shortcode' );

        $this->loader->add_shortcode( 'player_search_filters', $plugin_public, 'player_search_filters_shortcode' );

        $this->loader->add_shortcode( 'player_news_success', $plugin_public, 'player_news_success_shortcode' );

        $this->loader->add_shortcode( 'posts_table_for_query', $plugin_public, 'posts_table_for_query_shortcode' );

        $this->loader->add_shortcode( 'ua_next_camp_dates', $plugin_public, 'ua_next_camp_dates_shortcode' );

        $this->loader->add_shortcode( 'players_mentioned_cards', $plugin_public, 'players_mentioned_cards_shortcode' );

        $this->loader->add_filter( 'the_content', $plugin_public, 'add_player_link_popups_to_content' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Inman_Sports_Volleyball_Ranks_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
