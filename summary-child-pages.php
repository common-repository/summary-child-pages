<?php

/**
* Plugin Name: Summary of Child Pages
* Plugin URI:
* Description: Inserts a list of the child pages of the current page or other specified page
* Version: 1.0.0
* Author: AxiomCode
* Author URI: http://axiomcode.com
*/

//[summary-child-pages] 
//[summary-child-pages id=parent_id] 
//[summary-child-pages list_only="1"] = no images, excerpt and read more

add_action('admin_head', 'summary_child_pages_thumbnail', 10);

function summary_child_pages_thumbnail()
{
    add_meta_box('postimagediv', 'Featured Image', 'post_thumbnail_meta_box', 'page', 'side', 'low');
}


add_action('wp_head', 'summary_child_pages_css');

function summary_child_pages_css()
{
?>
<style type="text/css">
.child-pages li {
	clear: both;
}
.thumb {
	height: 150px;
	width: 150px;
	overflow: hidden;
	float: left;
	margin-right: 10px;
}
.thumb img {
	width: 150px;
}
.child-excerpt {
}
.child-pages {
    list-style: none;
}
</style>
<?php
}
add_shortcode('summary-child-pages', 'summary_child_pages');

function summary_child_pages($atts, $content)
{
	global $post;
        $list_only = $atts['list_only'];

	if (!is_page()) {
		return '';
	}
	
	$text = '<ul class="child-pages">';
	$id = ($atts['id']) ? $atts['id'] : $post->ID;
	query_posts('post_type=page&post_parent=' . $id . '&posts_per_page=-1');
	  if (have_posts()) : ?>
	   <?php while (have_posts()) : the_post();
				$image = '';
                $image = get_the_post_thumbnail(get_the_ID(), array(200));
				if ($image == '') {
					$img = scp_catch_page_image();
					if ($img != '') {
						$image = '<img title="' . get_the_title() . '" src="' . $img . '" />';
					}
				}
                //get_the_post_thumbnail($post_id, 'post-thumbnail')
                $text .= '<li>';
                if ($list_only) {
                $text .= '<a href="' . get_permalink(). '">' . get_the_title() . '</a>';
                }
                else {
		$text .= '  <div class="item">
				  <div class="thumb" style="' . (($image == '') ? 'display: none;' : '') . '">                                        
					<a title="' . get_the_title() . ' "href="' . get_permalink(). '" class="thumbnail" style="float: left; margin-right: 15px;">' . $image . '</a>
				  </div>
				  <div class="child-excerpt">
					<h2><a href="' . get_permalink(). '">' . get_the_title() . '</a></h2>
					<div>'. get_the_excerpt() . '</div>
                                            <a class="read-more" title="More About ' . get_the_title() . '" href="' . get_permalink(). '">Read More</a>
				  </div>
			  </div>';                    
                }
                

                $text .= '</li>';
		 endwhile; ?>
	   <?php else : ?>
	   NO PAGES FOUND
	  <?php endif;
	  

	  wp_reset_query();
          $text .= '</ul><div style="clear:both"></div>';
	  return $text;
}

// Get URL of first image in a post
function scp_catch_page_image() {
	global $post, $posts;
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];

	// no image found display default image instead
	if(empty($first_img)){
		$first_img = "";
	}
	return $first_img;
}