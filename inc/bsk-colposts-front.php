<?php

class BSKColumnPostsFront{

	public function __construct() {
        
		add_shortcode('bsk-colposts', array($this, 'bsk_colposts_show_posts_as_column') );
	}
	
	function bsk_colposts_show_posts_as_column( $atts ){
		global $post, $wpdb;
		
		$shortcode_parameters = shortcode_atts( 
                                            array(  'include' => '', 
                                                       'taxonomy' => '',
                                                       'term-ids' => '',
                                                       'order-by' => 'post__in',
                                                       'order' => 'DESC',
													   'columns' => 2, 
													   'show-full-content' => false,
													   'show-featured-image' => false,
                                                       'featured-image-size' => '',
													   'add-link-to-featured-image' => false,
													   'show-title' => false,
													   'add-link-to-title' => true,
													   'read-more-text' => '',
													   'link-to-url' => '',
													   'link-target' => '' ), 
										  		$atts );
        
        $query_args = array();
        
        $plugin_settings = get_option( BSKColumnPosts::$_bsk_colposts_settings_option_name, '' );
		$plugin_truncate_content_eanble = false;
		$plugin_truncate_limited_length = 50;
        $pluing_default_featured_image_size = 'thumbnail';
		if( $plugin_settings && is_array($plugin_settings) ){
			if( isset($plugin_settings['truncate_content_eanble']) && $plugin_settings['truncate_content_eanble'] == 'YES' ){
				$plugin_truncate_content_eanble = true;
			}
			if( isset($plugin_settings['truncate_limited_length']) ){
				$plugin_truncate_limited_length = $plugin_settings['truncate_limited_length'];
			}
            if( isset($plugin_settings['default_featured_image_size']) ){
				$pluing_default_featured_image_size = $plugin_settings['default_featured_image_size'];
			}
		}
        $featured_image_size = $pluing_default_featured_image_size;
        
        //show readmore button
		$order_by = 'post__in';
		$order = null;
		if( trim($shortcode_parameters['order-by']) ){
			$order_by_parameter = trim($shortcode_parameters['order-by']);
            if( $order_by_parameter == 'title' || $order_by_parameter == 'date' ){
                $order_by = $order_by_parameter;
            }
			$order = strtoupper($shortcode_parameters['order-by']) == 'DESC' ? 'DESC' : 'ASC';
		}
        
        $taxonomy_obj = null;
        $term_id_array = array();
        if( isset( $shortcode_parameters['taxonomy'] ) ){
            $taxonomy_obj = get_taxonomy( $shortcode_parameters['taxonomy'] );
            if( $taxonomy_obj && isset($taxonomy_obj->object_type) && 
                is_array( $taxonomy_obj->object_type ) && count( $taxonomy_obj->object_type ) > 0 ){
                //
                $taxonomy_obj = $taxonomy_obj;
            }
            //organise term id array
            if( isset( $shortcode_parameters['term-ids']  ) && $shortcode_parameters['term-ids'] ){
                $term_id_array = explode( ',', $shortcode_parameters['term-ids'] );
                foreach( $term_id_array as $key => $term_id ){
                    $term_id_array[$key] = intval( $term_id );
                }
            }
        }
        
        if( $taxonomy_obj && count($term_id_array) > 0 ){
            $supported_post_type = array();
            foreach( $taxonomy_obj->object_type as $post_type ){
                $supported_post_type[] = $post_type;
            }

            $query_args['post_type'] = $supported_post_type;
            $query_args['tax_query'] = array(
                                                            array(
                                                                        'taxonomy' => $taxonomy_obj->name,
                                                                        'field'    => 'term_id',
                                                                        'terms'    => $term_id_array
                                                                   )
                                                          );
            if( $order_by == 'post__in' ){
                $order_by = 'date';
                $order = 'DESC';
            }
            $query_args['nopaging'] = true;
            $query_args['orderby'] = $order_by;
            $query_args['order'] = $order;
            $query_args['post_status'] = 'publish';
        }else{
            $ids = trim($shortcode_parameters['include']);
            $ids = trim($ids, ',');
            if( $ids == "" ){
                return '';
            }
            $posts_id_array = explode(',', $ids);
            if( !is_array($posts_id_array) || count($posts_id_array) < 1 ){
                return '';
            }
            //get all supported posts
            $post_type_results = get_post_types( array( 'public' => true ), 'objects' );
            $post_types_array = array();
            foreach ( $post_type_results  as $post_type_obj ) {
                $post_types_array[] = $post_type_obj->name;
            }
            $query_args['post__in'] = $posts_id_array;
            $query_args['post_type'] = $post_types_array;
            $query_args['nopaging'] = true;
            $query_args['orderby'] = $order_by;
            $query_args['order'] = $order;
            $query_args['post_status'] = 'publish';
        }

        //columns
		$columns = intval($shortcode_parameters['columns']);
		if( is_int($columns) == false ){
			$columns = 2;
		}
		$column_class = '';
		switch( $columns ){
			case 2:
				$column_class = ' bsk-colposts-two-columns';
			break;
			case 3:
				$column_class = ' bsk-colposts-three-columns';
			break;
			case 4:
				$column_class = ' bsk-colposts-four-columns';
			break;
			case 5:
				$column_class = ' bsk-colposts-five-columns';
			break;
			case 6:
				$column_class = ' bsk-colposts-six-columns';
			break;
			case 1:
			default:
				$column_class = '';
			break;
		}
		
		//show full content
		$show_full_content = false;
		if( $shortcode_parameters['show-full-content'] && is_string($shortcode_parameters['show-full-content']) ){
			$show_full_content = strtoupper($shortcode_parameters['show-full-content']) == 'TRUE' ? true : false;
			if( !$show_full_content ){
				$show_full_content = strtoupper($shortcode_parameters['show-full-content']) == 'YES' ? true : false;
			}
		}else if( is_bool($shortcode_parameters['show-full-content']) ){
			$show_full_content = $shortcode_parameters['show-full-content'];
		}
		
		//show featured image
		$show_feature_image = false;
		if( $shortcode_parameters['show-featured-image'] && is_string($shortcode_parameters['show-featured-image']) ){
			$show_feature_image = strtoupper($shortcode_parameters['show-featured-image']) == 'TRUE' ? true : false;
			if( !$show_feature_image ){
				$show_feature_image = strtoupper($shortcode_parameters['show-featured-image']) == 'YES' ? true : false;
			}
		}else if( is_bool($shortcode_parameters['show-featured-image']) ){
			$show_feature_image = $shortcode_parameters['show-featured-image'];
		}
		//add link featured image
		$add_link_to_feature_image = false;
		if( $shortcode_parameters['add-link-to-featured-image'] && is_string($shortcode_parameters['add-link-to-featured-image']) ){
			$add_link_to_feature_image = strtoupper($shortcode_parameters['add-link-to-featured-image']) == 'TRUE' ? true : false;
			if( !$add_link_to_feature_image ){
				$add_link_to_feature_image = strtoupper($shortcode_parameters['add-link-to-featured-image']) == 'YES' ? true : false;
			}
		}else if( is_bool($shortcode_parameters['add-link-to-featured-image']) ){
			$add_link_to_feature_image = $shortcode_parameters['add-link-to-featured-image'];
		}
        //featured image size
        if( isset($shortcode_parameters['featured-image-size']) && trim($shortcode_parameters['featured-image-size']) ){
             $featured_image_size = strtolower( $shortcode_parameters['featured-image-size'] );
            //check if the size name registered
            $sizes = BSKColumnPostsCommon::get_image_sizes();
            if( array_key_exists( $featured_image_size, $sizes ) ){
                $pluing_default_featured_image_size = $featured_image_size;
            }
        }
            
		//show title
		$show_title = false;
		if( $shortcode_parameters['show-title'] && is_string($shortcode_parameters['show-title']) ){
			$show_title = strtoupper($shortcode_parameters['show-title']) == 'TRUE' ? true : false;
			if( !$show_title ){
				$show_title = strtoupper($shortcode_parameters['show-title']) == 'YES' ? true : false;
			}
		}else if( is_bool($shortcode_parameters['show-title']) ){
			$show_title = $shortcode_parameters['show-title'];
		}
		//add link title
		$add_link_to_title = false;
		if( $shortcode_parameters['add-link-to-title'] && is_string($shortcode_parameters['add-link-to-title']) ){
			$add_link_to_title = strtoupper($shortcode_parameters['add-link-to-title']) == 'TRUE' ? true : false;
			if( !$add_link_to_title ){
				$add_link_to_title = strtoupper($shortcode_parameters['add-link-to-title']) == 'YES' ? true : false;
			}
		}else if( is_bool($shortcode_parameters['add-link-to-title']) ){
			$add_link_to_title = $shortcode_parameters['add-link-to-title'];
		}
		
		//show readmore button
		$show_readmore_button = false;
		$read_more_text = 'Read more...';
		if( trim($shortcode_parameters['read-more-text']) ){
			$show_readmore_button = true;
			$read_more_text = trim($shortcode_parameters['read-more-text']);
		}
		
		//link url
		$link_to_url = '';
		if( trim($shortcode_parameters['link-to-url']) ){
			$link_to_url = trim($shortcode_parameters['link-to-url']);
		}
		
		//link target
		$link_open_target = '';
		if( trim($shortcode_parameters['link-target']) ){
			$link_open_target = 'target="'.trim($shortcode_parameters['link-target']).'"';
		}
		
		global $post;
        
        $the_query = new WP_Query( $query_args );
        $column_str  = '<div class="bsk-colposts-container">'."\n";
		$post_count = 0;

        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                global $post;
                
                $the_query->the_post();
                
                $post_id = $post->ID;
                $title = get_the_title();
                $final_link = get_permalink();
                if( $link_to_url ){
                    $final_link = $link_to_url;
                }

                $column_str .= '<div class="bsk-colposts-post'.$column_class.' post-'.$post_id.'">'."\n";
                $column_str .= '<div class="bsk-colposts-post-content">'."\n";
                if( $show_feature_image ){
                    $featured_image_url = get_the_post_thumbnail( $post_id, $featured_image_size, array( 'class' => 'alignnone' ) );
                    if( $featured_image_url ){
                        $featured_image_div = '<div class="bsk-colposts-post-featured-image">'.$featured_image_url.'</div>';
                        if( $add_link_to_feature_image ){
                            $column_str .= '<a href="' . esc_url( $final_link ) . '" '.$link_open_target.'>'.$featured_image_div.'</a>';
                        }else{
                            $column_str .= $featured_image_div;
                        }
                    }
                }
                if( $show_title ){
                    if( $add_link_to_title ){
                        $column_str .= the_title( '<a href="' . esc_url( $final_link ) . '" '.$link_open_target.'><h2>', '</h2></a>', false )."\n";
                    }else{
                        $column_str .= the_title( '<h2>', '</h2>', false )."\n";
                    }
                }

                //check if <!--more--> exists
                if( strpos( $post->post_content, '<!--more-->' ) === false &&
                    $plugin_truncate_content_eanble ){
                    $content = get_the_content( '' );
                    $content = apply_filters( 'the_content', $content );
                    $content = str_replace( ']]>', ']]&gt;', $content );
                    $content = wp_trim_words( $content, $plugin_truncate_limited_length );
                }else{
                    $content = get_the_content( '', !$show_full_content );
                    $content = apply_filters( 'the_content', $content );
                    $content = str_replace( ']]>', ']]&gt;', $content );
                }
                $column_str .= $content;
                $column_str .= "\n";
                if( !$show_full_content ){
                    $column_str .= '<p><a href="'.$final_link.'" class="bsk-colposts-post-read-more">'.$read_more_text.'</a></p>';
                }
                $column_str .= '</div>';
                $column_str .= '</div>';

                $post_count++;
                if( $post_count % $columns == 0 ){
                    $column_str .= '<div style="clear:both;"></div>';
                }
            }
            $column_str .= '	<div style="clear:both;"></div>';

            wp_reset_postdata();
        }
		$column_str .= '</div>';
        
		return $column_str;
	}
}
