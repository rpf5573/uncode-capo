<?php 
$post_id = get_the_ID();
$item_thumb_id = get_post_thumbnail_id( get_the_ID() );
$media_attributes = uncode_get_media_info($item_thumb_id);
if ( is_null($media_attributes) ) {
  return;
}
$media_metavalues = unserialize($media_attributes->metadata);
$image_orig_w = $media_metavalues['width'];
$image_orig_h = $media_metavalues['height'];
$dummy_padding = round(($image_orig_h / $image_orig_w) * 100, 2);
$big_image = uncode_resize_image($media_attributes->id, (is_array($media_attributes->guid) ? $media_attributes->guid['url'] : $media_attributes->guid), $media_attributes->path, $image_orig_w, $image_orig_h, 12, null, false);
$item_media = $big_image['url'];
$image_title = cp_gallery_title_and_subtitle($post_id);
?>

<div class="gallery-item tmb tmb-iso-w4 tmb-iso-h4 tmb-light tmb-text-showed tmb-overlay-text-anim tmb-overlay-anim tmb-content-left tmb-image-anim tmb-bordered  tmb-id-13821 tmb-content-under tmb-media-first tmb-no-bg">
  <div class="t-inside">
    <div class="t-entry-visual" tabindex="0">
      <div class="t-entry-visual-tc">
        <div class="t-entry-visual-cont">
          <div class="dummy" style="padding-top: <?php echo $dummy_padding; ?>%;"></div>
          <a tabindex="-1"
            href="<?php echo $item_media; ?>"
            class="pushed" data-title="<?php echo $image_title; ?>"
            data-caption="<?php echo cp_gallery_tags_for_item($post_id); ?>" data-deep="index-453784" data-notmb="1"
            data-lbox="ilightbox_index-453784"
            data-options="width:<?php echo $image_orig_w; ?>,height:<?php echo $image_orig_h; ?>,thumbnail: '<?php echo $item_media; ?>'"
            target="_self" title="<?php  ?>">
            <div class="t-entry-visual-overlay">
              <div class="t-entry-visual-overlay-in style-dark-bg" style="opacity: 0.5;">
              </div>
            </div>
            <div class="t-overlay-wrap">
              <div class="t-overlay-inner">
                <div class="t-overlay-content">
                  <div class="t-overlay-text single-block-padding">
                    <div class="t-entry t-single-line">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <img
              src="<?php echo $item_media; ?>"
              width="<?php echo $image_orig_w; ?>" height="<?php echo $image_orig_h; ?>" alt="">
          </a>
        </div>
      </div>
    </div>
    <div class="t-entry-text">
      <div class="t-entry-text-tc single-block-padding">
        <div class="t-entry">
          <h3 class="t-entry-title h6">
            <?php the_title(); ?>
          </h3>
          <div class="t-entry-sub-title">
            <?php the_excerpt(); ?>
          </div>
          <div class="t-entry-tags"> <?php
            echo cp_gallery_tags_for_item($post_id); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>