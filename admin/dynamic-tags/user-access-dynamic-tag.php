<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Dynamic Tag - Random Number
 *
 * Elementor dynamic tag that returns a random number.
 *
 * @since 1.0.0
 */
class Elementor_Dynamic_Tag_User_Access extends \Elementor\Core\DynamicTags\Tag {

    /**
     * Get dynamic tag name.
     *
     * Retrieve the name of the random number tag.
     *
     * @since 1.0.0
     * @access public
     * @return string Dynamic tag name.
     */
    public function get_name() {
        return 'uaer-access';
    }

    /**
     * Get dynamic tag title.
     *
     * Returns the title of the random number tag.
     *
     * @since 1.0.0
     * @access public
     * @return string Dynamic tag title.
     */
    public function get_title() {
        return esc_html__( 'User Access', 'elementor-random-number-dynamic-tag' );
    }

    /**
     * Get dynamic tag groups.
     *
     * Retrieve the list of groups the random number tag belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Dynamic tag groups.
     */
    public function get_group() {
        return [ 'post' ];
    }

    /**
     * Get dynamic tag categories.
     *
     * Retrieve the list of categories the random number tag belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Dynamic tag categories.
     */
    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
        ];
    }

    /**
     * Render tag output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function render() {

        global $post;
        $user_id = get_current_user_id();

        $user_access = get_field('player', 'user_'.$user_id);
        if ( $user_access == $post->ID ) {
            echo 'Yes';
        } else {
            echo 'No';
        }
    }


}
