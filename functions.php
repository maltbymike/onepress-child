<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

function wplook_activate_gutenberg_products($can_edit, $post_type){
	if($post_type == 'product'){
		$can_edit = true;
	}

	return $can_edit;
}
add_filter('use_block_editor_for_post_type', 'wplook_activate_gutenberg_products', 10, 2);
