<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package uncode
 */

get_header();

$tag_name = isset( $_GET['gallery_search'] ) ? $_GET['gallery_search'] : 'No Tag';
$tag_id = false;
$tag = get_term_by('name', $tag_name, 'gallery_tag');
$post_count = $tag->count;
if ($tag) {
  $tag_id = $tag->term_id;
}

$args = array(
  'post_type' => 'counter_gallery',
  'tax_query' => array(
    'relation' => 'AND',
    array(
      'field' => 'term_id',
      'taxonomy' => 'gallery_tag',
      'terms' => array($tag_id),
      'operator' => 'IN'
    )
  ),
  'post_status' => array(
    'publish'
  ),
  'posts_per_page' => -1 // show all
);
$tag_query = new WP_Query($args);

$image_count_message = $post_count . ' images found';
if ( $post_count == 0 ) {
  $image_count_message = 'not found';
}
?>

<!-- 아래의 HTML는 꼭 필요합니다. 이렇게 하지 않으면, menu의 background-color가 transport 에서 white로 변경되기 때문입니다. -->
<div id="page-header">
  <div class="header-basic">
    <div class="header-wrapper">
      <div class="header-main-container limit-width">
        <div class="header-content">
          <div class="header-content-inner">
            <h1 class="search-tags"> <?php echo $tag_name; ?> </h1>
            <div class="tag-count"> <?php echo $image_count_message; ?> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">UNCODE.initHeader();</script>

<div class="page-body style-light-bg">
  <div class="post-wrapper">
    <div class="post-body">
      <div class="post-content">
        <div class="row-container">
          <div class="row row-parent style-light limit-width">
            <div class="row-inner">
              <div class="pos-top pos-center align_left column_parent col-lg-12 boomapps_vccolumn single-internal-gutter">
                <div class="uncol style-light">
                  <div class="uncoltable">
                    <div class="uncell  boomapps_vccolumn no-block-padding">
                      <div class="uncont">
                        <div class="isotope-system isotope-general-light">
                          <div class="isotope-wrapper half-gutter">
                            <div class="isotope-container isotope-layout style-masonry" data-type="masonry" data-layout="masonry" data-lg="1000" data-md="600" data-sm="480"> <?php 
                              if ( $tag_query->have_posts() ) {
                                while( $tag_query->have_posts() ) {
                                  $tag_query->the_post();
                                  get_template_part( 'gallery', 'item' );
                                }
                              } ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>