<?php

/**
 * uncode_create_single_block() is located in uncode/partials/elements.php
 * I needed to override that function for modifiy a filter of 'uncode_media_attribute_title' and 'uncode_media_attribute_excerpt'
 * If you meet some error after update uncode theme, check this file first
 */

function uncode_create_single_block($block_data, $el_id, $style_preset, $layout, $lightbox_classes, $carousel_textual, $with_html = true)
	{
		global $adaptive_images, $adaptive_images_async, $adaptive_images_async_blur;

		$image_orig_w = $image_orig_h = $crop = $item_media = $media_code = $media_mime = $create_link = $title_link = $text_content = $media_attributes = $big_image = $lightbox_data = $single_height = $single_fixed = $single_title = $nested = $media_poster = $dummy_oembed = $images_size = $single_family = $object_class = $single_back_color = $single_animation = $is_product = $single_icon = $single_text = $single_style = $single_elements_click = $overlay_color = $overlay_opacity = $tmb_data = $adaptive_async_class = $adaptive_async_data = '';
		$media_type = 'image';
		$multiple_items = false;

		if (isset($block_data['media_id'])) {
			$item_thumb_id = apply_filters('wpml_object_id', $block_data['media_id'], 'attachment');
			if ($item_thumb_id === '' || empty($item_thumb_id)) $item_thumb_id = $block_data['media_id'];
		}
		if (isset($block_data['classes'])) $block_classes = $block_data['classes'];
		if (isset($block_data['tmb_data'])) $tmb_data = $block_data['tmb_data'];
		if (isset($block_data['images_size'])) $images_size = $block_data['images_size'];
		if (isset($block_data['single_style'])) $single_style = $block_data['single_style'];
		if (isset($block_data['single_text'])) $single_text = $block_data['single_text'];
		if (isset($block_data['single_elements_click'])) $single_elements_click = $block_data['single_elements_click'];
		if (isset($block_data['overlay_color'])) $overlay_color = $block_data['overlay_color'];
		if (isset($block_data['overlay_opacity'])) $overlay_opacity = ((int) ($block_data['overlay_opacity'])) / 100;
		if (isset($block_data['single_width'])) $single_width = $block_data['single_width'];
		if (isset($block_data['single_height'])) $single_height = $block_data['single_height'];
		if (isset($block_data['single_back_color'])) $single_back_color = $block_data['single_back_color'];
		if (isset($block_data['single_title'])) $single_title = $block_data['single_title'];
		if (isset($block_data['single_icon'])) $single_icon = $block_data['single_icon'];
		if (isset($block_data['poster'])) $media_poster = $block_data['poster'];
		if (isset($block_data['title_classes'])) $title_classes = (!$block_data['title_classes']) ? array('h3') : $block_data['title_classes'];
		if (isset($block_data['animation'])) $single_animation = $block_data['animation'];
		if (isset($block_data['product']) && $block_data['product'] === true) $is_product = true;
		else $is_product = false;

		$single_fixed = (isset($block_data['single_fixed'])) ? $block_data['single_fixed'] : null;

		if (!isset($block_classes)) $block_classes = array();

		if (isset($block_data['link'])) {
			$create_link = is_array($block_data['link']) ? $block_data['link']['url'] : $block_data['link'];
			$title_link = $create_link;
		}

		$a_classes = array();
		if (isset($block_data['link_class'])) $a_classes[] = $block_data['link_class'];

		/*** MEDIA SECTION ***/
		if (isset($images_size) && $images_size !== '' && $style_preset !== 'metro')
		{
			switch ($images_size)
			{
				case ('one-one'):
					$single_height = $single_width;
					break;

				case ('ten-three'):
					$single_height = $single_width / (10 / 3);
					break;

				case ('four-three'):
					$single_height = $single_width / (4 / 3);
					break;

				case ('three-two'):
					$single_height = $single_width / (3 / 2);
					break;

				case ('two-one'):
					$single_height = $single_width / (2 / 1);
					break;

				case ('sixteen-nine'):
					$single_height = $single_width / (16 / 9);
					break;

				case ('twentyone-nine'):
					$single_height = $single_width / (21 / 9);
					break;

				case ('one-two'):
					$single_height = $single_width / (1 / 2);
					break;

				case ('three-four'):
					$single_height = $single_width / (3 / 4);
					break;

				case ('two-three'):
					$single_height = $single_width / (2 / 3);
					break;

				case ('nine-sixteen'):
					$single_height = $single_width / (9 / 16);
					break;

				case ('three-ten'):
					$single_height = $single_width / (3 / 10);
					break;
			}
			$block_classes[] = 'tmb-img-ratio';
		}

		$items_thumb_id = explode(',', $item_thumb_id);

		if ((empty($item_thumb_id) || FALSE === get_post_mime_type( $item_thumb_id )) && !is_array($items_thumb_id))
		{
			$media_attributes = '';
			if (isset($layout['media']) && isset($layout['media'][0]) && $layout['media'][0] === 'placeholder') {
				$item_media = wc_placeholder_img_src();
				$get_size_item_media = getimagesize($item_media);
				$image_orig_w = isset($get_size_item_media[0]) ? $get_size_item_media[0] : 500;
				$image_orig_h = isset($get_size_item_media[1]) ? $get_size_item_media[0] : 500;
			} else {
				$item_media = 'https://placeholdit.imgix.net/~text?txtsize=33&amp;txt=media+not+available&amp;w=500&amp;h=500';
				$image_orig_w = 500;
				$image_orig_h = 500;
			}
		}
		else
		{
			/** get media info **/
			if (count($items_thumb_id) > 1 ) {
				if ($media_poster) {
					$media_attributes = uncode_get_media_info($items_thumb_id[0]);
					$media_metavalues = unserialize($media_attributes->metadata);
					$media_mime = $media_attributes->post_mime_type;
				} else $multiple_items = true;
			} else {
				$media_attributes = uncode_get_media_info($item_thumb_id);
				if (!isset($media_attributes)) {
					$media_attributes = new stdClass();
					$media_attributes->metadata = '';
					$media_attributes->post_mime_type = '';
					$media_attributes->post_excerpt = '';
					$media_attributes->post_content = '';
					$media_attributes->guid = '';

					if ( isset($items_thumb_id[0]) && filter_var($items_thumb_id[0], FILTER_VALIDATE_EMAIL) )
						$media_attributes->guid = filter_var($items_thumb_id[0], FILTER_SANITIZE_EMAIL);
				}
				$media_metavalues = unserialize($media_attributes->metadata);
				$media_mime = $media_attributes->post_mime_type;
			}

			/** check if open to lightbox **/
			if ($lightbox_classes)
			{
				if (isset($lightbox_classes['data-title']) && $lightbox_classes['data-title'] === true) $lightbox_classes['data-title'] = apply_filters( 'uncode_media_attribute_title', $media_attributes->post_title, $items_thumb_id[0], $block_data['id']);
				if (isset($lightbox_classes['data-caption']) && $lightbox_classes['data-caption'] === true) $lightbox_classes['data-caption'] = apply_filters( 'uncode_media_attribute_excerpt', $media_attributes->post_excerpt, $items_thumb_id[0], $block_data['id']);
			}

			/** shortcode carousel  **/
			if ($multiple_items) {

				$shortcode = '[vc_gallery nested="yes" el_id="gallery-'.rand().'" medias="'.$item_thumb_id.'" type="carousel" style_preset="'.$style_preset.'" single_padding="0" thumb_size="'.$images_size.'" carousel_lg="1" carousel_md="1" carousel_sm="1" gutter_size="0" media_items="media" carousel_interval="0" carousel_dots="yes" carousel_dots_mobile="yes" carousel_autoh="yes" carousel_type="fade" carousel_nav="no" carousel_nav_mobile="no" carousel_dots_inside="yes" single_text="overlay" single_border="yes" single_width="'.$single_width.'" single_height="'.$single_height.'" single_text_visible="no" single_text_anim="no" single_overlay_visible="no" single_overlay_anim="no" single_image_anim="no"]';

				$media_oembed = uncode_get_oembed($item_thumb_id, $shortcode, 'shortcode', false);
				$media_code = $media_oembed['code'];
				$media_type = $media_oembed['type'];
				if(($key = array_search('tmb-overlay-anim', $block_classes)) !== false) {
				    unset($block_classes[$key]);
				}
				if(($key = array_search('tmb-overlay-text-anim', $block_classes)) !== false) {
				    unset($block_classes[$key]);
				}
				if(($key = array_search('tmb-image-anim', $block_classes)) !== false) {
				    unset($block_classes[$key]);
				}
				$image_orig_w = $single_width;
				$image_orig_h = $single_height;
				$object_class = 'nested-carousel object-size';
			} else {
				/** This is a self-hosted image **/
				if ($media_mime !== 'image/svg+xml' && strpos($media_mime, 'image/') !== false && $media_mime !== 'image/url' && isset($media_metavalues['width']) && isset($media_metavalues['height']))
				{

					$image_orig_w = $media_metavalues['width'];
					$image_orig_h = $media_metavalues['height'];

					/** check if open to lightbox **/
					if ($lightbox_classes)
					{
						global $adaptive_images, $adaptive_images_async;
						if ($adaptive_images === 'on' && $adaptive_images_async === 'on') {
							$create_link = (is_array($media_attributes->guid) ? $media_attributes->guid['url'] : $media_attributes->guid);
						} else {
							$big_image = uncode_resize_image($media_attributes->id, (is_array($media_attributes->guid) ? $media_attributes->guid['url'] : $media_attributes->guid), $media_attributes->path, $image_orig_w, $image_orig_h, 12, null, false);
							$create_link = $big_image['url'];
						}
						$create_link = strtok($create_link, '?');
					}

					/** calculate height ratio if masonry and thumb size **/
					if ($style_preset === 'masonry')
					{
						if ($images_size !== '')
						{
							$crop = true;
						}
						else
						{
							$crop = false;
						}
					}
					else $crop = true;

					if ($media_mime === 'image/gif' || $media_mime === 'image/url') {
						$resized_image = array(
							'url' => $media_attributes->guid,
							'width' => $image_orig_w,
							'height' => $image_orig_h,
						);
					} else {
						if ( isset( $block_data['justify_row_height'] ) ) { //if Justified-Gallery is the case
							$single_width = '';
							$single_height = $block_data['justify_row_height'];
							$img_ratio = $image_orig_w/$image_orig_h;
							if ( $img_ratio < 1 ) { //portrait orientation
								$single_height = $single_height * 2;
							}
						} elseif ( isset( $block_data['fluid_height'] ) ) { //if fluid height (carousel or metro)
							$single_width = '';
							$single_height = $block_data['fluid_height'];
							$crop = false;
						}
						$resized_image = uncode_resize_image($media_attributes->id, $media_attributes->guid, $media_attributes->path, $image_orig_w, $image_orig_h, $single_width, $single_height, $crop, $single_fixed);
					}
					$item_media = esc_attr($resized_image['url']);
					if (strpos($media_mime, 'image/') !== false && $media_mime !== 'image/gif' && $media_mime !== 'image/url' && $adaptive_images === 'on' && $adaptive_images_async === 'on') {
						$adaptive_async_class = ' adaptive-async';
						if ($adaptive_images_async_blur === 'on') $adaptive_async_class .= ' async-blurred';
						$adaptive_async_data = ' data-uniqueid="'.$item_thumb_id.'-'.big_rand().'" data-guid="'.(is_array($media_attributes->guid) ? $media_attributes->guid['url'] : $media_attributes->guid).'" data-path="'.$media_attributes->path.'" data-width="'.$image_orig_w.'" data-height="'.$image_orig_h.'" data-singlew="'.$single_width.'" data-singleh="'.$single_height.'" data-crop="'.$crop.'" data-fixed="'.$single_fixed.'"';
					}
					$image_orig_w = $resized_image['width'];
					$image_orig_h = $resized_image['height'];
				} else if ($media_mime === 'oembed/svg') {
					$media_type = 'html';
					$media_code = $media_attributes->post_content;
					if ($media_mime === 'oembed/svg') {
						$media_code = preg_replace('#\s(id)="([^"]+)"#', ' $1="$2-' .big_rand() .'"', $media_code);
						$media_code = preg_replace('#\s(xmlns)="([^"]+)"#', '', $media_code);
						$media_code = preg_replace('#\s(xmlns:svg)="([^"]+)"#', '', $media_code);
						$media_code = preg_replace('#\s(xmlns:xlink)="([^"]+)"#', '', $media_code);
						if (isset($media_metavalues['width']) && $media_metavalues['width'] !== '1') $icon_width = ' style="width:'.$media_metavalues['width'].'px"';
						else $icon_width = ' style="width:100%"';
						$media_code = '<div'.$icon_width.' class="icon-media">'.$media_code.'</div>';
						if ($media_attributes->animated_svg) {
							$media_metavalues = unserialize($media_attributes->metadata);
							$icon_time = (isset($media_attributes->animated_svg_time) && $media_attributes->animated_svg_time !== '') ? $media_attributes->animated_svg_time : 100;
							preg_match('/(id)=("[^"]*")/i', $media_code, $id_attr);
							if (isset($id_attr[2])) {
								$id_icon = str_replace('"', '', $id_attr[2]);
							} else {
								$id_icon = 'icon-' . big_rand();
								$media_code = preg_replace('/<svg/', '<svg id="' . $id_icon . '"', $media_code);
							}
							if (isset($block_data['delay']) && $block_data['delay'] !== '') $icon_delay = 'delayStart: '.$block_data['delay'].', ';
							else $icon_delay = '';
							$media_code .= "<script type='text/javascript'>new Vivus('".$id_icon."', {type: 'delayed', pathTimingFunction: Vivus.EASE_OUT, animTimingFunction: Vivus.LINEAR, ".$icon_delay."duration: ".$icon_time."});</script>";
						}
					}
				} else if ($media_mime === 'image/svg+xml') {
					$media_type = 'other';
					$media_code = $media_attributes->guid;
					$image_orig_w = $media_metavalues['width'];
					$image_orig_h = $media_metavalues['height'];
					if (isset($media_metavalues['width']) && $media_metavalues['width'] !== '1') $icon_width = ' style="width:'.$media_metavalues['width'].'px"';
					else $icon_width = ' style="width:100%"';
					$id_icon = 'icon-' . big_rand();
					if ($media_attributes->animated_svg) {
						$media_metavalues = unserialize($media_attributes->metadata);
						$icon_time = (isset($media_attributes->animated_svg_time) && $media_attributes->animated_svg_time !== '') ? $media_attributes->animated_svg_time : 100;
						$media_code = '<div id="'.$id_icon.'"'.$icon_width.' class="icon-media"></div>';
						if (isset($block_data['delay']) && $block_data['delay'] !== '') $icon_delay = 'delayStart: '.$block_data['delay'].', ';
						else $icon_delay = '';
						$media_code .= "<script type='text/javascript'>new Vivus('".$id_icon."', {type: 'delayed', pathTimingFunction: Vivus.EASE_OUT, animTimingFunction: Vivus.LINEAR, ".$icon_delay."duration: ".$icon_time.", file: '".$media_attributes->guid."'});</script>";
					} else {
						$media_code = '<div id="'.$id_icon.'"'.$icon_width.' class="icon-media"><img src="'.$media_code.'" /></div>';
					}
				}
				/** This is an oembed **/
				else
				{
					$object_class = 'object-size';
					/** external image **/
					if ($media_mime === 'image/gif' || $media_mime === 'image/url')
					{
						$item_media = $media_attributes->guid;
						$image_orig_w = $media_metavalues['width'];
						$image_orig_h = $media_metavalues['height'];
						if ($lightbox_classes) $create_link = $item_media;
					}
					/** any other oembed **/
					else
					{
						$media_oembed = uncode_get_oembed($item_thumb_id, $media_attributes->guid, $media_attributes->post_mime_type, $media_poster, $media_attributes->post_excerpt, $media_attributes->post_content);
						/** check if is an image oembed  **/
						if ($media_oembed['type'] === 'image')
						{
							$item_media = esc_attr($media_oembed['code']);
							$image_orig_w = $media_oembed['width'];
							$image_orig_h = $media_oembed['height'];
							$media_type = 'image';
							if ($lightbox_classes) $create_link = $media_oembed['code'];
						}
						else
						{
							/** check if there is a poster  **/
							if (isset($media_oembed['poster']) && $media_oembed['poster'] !== '' && $media_poster)
							{
								/** calculate height ratio if masonry and thumb size **/
								if ($style_preset === 'masonry')
								{
									if ($images_size !== '')
									{
										$crop = true;
									}
									else
									{
										$crop = false;
									}
								}
								else $crop = true;

								if (!empty($media_oembed['poster']) && $media_oembed['poster'] !== '') {
									$poster_attributes = uncode_get_media_info($media_oembed['poster']);
									$media_metavalues = unserialize($poster_attributes->metadata);
									$image_orig_w = $media_metavalues['width'];
									$image_orig_h = $media_metavalues['height'];
									$resized_image = uncode_resize_image($poster_attributes->id, $poster_attributes->guid, $poster_attributes->path, $image_orig_w, $image_orig_h, $single_width, $single_height, $crop);
									$item_media = esc_attr($resized_image['url']);
									if (strpos($poster_attributes->post_mime_type, 'image/') !== false && $poster_attributes->post_mime_type !== 'image/gif' && $poster_attributes->post_mime_type !== 'image/url' && $adaptive_images === 'on' && $adaptive_images_async === 'on') {
										$adaptive_async_class = ' adaptive-async';
										if ($adaptive_images_async_blur === 'on') $adaptive_async_class .= ' async-blurred';
										$adaptive_async_data = ' data-uniqueid="'.$item_thumb_id.'-'.big_rand().'" data-guid="'.(is_array($poster_attributes->guid) ? $poster_attributes->guid['url'] : $poster_attributes->guid).'" data-path="'.$poster_attributes->path.'" data-width="'.$image_orig_w.'" data-height="'.$image_orig_h.'" data-singlew="'.$single_width.'" data-singleh="'.$single_height.'" data-crop="'.$crop.'"';
									}
									$image_orig_w = $resized_image['width'];
									$image_orig_h = $resized_image['height'];
									$media_type = 'image';
									if ($lightbox_classes) {
										switch ($media_attributes->post_mime_type) {
					    				case 'oembed/twitter':
					    				case 'oembed/html':
					    					global $adaptive_images, $adaptive_images_async;
												if ($adaptive_images === 'on' && $adaptive_images_async === 'on') {
													$poster_url = $poster_attributes->guid;
												} else {
													$big_image = uncode_resize_image($poster_attributes->id, $poster_attributes->guid, $poster_attributes->path, $image_orig_w, $image_orig_h, 12, null, false);
													$create_link = $big_image['url'];
												}
						    			break;
						    			case 'oembed/iframe':
						    				$create_link = '#inline-'.$item_thumb_id.'" data-type="inline" target="#inline' . $item_thumb_id;
						    			break;
						    			default;
						    				$create_link = $media_oembed['code'];
						    			break;
						    		}
									}
								}
							}
							else
							{
								$media_code = $media_oembed['code'];
								$media_type = $media_oembed['type'];
								$object_class = $media_oembed['class'];
								if ($style_preset === 'metro' || $images_size != '')
								{
									$image_orig_w = $single_width;
									$image_orig_h = $single_height;
								}
								else
								{
									$image_orig_w = $media_oembed['width'];
									$image_orig_h = $media_oembed['height'];
								}

								if (strpos($media_mime,'audio/') !== false && isset($media_oembed['poster']) && $media_oembed['poster'] !== '') {
			      			$poster_attributes = uncode_get_media_info($media_oembed['poster']);
					    		$media_metavalues = unserialize($poster_attributes->metadata);
					    		$image_orig_w = $media_metavalues['width'];
									$image_orig_h = $media_metavalues['height'];
					    		$resized_image = uncode_resize_image($poster_attributes->id, $poster_attributes->guid, $poster_attributes->path, $image_orig_w, $image_orig_h, $single_width, $single_height, $crop);
					    		$media_oembed['dummy'] = ($image_orig_h / $image_orig_w) * 100;
			      		}

			      		/** This is an iframe **/
								if ($media_mime === 'oembed/iframe') {
									$media_type = 'other';
									$media_code = $media_attributes->post_content;
									$image_orig_w = $media_metavalues['width'];
									$image_orig_h = $media_metavalues['height'];
								}

								if ($image_orig_h === 0) $image_orig_h = 1;

								if ($media_oembed['dummy'] !== 0 && $style_preset !== 'metro') $dummy_oembed = ' style="padding-top: ' . $media_oembed['dummy'] . '%"';
								if ($lightbox_classes && $media_type === 'image') $create_link = $media_oembed['code'];
							}
						}
					}
				}
			}
		}

		$media_alt = (isset($media_attributes->alt)) ? $media_attributes->alt : '';

		if ($item_media === '' && !isset($media_attributes->guid) && !$multiple_items)
		{
			$media_type = 'image';
			$item_media = 'http://placehold.it/500&amp;text=media+not+available';
			$image_orig_w = 500;
			$image_orig_h = 500;
		}

		if (!$with_html) {
			return array(
				'code' => (($media_type === 'image') ? esc_url($item_media) : $media_code),
				'type' => $media_type,
				'width' => $image_orig_w,
				'height' => $image_orig_h,
				'alt' => $media_alt,
				'async' => ($adaptive_async_data === '' ? false : array('class'=>$adaptive_async_class,'data'=>$adaptive_async_data))
			);
		}

		$entry = $inner_entry = '';

		foreach ($layout as $key => $value)
		{
			switch ($key)
			{
				case 'icon':
					if ($single_icon !== '' && $single_text === 'overlay') $inner_entry.= '<i class="' . $single_icon . ' t-overlay-icon"></i>';
				break;

				case 'title':
					$get_title = (isset($media_attributes->post_title)) ? $media_attributes->post_title : '';
					if (($single_text === 'overlay' && $single_elements_click !== 'yes') || (isset($media_attributes->team) && $media_attributes->team) || $title_link === '#') {
						$print_title = $single_title ? $single_title : $media_attributes->post_title;
						if ($print_title !== '') $inner_entry .= '<h3 class="t-entry-title '. trim(implode(' ', $title_classes)) . '">'.$print_title.'</h3>';
					} else {
						$print_title = $single_title ? $single_title : $get_title;
						if ($print_title !== '') {
							if ($title_link === '') $inner_entry .= '<h3 class="t-entry-title '. trim(implode(' ', $title_classes)) . '">'.$print_title.'</h3>';
							else $inner_entry .= '<h3 class="t-entry-title '. trim(implode(' ', $title_classes)) . '"><a href="'.$title_link.'">'.$print_title.'</a></h3>';
						}
					}
				break;

				case 'type':
					$get_the_post_type = get_post_type($block_data['id']);
					if ($get_the_post_type === 'portfolio') {
						$portfolio_cpt_name = ot_get_option('_uncode_portfolio_cpt');
						if ($portfolio_cpt_name !== '') $get_the_post_type = $portfolio_cpt_name;
					}
					$get_the_post_type = ucfirst($get_the_post_type);
					$inner_entry .= '<p class="t-entry-type"><span>' . $get_the_post_type . '</span></p>';
				break;

				case 'category':
				case 'meta':

					if (isset($value[0]) && $value[0] === 'yesbg') $with_bg = true;
					else $with_bg = false;

					$meta_inner = '';

					if (is_sticky()) {
						$meta_inner .= '<span class="t-entry-category"><i class="fa fa-ribbon fa-push-right"></i>' . esc_html__('Sticky','uncode').'</span><span class="small-spacer"></span>';
					}

					if ($key === 'meta') {
						$year = get_the_time( 'Y' );
						$month = get_the_time( 'm' );
						$day = get_the_time( 'd' );
						$date = get_the_date( '', $block_data['id'] );
						$meta_inner .= '<span class="t-entry-category"><i class="fa fa-clock fa-push-right"></i><a href="' . get_day_link( $year, $month, $day ) .'">'.$date.'</a></span><span class="small-spacer"></span>';
					}

					$categories_array = isset($block_data['single_categories_id']) ? $block_data['single_categories_id'] : array();

					$cat_icon = false;
					$tag_icon = false;

					$cat_count = count($categories_array);
					$cat_counter = 0;
					$only_cat_counter = 0;

					if ($cat_count === 0) continue;

					$first_taxonomy = isset($block_data['single_categories'][0]['tax']) ? $block_data['single_categories'][0]['tax'] : '';

					foreach ($block_data['single_categories'] as $cat_key => $cat) {
						if (isset($cat['tax']) && $cat['tax'] === $first_taxonomy) $only_cat_counter++;
					}

					foreach ($categories_array as $t_key => $tax) {
						$category = $term_color = '';

						if ($t_key !== $cat_count - 1 && $t_key !== $only_cat_counter - 1) $add_comma = true;
						else $add_comma = false;

						$cat_classes = 't-entry-category';
						if (isset($block_data['single_categories'][$t_key])) {
							$single_cat = $block_data['single_categories'][$t_key];
							if (gettype($single_cat) !== 'string' && isset($single_cat['link'])) {
								if ($key === 'category' && $block_data['single_categories'][$t_key]['tax'] === 'post_tag') continue;
								$cat_link = $block_data['single_categories'][$t_key]['link'];

								if ($block_data['single_categories'][$t_key]['tax'] === 'category' && !$cat_icon) {
									$category .= '<i class="fa fa-archive2 fa-push-right"></i>';
									$cat_icon = true;
								}
								if ($block_data['single_categories'][$t_key]['tax'] === 'post_tag' && !$tag_icon) {
									$category .= '<i class="fa fa-tag2 fa-push-right"></i>';
									$tag_icon = true;
								}
							} else {
								$cat_link = $block_data['single_categories'][$t_key];
								if (isset($block_data['single_tags']) && $key === 'category' && ( isset($block_data['taxonomy_type']) && isset($block_data['taxonomy_type'][$t_key]) && $block_data['taxonomy_type'][$t_key] !== 'category' && $block_data['taxonomy_type'][$t_key] !== 'portfolio_category' ) ) continue;
								if (isset($block_data['single_tags']) && $key === 'post_tag' && ( isset($block_data['taxonomy_type']) && isset($block_data['taxonomy_type'][$t_key]) && $block_data['taxonomy_type'][$t_key] !== 'post_tag' ) ) continue;
							}

							if (isset($block_data['single_categories'][$t_key]['cat_id'])) {
								$term_color = get_option( '_uncode_taxonomy_' . $block_data['single_categories'][$t_key]['cat_id'] );
								if (isset($term_color['term_color']) && $term_color['term_color'] !== '' && $with_bg) {
									$term_color = 'text-' . $term_color['term_color'] . '-color';
									$cat_link = str_replace('<a ', '<a class="'.$term_color.'" ', $cat_link);
								}
							}

							$category .= $cat_link . ($add_comma ? '<span class="cat-comma">,</span>' : '<span class="small-spacer"></span>');
							$add_comma = true;
						} else $category = '';
						$meta_inner .= '<span class="'.$cat_classes.'">'.$category.'</span>';
						$cat_counter++;
						$category = '';
					}

					if ($meta_inner !== '') {
						$inner_entry .= '<p class="t-entry-meta">';
						$inner_entry .= $meta_inner;
						$inner_entry .= '</p>';
					}

				break;

				case 'date':
					$date = get_the_date( '', $block_data['id'] );
					$inner_entry .= '<p class="t-entry-meta">';
					$inner_entry .= '<span class="t-entry-date">'.$date.'</span>';
					$inner_entry .= '</p>';
				break;

				case 'text':

					$post_format = get_post_format($block_data['id']);
					if (isset($value[0]) && ($value[0] === 'full')) {
						$block_text = (($post_format === 'link') ? '<i class="fa fa-link fa-push-right"></i>' : '') . $block_data['content'];
						$block_text .= wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'uncode' ),
							'after'  => '</div>',
							'link_before'	=> '<span>',
	    				'link_after'	=> '</span>',
							'echo'	=> 0
						));
					} else $block_text = get_post_field( 'post_excerpt', $block_data['id'] );

					if (function_exists('qtranxf_getLanguage')) $block_text = __($block_text);
					$block_text = uncode_remove_wpautop($block_text, true);

					$text_class = (isset($block_data['text_lead']) && ($block_data['text_lead'] === 'yes')) ? ' class="text-lead"' : '';

					$block_text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $block_text);
					if (isset($block_data['text_length']) && $block_data['text_length'] !== '') {
						$block_text = preg_replace('#<a class="more-link(.*?)</a>#', '', $block_text);
						$block_text = '<p'.$text_class.'>' . uncode_truncate($block_text, $block_data['text_length']) . '</p>';
					} else if (isset($value[1]) && !empty($value[1])) {
						$block_text = preg_replace('#<a class="more-link(.*?)</a>#', '', $block_text);
						$block_text = '<p'.$text_class.'>' . uncode_truncate($block_text, $value[1]) . '</p>';
					}


					if ($block_text !== '') {
						if ($text_class !== '') $text_class = ' text-lead';
						if ($single_text === 'overlay' && $single_elements_click !== 'yes') $inner_entry .= '<div class="t-entry-excerpt'.$text_class.'">'.preg_replace('/<\/?a(.|\s)*?>/', '', $block_text).'</div>';
						else {
							if (isset($value[0]) && ($value[0] === 'full')) $inner_entry .= $block_text;
							else $inner_entry .='<div class="t-entry-excerpt'.$text_class.'">'.$block_text.'</div>';
						}
					}

				break;

				case 'link':
					$btn_shape = ' btn-default';
					if (isset($value[0]) && $value[0] !== 'default') {
						if ($value[0] === 'link') $btn_shape = ' btn-link';
						else $btn_shape = ' btn-default btn-' . $value[0];
					}
					if ($single_text === 'overlay' && $single_elements_click !== 'yes') $inner_entry .= '<p class="t-entry-readmore"><span class="btn'.$btn_shape.'">' . esc_html__('Read More','uncode').' </span></p>';
					else $inner_entry .= '<p class="t-entry-readmore"><a href="'.$create_link.'" class="btn'.$btn_shape.'">' . esc_html__('Read More','uncode').' </a></p>';
				break;

				case 'author':
					$author = get_post_field( 'post_author', $block_data['id'] );
					$author_name = get_the_author_meta( 'display_name', $author );
					$author_link = get_author_posts_url( $author );
					$inner_entry .= '<p class="t-entry-author">';
					if ($single_text === 'overlay' && $single_elements_click !== 'yes') $inner_entry .= get_avatar( $author, 80 ). '<span>' . esc_html__('by','uncode') . ' ' . $author_name . '</span>';
					else $inner_entry .= '<a href="'.$author_link.'">'.get_avatar( $author, 80 ). '<span>' . esc_html__('by','uncode') . ' ' . $author_name.'</span></a>';
					$inner_entry .= '</p>';
				break;

				case 'extra':
					$inner_entry .= '<p class="t-entry-comments entry-small"><span class="extras">';

					if( function_exists('dot_irecommendthis')) {
						global $uncode_irecommendthis;
						if ($single_text === 'under') {
							$inner_entry .= $uncode_irecommendthis->dot_recommend($block_data['id'], true);
						} else {
							if ($single_elements_click === 'yes') {
								$inner_entry .= $uncode_irecommendthis->dot_recommend($block_data['id'], true);
							} else {
								$inner_entry .= $uncode_irecommendthis->dot_recommend($block_data['id'], false);
							}
						}
					}

					$num_comments = get_comments_number( $block_data['id'] );
					$entry_comments = '<i class="fa fa-speech-bubble"></i><span>'.$num_comments.' '._nx( 'Comment', 'Comments', $num_comments, 'comments', 'uncode' ).'</span>';
					if ($single_text === 'overlay' && $single_elements_click !== 'yes') $inner_entry .= '<span class="extras-wrap">' . $entry_comments . '</span>';
					else $inner_entry .= '<a class="extras-wrap" href="'.get_comments_link($block_data['id']).'" title="title">'.$entry_comments.'</a>';
					$inner_entry .= '<span class="extras-wrap"><i class="fa fa-watch"></i><span>'.uncode_estimated_reading_time($block_data['id']).'</span></span></span></p>';
				break;

				case 'price':
					if ( class_exists( 'WooCommerce' ) ) {
						$WC_Product = wc_get_product( $block_data['id'] );
						$inner_entry .= '<span class="price '.trim(implode(' ', $title_classes)).'">'.$WC_Product->get_price_html().'</span>';
					}
				break;

				case 'caption':
					if (isset($media_attributes->post_excerpt) && $media_attributes->post_excerpt !== '') $inner_entry.= '<p class="t-entry-meta"><span>' . $media_attributes->post_excerpt . '</span></p>';
				break;

				case 'description':
					if (isset($media_attributes->post_content) && $media_attributes->post_content !== '') $inner_entry.= '<p class="t-entry-excerpt">' . $media_attributes->post_content . '</p>';
				break;

				case 'team-social':
					if ($media_attributes->team) {
						$team_socials = explode("\n", $media_attributes->team_social);
						$inner_entry .= '<p class="t-entry-comments t-entry-member-social"><span class="extras">';
						$host = str_replace("www.", "", $_SERVER['HTTP_HOST']);
						$host = explode('.', $host);
						$local_host = $host[0].$host[1];
						foreach ($team_socials as $key => $value) {
							$value = trim($value);
							if ($value !== '') {
								if(filter_var($value, FILTER_VALIDATE_EMAIL)) {
									$inner_entry .= '<a href="mailto:'.$value.'"><i class="fa fa-envelope-o"></i></a>';
								} else {
									$get_host = parse_url($value);
									$host = str_replace("www.", "", $get_host['host']);
									$host = explode('.', $host);
									if ($local_host === $host[0].$host[1]) {
										$inner_entry.= '<a href="'.esc_url($value).'"><i class="fa fa-user"></i></a>';
									} else {
										if ($host[0] === 'plus') $host[0] = 'google-' . $host[0];
										$inner_entry.= '<a href="'.esc_url($value).'" target="_blank"><i class="fa fa-'.esc_attr($host[0]).'"></i></a>';
									}
								}
							}
						}
						$inner_entry .= '</span></p>';
					}
				break;

				case 'spacer':
					if (isset($value[0])) {
						switch ($value[0]) {
							case 'half':
								$spacer_class = 'half-space';
								break;
							case 'one':
								$spacer_class = 'single-space';
								break;
							case 'two':
								$spacer_class = 'double-space';
								break;
						}
						$inner_entry.= '<div class="spacer '.$spacer_class.'"></div>';
					}
				break;

				case 'sep-one':
				case 'sep-two':
					$sep_class = (isset($value[0]) && $value[0] === 'reduced') ? ' class="separator-reduced"' : '';
					$inner_entry.= '<hr'.$sep_class.' />';
				break;

				default:
					if ($key !== 'media') {
						$get_cf_value = get_post_meta($block_data['id'], $key, true);
						if (isset($get_cf_value) && $get_cf_value !== '') $inner_entry.= '<div class="t-entry-cf-'.$key.'">' . $get_cf_value . '</div>';
					}
				break;
			}
		}

		if (isset($media_attributes->team) && $media_attributes->team) $single_elements_click = 'yes';

		if (!empty($layout) && !(count($layout) === 1 && array_key_exists('media',$layout)) && $inner_entry !== '')
		{

			if ($single_text === 'under')
			{
				$entry.= '<div class="t-entry-text">
							<div class="t-entry-text-tc '.$block_data['text_padding'].'">';
			}

			$entry.= '<div class="t-entry">';

			$entry .= $inner_entry;

			$entry.= '</div>';

			if ($single_text === 'under')
			{

				$entry.= '</div>
					</div>';
			}
		}

		if ($lightbox_classes) {
			$div_data_attributes = array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $lightbox_classes, array_keys($lightbox_classes));
			$lightbox_data = ' ' . implode(' ', $div_data_attributes);
			$lightbox_data .= ' data-lbox="ilightbox_' . $el_id . '"';
			$video_src = '';
			if (isset($media_attributes->post_mime_type) && strpos($media_attributes->post_mime_type, 'video/') !== false) {
				$video_src .= 'html5video:{preload:\'true\',';
				$video_autoplay = get_post_meta($item_thumb_id, "_uncode_video_autoplay", true);
				if ($video_autoplay) $video_src .= 'autoplay:\'true\',';
				$alt_videos = get_post_meta($item_thumb_id, "_uncode_video_alternative", true);
				if (!empty($alt_videos)) {
					foreach ($alt_videos as $key => $value) {
						$exloded_url = explode(".", strtolower($value));
						$ext = end($exloded_url);
						if ($ext !== '') $video_src .= $ext . ":'" . $value."',";
					}
				}
				$video_src .= '},';
			}


			if (isset($media_attributes->metadata)) {

				if (isset($media_metavalues['width']) && isset($media_metavalues['height'])) {
					$media_dimensions = 'width:' . $media_metavalues['width'] . ',';
					$media_dimensions .= 'height:' . $media_metavalues['height'] . ',';
				} else $media_dimensions = '';


				$lightbox_data .= ' data-options="'.$media_dimensions.$video_src.'thumbnail: \''.$item_media.'\'"';
			}
		}

		$layoutArray = array_keys($layout);
		foreach ($layoutArray as $key => $value) {
			if ($value === 'icon') unset($layoutArray[$key]);
		}

		if (!array_key_exists('media',$layout)) {
			$block_classes[] = 'tmb-only-text';
			$with_media = false;
		} else {
			$with_media = true;
		}

		if ($single_text === 'overlay') {
			if ($with_media) {
				$block_classes[] = 'tmb-media-first';
				$block_classes[] = 'tmb-media-last';
			}
			$block_classes[] = 'tmb-content-overlay';
		} else {
			$block_classes[] = 'tmb-content-under';
			$layoutLast = (string) array_pop($layoutArray);

			if ($with_media) {
				if (($layoutLast === 'media' || $layoutLast === '') && $with_media) $block_classes[] = 'tmb-media-last';
				else $block_classes[] = 'tmb-media-first';
			}
		}

		if ($single_back_color === '') {
			$block_classes[] = 'tmb-no-bg';
		} else {
			$single_back_color = ' style-' . $single_back_color . '-bg';
		}

		$div_data_attributes = array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $tmb_data, array_keys($tmb_data));

		$output = 	'<div class="'.implode(' ', $block_classes).'">
						<div class="' . (($nested !== 'yes') ? 't-inside' : '').$single_back_color . $single_animation . '" '.implode(' ', $div_data_attributes) .'>';

		if ($single_text === 'under' && $layoutLast === 'media') {
			$output .= $entry;
		}

		if (array_key_exists('media',$layout) || $single_text === 'overlay') :
			$output .= 		'<div class="t-entry-visual" tabindex="0"><div class="t-entry-visual-tc"><div class="t-entry-visual-cont">';

			if ($style_preset === 'masonry' && ($images_size !== '' || ($single_text === 'under' || $single_elements_click !== 'yes')) && array_key_exists('media',$layout)):

				if ( ( $media_type === 'image' || $media_type === 'email' ) && $image_orig_w != 0 && $image_orig_h != 0) :
					$dummy_padding = round(($image_orig_h / $image_orig_w) * 100, 2);
					$output .= 			'<div class="dummy" style="padding-top: '.$dummy_padding.'%;"></div>';

				endif;

			endif;

			if (($single_text === 'under' || $single_elements_click !== 'yes') && $media_type === 'image'):

				if ($style_preset === 'masonry') $a_classes[] = 'pushed';

				$data_values = (isset($block_data['link']['target']) && !empty($block_data['link']['target']) && is_array($block_data['link'])) ? ' target="'.trim($block_data['link']['target']).'"' : '';

				$output .= '<a tabindex="-1" href="'. (($media_type === 'image') ? $create_link : '').'"'.((count($a_classes) > 0 ) ? ' class="'.trim(implode(' ', $a_classes)).'"' : '').$lightbox_data.$data_values.'>';

			endif;

			if (is_object($media_attributes) && $media_attributes->post_mime_type !== 'oembed/facebook' && $media_attributes->post_mime_type !== 'oembed/twitter') :

			$output .= 	'<div class="t-entry-visual-overlay"><div class="t-entry-visual-overlay-in '.$overlay_color.'" style="opacity: '.$overlay_opacity.';"></div></div>
									<div class="t-overlay-wrap">
										<div class="t-overlay-inner">
											<div class="t-overlay-content">
												<div class="t-overlay-text '.$block_data['text_padding'].'">';

			if ($single_text === 'overlay'):

				$output .= $entry;

			else:

				$output .=					'<div class="t-entry t-single-line">';

				if (array_key_exists('icon',$layout)) :

					if ($single_icon !== '') :

						$output .= 				'<i class="'.$single_icon.' t-overlay-icon"></i>';

					endif;

				endif;

				$output .= 						'</div>';

			endif;

			$output .= 						'</div></div></div></div>';

			endif;

			if (array_key_exists('media',$layout)) :

				if (isset($layout['media'][3]) && $layout['media'][3] === 'show-sale') {
					global $woocommerce;
					if ( class_exists( 'WooCommerce' ) ) {
						if (isset($block_data['id'])) {
							$product = wc_get_product($block_data['id']);
							if ( $product->is_on_sale() ) {
								$output .= '<div class="woocommerce"><span class="onsale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</span></div>';
							} else if ( ! $product->is_in_stock() ) {
								$output .= '<div class="woocommerce"><span class="soldout">' . esc_html__( 'Sold Out', 'woocommerce' ) . '</span></div>';
							}
						}
					}
				}

				if ($style_preset === 'metro'):

					if ($single_elements_click === 'yes' && $media_type === 'image'):

						$a_classes[] = 't-background-click';

						$data_values = !empty($block_data['link']['target']) ? ' target="'.trim($block_data['link']['target']).'"' : '';

						$output .= 			'<a href="'. (($media_type === 'image') ? $create_link : '').'"'.((count($a_classes) > 0 ) ? ' class="'.trim(implode(' ', $a_classes)).'"' : '').$lightbox_data.$data_values.'>
												<div class="t-background-cover'.($adaptive_async_class !== '' ? $adaptive_async_class : '').'" style="background-image:url(\''.$item_media.'\')"'.($adaptive_async_data !== '' ? $adaptive_async_data : '').'></div>
											</a>';

					else:

						if ($media_type === 'image') :

							$output .= 		'<div class="t-background-cover'.($adaptive_async_class !== '' ? $adaptive_async_class : '').'" style="background-image:url(\''.$item_media.'\')"'.($adaptive_async_data !== '' ? $adaptive_async_data : '').'></div>';

						else:

							$output .= 		'<div class="fluid-object '. trim(implode(' ', $title_classes)) . ' '.$object_class.'"'.$dummy_oembed.'>'.$media_code.'</div>';

						endif;

					endif;

				else:

					if ($media_type === 'image') :

						$output .= '<img'.($adaptive_async_class !== '' ? ' class="'.trim($adaptive_async_class).'"' : '').' src="'.$item_media.'" width="'.$image_orig_w.'" height="'.$image_orig_h.'" alt="'.$media_alt.'"'.($adaptive_async_data !== '' ? $adaptive_async_data : '').' />';

					elseif ($media_type === 'email') :

						$output .= get_avatar($media_attributes->guid, $image_orig_w);

					else:
						if ($object_class !== '') $title_classes[] = $object_class;
						if (isset($media_attributes->post_mime_type)) {
							switch ($media_attributes->post_mime_type) {
								case 'oembed/svg':
								case 'image/svg+xml':
									$title_classes = array('fluid-svg');
								break;
								case 'oembed/facebook':
								case 'oembed/twitter':
									$title_classes[] = 'social-object';
									if ($media_attributes->post_mime_type === 'oembed/facebook') $title_classes[] = 'facebook-object';
									else {
										if ($media_attributes->social_original) $title_classes[] = 'twitter-object';
										else $title_classes[] = 'fluid-object';
									}
									$dummy_oembed = '';
								break;
								default:
									$title_classes[] = 'fluid-object';
									break;
							}
						} else $title_classes[] = 'fluid-object';
						$output .= 			'<div class="'. trim(implode(' ', $title_classes)) . '"'.$dummy_oembed.'>'.$media_code.'</div>';

					endif;

				endif;

			endif;

			if (($single_text === 'under' || $single_elements_click !== 'yes') && $media_type === 'image'):

				$output .= 				'</a>';

			endif;

			if (class_exists( 'WooCommerce' ) && $is_product) {
				$product = wc_get_product($block_data['id']);
				if (!empty($product)) {
					if (get_option('woocommerce_hide_out_of_stock_items') == 'yes' && ! $product->is_in_stock()) return;
					$product_add_to_cart = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s %s product_type_%s ">%s</a>',
											        esc_url( $product->add_to_cart_url() ),
											        esc_attr( $block_data['id'] ),
											        esc_attr( $product->get_sku() ),
											        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
													$product->supports( 'ajax_add_to_cart' ) && get_option('woocommerce_enable_ajax_add_to_cart') == 'yes' ? 'ajax_add_to_cart' : '',
											        esc_attr( $product->get_type() ),
											        esc_html( $product->add_to_cart_text())
											   );

					$output .=				'<div class="add-to-cart-overlay">'.$product_add_to_cart.'</div>';
				}
				
			}

			$output .= 				'</div>
								</div>
							</div>';

		endif;

		if ($single_text === 'under' && $layoutLast !== 'media') :

			$output .= $entry;

		endif;

		$output .= 		'</div>
					</div>';

		return $output;
  }
?>