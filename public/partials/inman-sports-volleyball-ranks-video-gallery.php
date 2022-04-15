<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://inmansports.com
 * @since      1.0.0
 *
 * @package    Inman_Sports_Volleyball_Ranks
 * @subpackage Inman_Sports_Volleyball_Ranks/public/partials
 */

global $post;
$player_id = $post->ID;
$videos = get_field('highlights', $player_id);
?>

<div class="embed__container">
    <div id="player"></div>
</div>

<div class="carousel__wrap">
    <div class="owl-carousel">
        <?php foreach ($videos as $video) : ?>
            <div data-video="<?php echo $video['youtube_id']; ?>" class="item video-thumb">
                <img src="https://i.ytimg.com/vi/<?php echo $video['youtube_id']; ?>/default.jpg" />
            </div>
        <?php endforeach; ?>
    </div>
</div>


<script>
    var tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    var player;

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '315',
            width: '560',
            videoId: '<?php echo $videos[0]['youtube_id']; ?>',
            playerVars: {
                color: 'white',
                showinfo: 0,
                rel: 0,
                enablejsapi: 1,
                modestbranding: 1,
                showinfo: 0,
                ecver: 2,
            },
            events: {
                onStateChange: onPlayerStateChange,
                onReady: function () {
                    jQuery('.ytp-expand-pause-overlay .ytp-pause-overlay').css(
                        'display',
                        'none'
                    );
                    jQuery('.video-thumb').click(function () {
                        console.log('clicked');
                        let $this = jQuery(this);
                        if (!$this.hasClass('active')) {
                            player.loadVideoById($this.attr('data-video'));
                            jQuery('.video-thumb').removeClass('active');
                            $this.addClass('active');
                        }
                    });
                },
            },
        });
        function onPlayerStateChange(e) {
            console.log('state');
            if (e.data == YT.PlayerState.ENDED) {
                document.getElementById('playerWrap').classList.add('shown');
            }
        }
        document.getElementById('playerWrap').addEventListener('click', function () {
            player.seekTo(0);
            document.getElementById('playerWrap').classList.remove('shown');
        });
    }

    (function ($) {
        $(document).ready(function () {
            $('.owl-carousel').owlCarousel({
                loop: false,
                margin: 10,
                nav: true,
                navText: [
                    "<i class='fas fa-chevron-left'></i>",
                    "<i class='fas fa-chevron-right'></i>",
                ],
                autoplay: false,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 3,
                    },
                    600: {
                        items: 4,
                    },
                    1000: {
                        items: 5,
                    },
                },
            });
        });
    })(jQuery);

</script>