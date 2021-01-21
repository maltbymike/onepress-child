<?php
function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Add product search bar to bottom of header
function ir_product_search_header() {
  get_template_part( 'templates/header', 'search' );
}
add_action( 'onepress_page_before_content', 'ir_product_search_header');

// Override onepress_get_social_profiles to add noopener tag
function onepress_get_social_profiles() {
	$array = get_theme_mod( 'onepress_social_profiles' );
	if ( is_string( $array ) ) {
		$array = json_decode( $array, true );
	}
	$html = '';
	if ( ! empty( $array ) && is_array( $array ) ) {
		foreach ( $array as $k => $v ) {
			$array[ $k ] = wp_parse_args(
				$v,
				array(
					'network' => '',
					'icon' => '',
					'link' => '',
				)
			);

			// Get/Set social icons
			// If icon isset
			$icons = array();
			$array[ $k ]['icon'] = trim( $array[ $k ]['icon'] );

			if ( $array[ $k ]['icon'] != '' && strpos( $array[ $k ]['icon'], 'fa' ) !== 0 ) {
				$icons[ $array[ $k ]['icon'] ] = 'fa-' . $array[ $k ]['icon'];
			} else {
				$icons[ $array[ $k ]['icon'] ] = $array[ $k ]['icon'];
			}

			$network = ( $array[ $k ]['network'] ) ? sanitize_title( $array[ $k ]['network'] ) : false;
			if ( $network && ! $array[ $k ]['icon'] ) {
				$icons[ 'fa-' . $network ] = 'fa-' . $network;
			}

			$array[ $k ]['icon'] = join( ' ', $icons );

		}
	}

	foreach ( (array) $array as $s ) {
		if ( $s['icon'] != '' ) {
			$html .= '<a target="_blank" rel="noopener" href="' . $s['link'] . '" title="' . esc_attr( $s['network'] ) . '"><i class="fa ' . esc_attr( $s['icon'] ) . '"></i></a>';
    }
		}

	return $html;
}

add_filter( 'woocommerce_upsell_display_args', 'ir_woocommerce_upsell_display_args', 99999 );

function ir_woocommerce_upsell_display_args( $args ) {
  	  $args['columns']        = 3;
      $args['posts_per_page'] = 10;
  	   return $args;
  	}
