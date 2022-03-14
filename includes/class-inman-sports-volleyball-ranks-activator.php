<?php

/**
 * Fired during plugin activation
 *
 * @link       https://inmansports.com
 * @since      1.0.0
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/includes
 * @author     Andrew Inman <andy@inmansports.com>
 */
class Inman_Sports_Volleyball_Ranks_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'previous_rankings';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		sort_view_id mediumint(9),
		tax_id mediumint(9),
		rankings blob NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'vball_db_version', $vball_db_version );


	}

}
