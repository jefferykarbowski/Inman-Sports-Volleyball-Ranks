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
class Inman_Sports_Volleyball_Ranks_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/inman-sports-volleyball-ranks-public.css', array(), $this->version, 'all');

        wp_register_style('owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');

        wp_enqueue_style('tooltipster-css', 'https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/css/tooltipster.bundle.css');

        wp_enqueue_style( 'font-awesome-free', '//use.fontawesome.com/releases/v5.6.3/css/all.css' );



    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/inman-sports-volleyball-ranks-public.js', ['jquery', 'jquery-datatables-ptp', 'jquery-blockui', 'select2-ptp', 'tooltipster-js'], $this->version, true);

        wp_register_script('owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), '2.3.4', true);

        // register tooltipster
        wp_enqueue_script('tooltipster-js', 'https://cdnjs.cloudflare.com/ajax/libs/tooltipster/4.2.8/js/tooltipster.bundle.min.js', array('jquery'), '4.2.8', true);

    }

    /**
     * Add the shortcode to get the player rankings
     * @param $attr
     * @return void
     */
    public function player_previous_ranks($atts)
    {

        $a = shortcode_atts(array('rank_type' => 'national',), $atts);

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

            if ($rankings[$i + 1]['rank']) {
                if ($rank['rank'] > $rankings[$i + 1]['rank']) {
                    echo '<i aria-hidden="true" class="fas fa-angle-double-down"></i>';
                } elseif ($rank['rank'] < $rankings[$i + 1]['rank']) {
                    echo '<i aria-hidden="true" class="fas fa-angle-double-up"></i>';
                } else {
                    echo '<i class="fas fa-arrows-alt-h"></i>';
                }
            } else {
                echo '<i class="fas fa-transporter-empty"></i>';
            }

            echo '</span>';
            echo '<span class="elementor-icon-list-text">';

            // format $rank['date'] to m-d-Y
            $date = date('m/d/Y', strtotime($rank['date']));

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



    // Add the shortcode to display the video gallery
    /*
     * @param $attr
     * @return void
     */
    public function player_video_gallery_function()
    {

        wp_enqueue_style('owl-carousel-css');
        wp_enqueue_script('owl-carousel-js');

        ob_start();

        include(plugin_dir_path(__FILE__) . 'partials/inman-sports-volleyball-ranks-video-gallery.php');

        return ob_get_clean();

    }

    public function create_player_news_post_args($post_id, $type, $args, $form, $action)
    {

        $featured_image_id = get_field('featured_image', $post_id);
        if ($featured_image_id) {
            add_post_meta($post_id, '_thumbnail_id', $featured_image_id);
        }

        $user_id = get_current_user_id();
        $player_affiliation = get_field('player', 'user_' . $user_id);

        // add_post_meta($post_id, 'player_affiliation', $player_affiliation);
        $player = get_field('player_affiliation', $post_id, false);
        $player[] = $player_affiliation;
        update_field('player_affiliation', $player, $post_id);

        wp_set_post_categories($post_id, array(42), false);

    }

    public function posts_table_acf_value($value, $field_obj, $post_id)
    {
        if ('star_rating' === $field_obj['name']) {
            return $this->create_star_rating($field_obj['value']);
        } else {
            return $value;
        }
    }

    public function create_star_rating($rating)
    {

        $memberships = wpmem_get_user_products();

        if ($memberships['premium-access']) {
            $stars = '<div class="elementor-star-rating" title="' . $rating . '/5" itemtype="http://schema.org/Rating" itemscope="" itemprop="reviewRating">';
            for ($i = 1; $i <= 5; $i++) {
                $stars .= ($i <= $rating ? '<i class="elementor-star-full">&#9733;</i>' : '<i class="elementor-star-empty">&#9733;</i>');
            }
            $stars .= '</div>';
            $stars .= '<span class="elementor-screen-only">' . $rating . '/5</span>';
        } else {
            // show locked icon greyed out
            $stars = '<div class="locked" itemprop="reviewRating">';
            $stars .= '<i aria-hidden="true" class="fa fa-lock" style="color:#e60e16;"></i>';
            $stars .= '</div>';
        }

        return $stars;

    }

    public function posts_table_data_custom_taxonomy_recruiting_school($terms, $post)
    {

        return str_replace('Uncommitted', '&nbsp;', $terms);

    }

    public function premium_content_shortcode_function($atts, $content = null)
    {

        if (!is_user_logged_in()) {
            return;
        }

        global $post;

        $a = shortcode_atts(array('has_player_access' => 0, 'player_content' => 0, 'nested_shortcode' => 0,), $atts);

        if ($a['has_player_access'] === 'false') $a['has_player_access'] = false;
        if ($a['player_content'] === 'false') $a['player_content'] = false;
        if ($a['nested_shortcode'] === 'false') $a['nested_shortcode'] = false;

        $user_id = get_current_user_id();

        $memberships = wpmem_get_user_products();

        if (!$a['has_player_access'] && !$a['player_content']) {
            if ($memberships['premium-access'] && !$memberships['player']) {
                if ($a['nested_shortcode']) {
                    return do_shortcode($content);
                } else {
                    return $content;
                }
            }
        }

        if ($a['has_player_access']) {
            if ($memberships['player']) {
                if ($a['nested_shortcode']) {
                    return do_shortcode($content);
                } else {
                    return $content;
                }
            }
        }

        if ($a['player_content']) {
            if ($memberships['player']) {
                $player_affiliation = get_field('player', 'user_' . $user_id);
                if ($player_affiliation->ID === $post->ID) {
                    if ($a['nested_shortcode']) {
                        return do_shortcode($content);
                    } else {
                        return $content;
                    }
                }
            }
        }

        return '';

    }

    public function create_player_function($post_id, $type, $args, $form, $action)
    {

        $user_id = get_current_user_id();
        update_field('player', $post_id, 'user_' . $user_id);

    }

    public function assign_user_to_player_shortcode()
    {

        if (!is_user_logged_in()) {
            return;
        }

        global $post;

        $user_id = get_current_user_id();

        $memberships = wpmem_get_user_products();

        ob_start();

        if ($memberships['player']) {
            $player_affiliation = get_field('player', 'user_' . $user_id);

            if (!$player_affiliation) {

                ?>

                <div class="elementor-element elementor-element-1ee6767 elementor-widget elementor-widget-button"
                     data-id="1ee6767" data-element_type="widget" data-widget_type="button.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-button-wrapper">
                            <a href="/create-a-player-profile"
                               class="elementor-button-link elementor-button elementor-size-sm" role="button">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">Create A Player Profile</span>
		</span>
                            </a>
                        </div>
                    </div>
                </div>

                <?php

                echo do_shortcode('[acfe_form name="claim-your-player"]');

            } else {
                // if $player_affiliation post_status is not 'publish'
                if (get_post_status($player_affiliation) != 'publish') :
                    ?>

                    <p>You have claimed <?php echo get_the_title($player_affiliation); ?>, your submission is being
                        reviewed, we will contact you shortly when published.</p>

                <?php else: ?>

                    <p>You have claimed <a
                                href="<?php echo get_permalink($player_affiliation); ?>"><?php echo get_the_title($player_affiliation); ?></a>
                    </p>

                    <div class="elementor-element elementor-element-1ee6767 elementor-widget elementor-widget-button"
                         data-id="1ee6767" data-element_type="widget" data-widget_type="button.default">
                        <div class="elementor-widget-container">
                            <div class="elementor-button-wrapper">
                                <a href="<?php echo get_permalink($player_affiliation); ?>"
                                   class="elementor-button-link elementor-button elementor-size-sm" role="button">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">Edit Profile</span>
		</span>
                                </a>
                            </div>
                        </div>
                    </div>


                <?php

                endif;

            }

        }

        return ob_get_clean();

    }

    public function claim_your_player_submit_function($form, $post_id)
    {

        $user_id = get_current_user_id();
        $player_affiliation = get_field('player', 'user_' . $user_id);

    }

    public function player_post_object_query($args, $field, $post_id)
    {

        $args['meta_query'] = array(relation => 'OR', array('key' => 'player_claimed_user', 'compare' => 'NOT EXISTS'), array('key' => 'player_claimed_user', 'value' => '', 'compare' => '='));

        return $args;

    }

    public function add_multiple_to_cart_action()
    {
        if (!isset($_REQUEST['multiple-item-to-cart']) || false === strpos(wp_unslash($_REQUEST['multiple-item-to-cart']), '|')) {
            return;
        }

        wc_nocache_headers();

        $product_ids = apply_filters('woocommerce_add_to_cart_product_id', wp_unslash($_REQUEST['multiple-item-to-cart'])); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
        $product_ids = explode('|', $product_ids);
        if (!is_array($product_ids)) return;

        $product_ids = array_map('absint', $product_ids);
        $was_added_to_cart = false;
        $last_product_id = end($product_ids);
        //stop re-direction
        add_filter('woocommerce_add_to_cart_redirect', '__return_false');
        foreach ($product_ids as $index => $product_id) {
            $product_id = absint($product_id);
            if (empty($product_id)) continue;
            $_REQUEST['add-to-cart'] = $product_id;
            if ($product_id === $last_product_id) {

                add_filter('option_woocommerce_cart_redirect_after_add', function () {
                    return 'yes';
                });
            } else {
                add_filter('option_woocommerce_cart_redirect_after_add', function () {
                    return 'no';
                });
            }

            WC_Form_Handler::add_to_cart_action();
        }
    }

    public function claimed_player_success_shortcode($atts)
    {
        ob_start();
        $user_id = get_current_user_id();
        $player_affiliation = get_field('player', 'user_' . $user_id);
        if (isset($_GET['claimed-player']) && $player_affiliation) : ?>
            <p>You have successfully claimed <?php echo get_the_title($player_affiliation); ?>, your submission is being
                reviewed, we will contact you shortly when published.</p>

            <!--            <div class="elementor-element elementor-element-1ee6767 elementor-widget elementor-widget-button" data-id="1ee6767" data-element_type="widget" data-widget_type="button.default">-->
            <!--                <div class="elementor-widget-container">-->
            <!--                    <div class="elementor-button-wrapper">-->
            <!--                        <a href="--><?php //echo get_permalink($player_affiliation); ?><!--" class="elementor-button-link elementor-button elementor-size-sm" role="button">-->
            <!--						<span class="elementor-button-content-wrapper">-->
            <!--						<span class="elementor-button-text">Edit Profile</span>-->
            <!--		</span>-->
            <!--                        </a>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
        <?php
        endif;
        return ob_get_clean();
    }

    public function player_search_filters_shortcode($atts)
    {

        // get the graduating_class, position, and recruiting_school taxonomy terms that have at least one post and assign them to variables
        $graduating_class_terms = get_terms(array('taxonomy' => 'graduating_class', 'hide_empty' => false,));
        $postion_terms = get_terms(array('taxonomy' => 'position', 'hide_empty' => false,));
        $recruiting_school_terms = get_terms(array('taxonomy' => 'recruiting_school', 'hide_empty' => false,));

        // create dropdowns with checkboxes for each taxonomy term
        $graduating_class_dropdown = '<select name="graduating_class" id="graduating_class" class="graduating_class dropdown-check-list">';
        foreach ($graduating_class_terms as $graduating_class_term) {
            $graduating_class_dropdown .= '<option value="' . $graduating_class_term->name . '" selected="selected" >' . $graduating_class_term->name . '</option>';
        }
        $graduating_class_dropdown .= '</select>';

        $position_dropdown = '<select name="position" id="position" class="position dropdown-check-list">';
        foreach ($postion_terms as $position_term) {
            $position_dropdown .= '<option value="' . $position_term->name . '" selected="selected" >' . $position_term->name . '</option>';
        }
        $position_dropdown .= '</select>';

        $recruiting_school_dropdown = '<select name="recruiting_school" id="recruiting_school" class="recruiting_school dropdown-check-list">';
        $recruiting_school_dropdown .= '<option value="" selected="selected" >Uncommitted</option>';
        foreach ($recruiting_school_terms as $recruiting_school_term) {
            $recruiting_school_dropdown .= '<option value="' . $recruiting_school_term->name . '" selected="selected" >' . $recruiting_school_term->name . '</option>';
        }
        $recruiting_school_dropdown .= '</select>';

        ob_start(); ?>

        <div class="player-search-filters">
            <div class="player-search-filters__graduating-class">
                <?php echo $graduating_class_dropdown; ?>
            </div>
            <div class="player-search-filters__position">
                <?php echo $position_dropdown; ?>
            </div>
            <div class="player-search-filters__recruiting-school">
                <?php echo $recruiting_school_dropdown; ?>
            </div>

        </div>

        <script>
            jQuery(document).ready(function ($) {

                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {

                        let graduating_class_column = data[1]
                        let graduating_class_filter = $('#graduating_class').val()

                        let position_column = data[2]
                        let position_filter = $('#position').val()

                        let recruiting_schools_column = data[4]
                        let recruiting_schools_filter = $('#recruiting_school').val()

                        if (
                            ($.inArray(graduating_class_column, graduating_class_filter) > -1) &&
                            ($.inArray(position_column, position_filter) > -1) &&
                            ($.inArray(recruiting_schools_column, recruiting_schools_filter) > -1)
                        ) {
                            return true
                        }

                        return false
                    }
                )

            })

        </script>


        <?php

        return ob_get_clean();

    }

    public function player_news_success_shortcode($atts)
    {
        ob_start();
        if (isset($_GET['post-pending'])) : ?>
            <p>You post is Pending Review, it will show up shortly when published.</p>
        <?php endif;
        return ob_get_clean();
    }

    public function posts_table_for_query_shortcode()
    {

        if (function_exists('ptp_the_posts_table')) {
            $shortcode = '[posts_table]';

            $args = shortcode_parse_atts(str_replace(array('[posts_table', ']'), '', $shortcode));
            $args = !empty($args) && is_array($args) ? $args : array();

            if (is_category()) {
                $args['category'] = get_queried_object_id();
            } elseif (is_tag()) {
                $args['tag'] = get_queried_object_id();
            } elseif (is_author()) {
                $args['author'] = get_queried_object_id();
            } elseif (is_year()) {
                $args['year'] = get_queried_object_id();
            } elseif (is_month()) {
                $args['month'] = get_queried_object_id();
            } elseif (is_date()) {
                $args['day'] = get_queried_object_id();
            } elseif (is_tax()) {
                $term = get_term(get_queried_object_id());
                $args['term'] = $term->taxonomy . ':' . get_queried_object_id();
            }

            return ptp_the_posts_table($args);
        } else {
            return '<p>Posts Table Pro is not installed. Please install it to use this shortcode.</p>';
        }
    }

    public function ua_next_camp_dates_shortcode()
    {

        ob_start();

        $terms = get_terms(array('taxonomy' => 'ua_next_camp', 'hide_empty' => false, 'meta_key' => 'tax_position', 'orderby' => 'tax_position',));

        echo '<div class="ua-next-camp-dates">';
        foreach ($terms as $term) { ?>

            <p>
                <?php if ($term->count > 0) : ?><a class="select_ua_next_camp" href="javascript:void(0)"
                                                   data-select="<?php echo $term->slug; ?>"><?php endif; ?>
                    <strong><?php echo $term->name; ?></strong><?php if ($term->count > 0) : ?></a><?php endif; ?><br>
                <?php echo get_field('venue', $term); ?>
            </p>


        <?php }
        echo '</div>';
        return ob_get_clean();

    }

    public function players_mentioned_cards_shortcode()
    {

        ob_start();

        $player_affiliation = get_field('player_affiliation');

        echo '<div class="players-mentioned-cards">';

        foreach ($player_affiliation as $player_id) {

            $player_name = get_the_title($player_id);

            $player_image = get_field('image', $player_id);
            if (!($player_image)) {
                $player_image['ID'] = 1140;
            }
            $player_image = wp_get_attachment_image( $player_image['ID'], array(100, 100) );

            $player_link = get_permalink($player_id);

            $player_position = get_the_terms($player_id, 'position');
            $player_position = $player_position[0]->name;

            $player_club_team = get_the_terms($player_id, 'club_team');
            $player_club_team = $player_club_team[0]->name;

            $player_recruiting_school = get_the_terms($player_id, 'recruiting_school');
            $player_recruiting_school = $player_recruiting_school[0]->name;

            $player_rating = get_field('star_rating', $player_id);

            echo '<a href="' . $player_link . '" class="player-card">';
            echo '<div class="player-quick-box" onclick="javascript:">';
            echo '<div class="player-image">';
            echo $player_image;
            echo '</div>';
            echo '<div class="player-info">';
            echo '<h3>' . $player_name . '</h3>';
            echo '<p>' . $player_position . ' | ' . $player_club_team . '</p>';
            echo $player_recruiting_school ? '<p>Committed to: ' . $player_recruiting_school . '</p>' : '';
            echo '<p>' . $this->create_star_rating($player_rating) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';

        }

        echo '</div>';

        return ob_get_clean();

    }

    public function add_player_link_popups_to_content($content)
    {

        $memberships = wpmem_get_user_products();

        if ((is_singular()) && (is_main_query())) {
            $string = $content;

            // find anchor tags with '/player/' in the href and also get the data-id attribute
            preg_match_all('/<a.*?href="(.*?)".*?data-id="(.*?)".*?>/', $string, $matches);

            // loop through the matches and replace the anchor tags with the player link popup
            foreach ($matches[0] as $key => $match) {

                $player_id = $matches[2][$key];

                $player_position = get_the_terms($player_id, 'position');
                $player_position = $player_position[0]->name;

                $player_club_team = get_the_terms($player_id, 'club_team');
                $player_club_team = $player_club_team[0]->name;

                $player_recruiting_school = get_the_terms($player_id, 'recruiting_school');
                $player_recruiting_school = $player_recruiting_school[0]->name;

                if ($memberships['premium-access']) {
                    $player_rating = get_field('star_rating', $player_id);
                } else {
                    $player_rating = 'locked';
                }


                $string = str_replace($match, '<a href="' . $matches[1][$key] . '" data-id="' . $matches[2][$key] . '" class="player-link-popup" data-position="' . $player_position . '" data-club_team="' . $player_club_team . '" data-recruiting_school="' . $player_recruiting_school . '" data-rating="' . $player_rating . '">', $string);
            }

            return $string;

        }

        return $content;

    }
}
