<?php

function is_gallery_search() {
  if ( isset($_REQUEST['gallery_search']) ) {
    return true;
  }
  return false;
}

/**
 * Add gallery custom post type
 *
 * @return void
 */
function cp_add_gallery_post_type() {
  $labels = array(
    "name" => __( "갤러리", "uncode" ),
    "singular_name" => __( "갤러리", "uncode" ),
  );

  $args = array(
    "label" => __( "갤러리", "uncode" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "delete_with_user" => false,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => true,
    "exclude_from_search" => false,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => array( "slug" => "counter_gallery", "with_front" => true ),
    "query_var" => true,
    "supports" => array( "title", "editor", "thumbnail", "excerpt" ),
    "taxonomies" => array( "gallery_tag" ),
  );

  register_post_type( "counter_gallery", $args );
  remove_post_type_support( 'counter_gallery', 'editor' );
}
add_action( 'init', 'cp_add_gallery_post_type' );

/**
 * Add gallery tag custom taxonomy, then attatch it only to gallery post type
 *
 * @return void
 */
function cp_add_gallery_tag_custom_taxonomy() {

	/**
	 * Taxonomy: 갤러리 태그.
	 */

	$labels = array(
		"name" => __( "갤러리 태그", "uncode" ),
		"singular_name" => __( "갤러리 태그", "uncode" ),
	);

	$args = array(
		"label" => __( "갤러리 태그", "uncode" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'gallery_tag', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "gallery_tag",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => true,
		);
	register_taxonomy( "gallery_tag", array( "counter_gallery" ), $args );
}
add_action( 'init', 'cp_add_gallery_tag_custom_taxonomy' );

/**
 * Add view more button on gallery image item
 *
 * @param [String] $caption
 * @param [int] $thumbnail_id
 * @return $caption
 */
function cp_add_view_more_btn($image_excerpt, $thumbnail_id, $post_id) {
  if ( is_front_page() && !is_gallery_search() ) {
    $galleryUrl = home_url( 'gallery' );
    $image_excerpt .= '<a href=' . $galleryUrl . ' class=view-more-btn>Go To Gallery</a>';
  }
  return $image_excerpt;
}
add_filter('uncode_media_attribute_excerpt', 'cp_add_view_more_btn', 1000, 3);

function cp_add_post_title_and_excerpt_on_image_title( $image_title, $item_thumb_id, $post_id ) {
  if ( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type == 'counter_gallery' ) {
      $image_title = cp_gallery_title_and_subtitle( $post_id );
    }
  }
  return $image_title;
}
add_filter('uncode_media_attribute_title', 'cp_add_post_title_and_excerpt_on_image_title', 1000, 3);

function cp_show_post_tags_on_image_caption($image_excerpt, $item_thumb_id, $post_id) {
  if ( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type == 'counter_gallery' ) {
      $image_excerpt = cp_gallery_tags_for_item( $post_id );
    }
  }
  return $image_excerpt;
}
add_filter( 'uncode_media_attribute_excerpt', 'cp_show_post_tags_on_image_caption', 1000, 3 );

/**
 * Add a field in customizer to manage placeholder of search bar
 *
 * @param [WP_Customizer] $wp_customizer
 * @return void
 */
function cp_add_customizer_to_edit_placeholder_of_search_bar( $wp_customizer ) {
  $wp_customizer->add_section(
    'cp_gallery_section',
    array(
      'type'    => 'theme_mode',
      'title'   => 'Gallery',
    )
  );
  $wp_customizer->add_setting(
    'cp_gallery_search_bar_placeholder_setting', 
    array(
      'default' => 'Suggested : iPhoneXS, Galaxy S10, Sky, Flowers, Foods',
    )
  );
  $wp_customizer->add_control(
    'cp_gallery_search_bar_placeholder_setting',
    array(
      'label'   => __('Search Bar Placeholder'),
      'type'    => 'textarea',
      'section' => 'cp_gallery_section'
    )
  );
}
add_action( 'customize_register', 'cp_add_customizer_to_edit_placeholder_of_search_bar' );

/**
 * Shortcode for search bar
 *
 * @param [Array] $atts
 * @return void
 */
function cp_search_bar_shortcode($atts) {
  extract( shortcode_atts( array(
    'placeholder'  => 'This is test placeholder'
    ), $atts )
  );
  $home_url = home_url();
  $form = "<form class='gallery-search-form' action='{$home_url}' method='get'>" .
            "<input type='text' placeholder='{$placeholder}' class='gallery-search-bar' name='gallery_search'>" .
            "<button type='submit' class='gallery-search-submit-btn'>Submit</button>" .
          "</form>";
  return $form;
}
add_shortcode('counter_point_search_bar', 'cp_search_bar_shortcode');

/**
 * load gallery_search.php when user search tag in gallery page
 *
 * @param [String] $template
 * @return void
 */
function cp_search_template_load($template) {
  if ( is_gallery_search() ) {
    $template = locate_template(array('gallery_search.php'));
    \PC::debug( ['template' => $template], __FUNCTION__ );
  }
  return $template;
}
add_filter('template_include', 'cp_search_template_load');

/**
 * Change page title when in gallery search page
 *
 * @param [String] $title
 * @return void
 */
function cp_gallery_search_page_title($title) {
  if ( is_gallery_search() ) {
    $title = 'Gallery Search Results';
  }
  return $title;
}
add_filter('pre_get_document_title', 'cp_gallery_search_page_title', 9999 );

/**
 * Remove home class and add gallery search result class
 *
 * @param [type] $classes
 * @return void
 */
function cp_body_class_filter($classes) {
  if ( is_gallery_search() ) {
    if (($key = array_search('home', $classes)) !== false) {
      unset($classes[$key]);
    }
    $classes[] = 'gallery-search-result-page';
  }
  return $classes;
}
add_filter('body_class', 'cp_body_class_filter');

/**
 * Make gallery title and subtitle string
 *
 * @param [Int] $post_id
 * @return void
 */
function cp_gallery_title_and_subtitle($post_id) {
  $post_title = get_the_title( $post_id );
  $post_excerpt = get_the_excerpt( $post_id );
  $output = "<div class='gallery-item-title-container'>" .
              "<div class='gallery-item-title'>{$post_title}</div>" .
              "<div class='gallery-item-subtitle'>{$post_excerpt}</div>" .
            "</div>";
  return $output;
}

function cp_gallery_tags_for_item($post_id) {
  $tags = get_the_terms( $post_id, 'gallery_tag' );
  $output = '';
  if ( is_array($tags) ) {
    foreach($tags as $tag){
      $href = home_url() . '?gallery_search=' . $tag->name;
      $output .= "<a class='gallery-item-tag-box' href={$href}>{$tag->name}</a>";
    }
  }
  return $output;
}
?>