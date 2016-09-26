<?php

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles',11);
function child_enqueue_styles() {
    $parent_style = 'parent-style';

    //Dequeue styles: Avoids WP to try to load the styles from the chil directory
    wp_dequeue_style( 'islemag-bootstrap' );
	wp_dequeue_style( 'islemag-style' );
	wp_dequeue_style( 'islemag-fontawesome' );

    wp_enqueue_style( 'telcomag-bootstrap', get_template_directory_uri().'/css/bootstrap.min.css',array(), '3.3.5');
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'telcomag-fontawesome', get_template_directory_uri().'/css/font-awesome.min.css',array(), '4.4.0');

    wp_enqueue_style( 'telcomag-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ),  wp_get_theme()->get('Version') );
}


// Adds the featured image at the begining of the content of each single post

add_filter( 'the_content', 'featured_image_before_content' );

function featured_image_before_content( $content ) {
    if ( is_singular('post') && has_post_thumbnail()) {
        $thumbnail = '<div class="post-thumbnail">
            <figure>
                <?php the_post_thumbnail(); ?>
            </figure>
        </div><!-- End .entry-media -->' . get_the_post_thumbnail();
    }
    $content = $thumbnail . $content;

    return $content;
}

// Enable featured images in blog posts
if ( ! function_exists( 'telcomag_setup' ) ) :
function telcomag_setup() {

	// Enable support for Post Thumbnails, and declare two sizes.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 770, 320, true );

}
endif;
add_action( 'after_setup_theme', 'telcomag_setup' );

//Change the default "Leave a reply" message from the post comment section
add_filter('comment_form_defaults', 'set_my_comment_title', 20);
function set_my_comment_title( $defaults ){
  $defaults['title_reply'] = __('Your opinions are valuable. Leave a comment!', 'customizr-child');
  return $defaults;
}

//Remove URL field from comments form
function disable_comment_url($fields) {
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields','disable_comment_url');


//Insert ads after second paragraph of single post content.

add_filter( 'the_content', 'prefix_insert_post_ads' );

function prefix_insert_post_ads( $content ) {

  //Code from AdSense using "Under Slider" add unit
	$ad_code = '<div class="post-embeded-addunit">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Under slider -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1716846922074930"
     data-ad-slot="4640265402"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>';

	if ( is_single() && ! is_admin() ) {
		return prefix_insert_after_paragraph( $ad_code, 2, $content );
	}

	return $content;
}

// Parent Function that makes the magic happen

function prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
	$closing_p = '</p>';
	$paragraphs = explode( $closing_p, $content );
	foreach ($paragraphs as $index => $paragraph) {

		if ( trim( $paragraph ) ) {
			$paragraphs[$index] .= $closing_p;
		}

		if ( $paragraph_id == $index + 1 ) {
			$paragraphs[$index] .= $insertion;
		}
	}

	return implode( '', $paragraphs );
}
?>
