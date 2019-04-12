<?php
$rootPath = get_stylesheet_directory();
\PC::debug( ['rootPath' => $rootPath], __FUNCTION__ );
require_once $rootPath . '/includes/gallery.php';

add_action('after_setup_theme', 'uncode_language_setup');
function uncode_language_setup()
{
	load_child_theme_textdomain('uncode', get_stylesheet_directory() . '/languages');
}

function theme_enqueue_styles()
{
	$production_mode = ot_get_option('_uncode_production');
	$resources_version = ($production_mode === 'on') ? null : rand();
	$parent_style = 'uncode-style';
	$child_style = array('uncode-custom-style');
	wp_enqueue_style($parent_style, get_template_directory_uri() . '/library/css/style.css', array(), $resources_version);
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', $child_style, $resources_version);
	wp_enqueue_script( 'child-js', get_stylesheet_directory_uri(), '/main.js', '1.0.0', true );

	if ( isset($_GET['s']) ) {
		\PC::debug( ['search keyword' => $_GET['s']], __FUNCTION__ );
		wp_enqueue_script( 'vc_masonry', vc_asset_url( 'lib/bower/masonry/dist/masonry.pkgd.min.js' ), array(), '1.0.0', true );
	}

}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

// add category nicenames in body and post class
function category_id_class( $classes ) {
	global $post;
	foreach ( ( get_the_category( $post->ID ) ) as $category ) {
		$classes[] = $category->category_nicename;
	}
	return $classes;
}
add_filter( 'post_class', 'category_id_class' );
add_filter( 'body_class', 'category_id_class' );

// add language class
add_filter('body_class', 'append_language_class');

function append_language_class($classes){
	$classes[] = ICL_LANGUAGE_CODE; 
	return $classes;
}