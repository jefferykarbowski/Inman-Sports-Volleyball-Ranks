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

<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.4.0/css/lightgallery-bundle.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.4.0/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.4.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.4.0/plugins/video/lg-video.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.5.0-beta.5/plugins/vimeoThumbnail/lg-vimeo-thumbnail.min.js"></script>



<div id="lightgallery">


    <?php  foreach ($videos as $video) :  ?>



        <?php if ($video['video_type'] == 'YouTube') : ?>
    <a
            data-lg-size="1280-720"
            data-src="//www.youtube.com/watch?v=<?php echo $video['youtube_id']; ?>"
            data-poster="https://img.youtube.com/vi/<?php echo $video['youtube_id']; ?>/maxresdefault.jpg"
    >
        <img
                width="300"
                height="100"
                class="img-responsive"
                src="https://img.youtube.com/vi/<?php echo $video['youtube_id']; ?>/maxresdefault.jpg"
        />
    </a>
        <?php elseif ($video['video_type'] == 'Vimeo') : ?>


    <a
            data-lg-size="1280-720"
            data-src="//vimeo.com/<?php echo $video['vimeo_id']; ?>"
            data-poster="https://vumbnail.com/<?php echo $video['vimeo_id']; ?>.jpg"
    >
        <img
                width="300"
                height="100"
                class="img-responsive"
                src="https://vumbnail.com/<?php echo $video['vimeo_id']; ?>.jpg"
        />
    </a>
        <?php endif; ?>


    <?php endforeach; ?>


</div>


<div class="hudl-videos">
    <?php  foreach ($videos as $video) :  ?>
        <?php if ($video['video_type'] == 'Hudl') : ?>
        <div class="hudl-video">
            <iframe src='<?php echo $video['hudl_embed_url']; ?>' width='1060' height='596'  allowfullscreen></iframe>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>




<script type="text/javascript">
    lightGallery(document.getElementById('lightgallery'), {
        plugins: [lgThumbnail, lgVideo],
    });
</script>