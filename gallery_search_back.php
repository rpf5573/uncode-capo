<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package uncode
 */

get_header();
?>

<!-- 아래의 HTML는 꼭 필요합니다. 이렇게 하지 않으면, menu의 background-color가 transport 에서 white로 변경되기 때문입니다. -->
<div id="page-header">
  <div class="header-basic">
    <div class="header-wrapper">
      <div class="header-main-container limit-width">
        <div class="header-content">
          <div class="header-content-inner">
            <h1 class="search-tags"> <?php echo $_GET['gallery_search']; ?> </h1>
            <div class="tag-count">5 images found</div>
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
          <div class="row row-parent style-light limit-width double-top-padding double-bottom-padding">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
if ( have_posts() ) {
  
} 
?>

<?php get_footer(); ?>