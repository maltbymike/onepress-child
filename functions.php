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


// Move upsells section above Additional Information
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'ir_woocommerce_output_upsells', 5 );

function ir_woocommerce_output_upsells() {
  woocommerce_upsell_display( -1,1 );
}

// Customise display of upsells
add_action( 'woocommerce_before_shop_loop_item', 'ir_customize_single_upsells' );
function ir_customize_single_upsells() {
   global $woocommerce_loop;
   if ( $woocommerce_loop['name'] == 'up-sells' ) {
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
      add_filter( 'woocommerce_get_price_html', 'ir_change_product_price_rental_rates', 10, 2 );
   }
}

// add_filter( 'woocommerce_product_upsells_products_heading', 'ir_product_upsells_products_heading' );
function ir_product_upsells_products_heading() {
  return
  "<div class='row product-table-rate-heading'>
    <span class='product-table-heading'>Image</span>
    <span class='product-table-heading'>Product</span>
    <span class='product-table-heading'>4 Hour</span>
    <span class='product-table-heading'>Daily</span>
    <span class='product-table-heading'>Weekly</span>
  </div>";
}

function ir_change_product_price_rental_rates( $price_html, $product ) {

  if ($product->is_type('simple_rental')) {

    $price_html  = '<span class="rental-price-group"><span class="rental-price rental-price-heading 4-hour">4 Hour</span><span class="rental-price 4-hour-rate">$' . $product->get_4_hour_rate() . '</span></span>';
    $price_html .= '<span class="rental-price-group"><span class="rental-price rental-price-heading daily-rate">Daily</span><span class="rental-price daily-rate">$' . $product->get_daily_rate() . '</span></span>';
    $price_html .= '<span class="rental-price-group"><span class="rental-price rental-price-heading weekly-rate">Weekly</span><span class="rental-price weekly-rate">$' . $product->get_weekly_rate() . '</span></span>';

  }

  return $price_html;
}
