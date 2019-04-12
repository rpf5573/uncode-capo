<?php
\PC::debug( 'gallery! called', __FUNCTION__ );

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

?>