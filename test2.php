<?php

class uncode_index extends WPBakeryShortCode
{
	protected $filter_categories = array();
	protected $query = false;
	protected $loop_args = array();
	protected $taxonomies = false;

	function __construct($settings)
	{
		parent::__construct($settings);
	}

	protected function getFileName()
	{
		return 'uncode_index';
	}

	public function template($content = '')
	{
		return $this
			->contentAdmin($this->atts);
	}

	public function getCategoriesLink( $post_id ) {
		$categories_link = array();
		$args = array('orderby' => 'taxonomy', 'order' => 'DESC', 'fields' => 'all');
		$post_categories = wp_get_object_terms( $post_id, $this->getTaxonomies(), $args);
		foreach ( $post_categories as $cat ) {
			if (is_taxonomy_hierarchical($cat->taxonomy) && substr( $cat->taxonomy, 0, 3 ) !== 'pa_') {
				$categories_link[] = array('link' => '<a href="'.get_term_link($cat->term_id, $cat->taxonomy).'">'.$cat->name.'</a>', 'tax' => $cat->taxonomy, 'cat_id' => $cat->term_id);
			} else if ($cat->taxonomy === 'post_tag') {
				$categories_link[] = array('link' => '<a href="'.get_term_link($cat->term_id, $cat->taxonomy).'">'.$cat->name.'</a>', 'tax' => $cat->taxonomy, 'cat_id' => $cat->term_id);
			}
		}
		return $categories_link;
	}

	public function getCategoriesCss($post_id) {
		$categories_css = '';
		$categories_name = array();
		$tag_name = array();
		$categories_id = array();
		$taxonomy_type = array();
		$args = array('orderby' => 'taxonomy', 'order' => 'DESC', 'fields' => 'all');
		$post_categories = wp_get_object_terms($post_id, $this->getTaxonomies(), $args);
		foreach ($post_categories as $cat) {
			if (is_taxonomy_hierarchical($cat->taxonomy) && substr( $cat->taxonomy, 0, 3 ) !== 'pa_') {
				if (!in_array($cat->term_id, $this->filter_categories)) {
					$this->filter_categories[] = $cat->term_id;
				}
				if ($cat->taxonomy !== 'post_tag') $categories_css.= ' grid-cat-' . $cat->term_id;
				$categories_name[] = $cat->name;
				$categories_id[] = $cat->term_id;
			} else if ($cat->taxonomy === 'post_tag') {
				$categories_id[] = $cat->term_id;
				$categories_name[] = $cat->name;
				$tag_name[] = $cat->name;
			}
			$taxonomy_type[] = $cat->taxonomy;
		}

		return array('cat_css' => $categories_css, 'cat_name' => $categories_name, 'cat_id' => $categories_id, 'tag' => $tag_name, 'taxonomy' => $taxonomy_type );
	}
	protected function resetTaxonomies()
	{
		$this->taxonomies = false;
	}
	protected function getTaxonomies()
	{
		if ($this
			->taxonomies === false)
		{
			$this
				->taxonomies = get_object_taxonomies(!empty($this
				->loop_args['post_type']) ? $this->loop_args['post_type'] : get_post_types(array(
				'public' => false,
				'name' => 'attachment'
			) , 'names', 'NOT'));
		}

		return $this->taxonomies;
	}

	protected function getFilterCategories()
	{
		return get_terms($this
			->getTaxonomies() , array(
			'orderby' => 'name',
			'include' => implode(',', $this->filter_categories)
		));
	}

	protected function getPostContent()
	{
		remove_filter('the_content', 'wpautop');
		$content = str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content()));
		return $content;
	}

	protected function getPostExcerpt()
	{
		remove_filter('the_excerpt', 'wpautop');
		$content = apply_filters('the_excerpt', get_the_excerpt());
		return $content;
	}

	public function buildWpQuery($query, $exclude_id = false, $offset = false)
	{
		$data = self::parseData($query);
		$query_builder = new UncodeLoopQueryBuilder( $data );
		if ($exclude_id){
			$query_builder->excludeId($exclude_id);
		}
		return $query_builder->build($offset);
	}
	public function vc_build_loop_query($query, $exclude_id = false, $offset = false)
	{
		return self::buildWpQuery($query, $exclude_id, $offset);
	}
	protected function parseData($value)
	{
		if (is_array($value)) return $value;
		$data = array();
		$values_pairs = preg_split('/\|/', $value);
		foreach ($values_pairs as $pair)
		{
			if (!empty($pair))
			{
				list($key, $value) = preg_split('/\:/', $pair);
				$data[$key] = $value;
			}
		}
		return $data;
	}
	protected function getLoop($loop,$offset = false)
	{
		$data = self::parseData($loop);
		foreach ($data as $key => $value)
		{
			$method = 'parse_' . $key;
			if (method_exists($this, $method))
			{
				$this->$method($value);
			}
		}

		$exclude_post = is_single() || is_page() ? get_the_ID() : '';
		list($this->loop_args, $this->query) = $this->vc_build_loop_query($loop, $exclude_post, $offset);
	}

}

require_once vc_path_dir('PARAMS_DIR', 'loop/loop.php');

class UncodeLoopQueryBuilder extends VcLoopQueryBuilder
{
	protected function parse_paged($value)
	{
		$this->args['paged'] = $value;
	}
	protected function parse_category($value)
	{
		global $wpdb;
		$term_query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->term_taxonomy WHERE term_id = %d",$value ));
		$term_type = $term_query->taxonomy;
		$this->args['tax_query'] = array(
			'relation' => 'AND'
		);
		$this->args['tax_query'][] = array(
			'field' => 'term_id',
			'taxonomy' => $term_type,
			'terms' => $value,
			'operator' => 'IN'
		);
	}

	protected function parse_tax_query( $value ) {
		$terms = $this->stringToArray( $value );
		if ( empty( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = array( 'relation' => 'AND' );
		}
		$negative_term_list = array();
		foreach ( $terms as $term ) {
			if ( (int) $term < 0 ) {
				$negative_term_list[] = abs( $term );
			}
		}

		$not_in = array();
		$in = array();

		$terms = get_terms( VcLoopSettings::getTaxonomies(),
			array( 'include' => array_map( 'abs', $terms ) ) );
		foreach ( $terms as $t ) {
			if ( in_array( (int) $t->term_id, $negative_term_list ) ) {
				$not_in[ $t->taxonomy ][] = $t->term_id;
			} else {
				$in[ $t->taxonomy ][] = $t->term_id;
			}
		}

		foreach ( $in as $taxonomy => $terms ) {
			$this->args['tax_query'][] = array(
				'field' => 'term_id',
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'operator' => 'IN',
			);
		}
		foreach ( $not_in as $taxonomy => $terms ) {
			$this->args['tax_query'][] = array(
				'field' => 'term_id',
				'taxonomy' => $taxonomy,
				'terms' => $terms,
				'operator' => 'NOT IN',
			);
		}
	}

	protected function parse_order_ids( $value ) {
	}

	protected function parse_search($value) {
		$this->args['s'] = $value;
	}

	protected function parse_year($value)
	{
		$this->args['year'] = $value;
	}

	protected function parse_month($value)
	{
		$this->args['monthnum'] = $value;
	}

	protected function parse_day($value)
	{
		$this->args['day'] = $value;
	}

	/**
	 * @return array
	 */
	public function build($offset = false) {
		if ($offset){
			$offset_args = $this->args;
			$offset_args['posts_per_page'] = $offset;
			$limit_posts = new WP_Query( $offset_args );
			$offset_array = array();
			foreach ($limit_posts->posts as $off_post) {
				$offset_array[] = $off_post->ID;
			}
			if (isset($this->args['post__not_in'])) {
				$this->args['post__not_in'] = array_merge($this->args['post__not_in'], $offset_array);
			} else $this->args['post__not_in'] = $offset_array;
		}

		return array( $this->args, new WP_Query( $this->args ) );
	}
}

?>