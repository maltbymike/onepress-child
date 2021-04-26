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

/* Remove Categories from Single Products */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Add prefix to sale item prices
add_filter( 'woocommerce_get_price_html', 'ir_add_price_prefix_for_sales', 99, 2 );
function ir_add_price_prefix_for_sales( $price, $product ){
  if (!$product->is_type('simple_rental') && !is_product_category()) {
    $price = 'Sell Price: ' . $price;
  }
  return $price;
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
   if ( $woocommerce_loop['name'] == 'up-sells' OR $woocommerce_loop['name'] == 'related' ) {
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
   }
}

add_filter( 'woocommerce_get_price_html', 'ir_change_product_price_rental_rates', 15, 2 );
add_action( 'woocommerce_shop_loop_item_title', 'ir_add_wrapper_upsell_content', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'ir_close_wrapper_upsell_content', 15 );

function ir_add_wrapper_upsell_content() {
  echo "<span class='rental-product-upsells-content'>";
}
function ir_close_wrapper_upsell_content() {
  echo "</span>";
}
function ir_change_product_price_rental_rates( $price_html, $product ) {
  if ($product->is_type('simple_rental')) {
    $price_html  = '<span class="rental-price-group"><span class="rental-price rental-price-heading 4-hour">4 Hour</span><span class="rental-price 4-hour-rate">$' . $product->get_4_hour_rate() . '</span></span>';
    $price_html .= '<span class="rental-price-group"><span class="rental-price rental-price-heading daily-rate">Daily</span><span class="rental-price daily-rate">$' . $product->get_daily_rate() . '</span></span>';
    $price_html .= '<span class="rental-price-group"><span class="rental-price rental-price-heading weekly-rate">Weekly</span><span class="rental-price weekly-rate">$' . $product->get_weekly_rate() . '</span></span>';
  }
  return $price_html;
}

// Sort upsells in menu order
add_filter( 'woocommerce_upsells_orderby', 'ir_filter_woocommerce_upsells_orderby', 10, 1 );
function ir_filter_woocommerce_upsells_orderby( $orderby ) {
    return 'menu_order';
};

// Sort upsells in ascending order
add_filter( 'woocommerce_upsells_order', 'ir_filter_woocommerce_upsells_order', 10, 1 );
function ir_filter_woocommerce_upsells_order( $order ) {
    return 'asc';
}

// Set related product in 1 column rows
add_filter( 'woocommerce_related_products_columns', 'ir_filter_woocommerce_related_products_columns', 10);
function ir_filter_woocommerce_related_products_columns() {
    return 1;
}

/***************************************************
Nested Subcategories and products
***************************************************/
//Remove Subcategory Thumbnail
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

//Override Default setting for product # per row to force list view
function loop_columns() {
  return 1; // 1 product per row
}
add_filter ( 'loop_shop_columns', 'loop_columns', 999);

// Add subcategory rate header below subcategories
function ir_get_product_table( $category ) {

  get_template_part( 'templates/archive', 'producttable' );

  $subcategory_products = new WP_Query(
    array(
      'post_type' => 'product',
      'product_cat' => $category->slug,
      'tax_query' => array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'exclude-from-catalog',
            'operator' => 'NOT IN',
        ),
      ),
    )
  );

  if($subcategory_products->have_posts()):?>

      <?php while ( $subcategory_products->have_posts() ) : $subcategory_products->the_post();

        $_product = wc_get_product( $subcategory_products->post->ID ); ?>

        <div class="row align-items-center <?php echo esc_attr( sanitize_title( $_product->get_type() ) ); ?>">

          <div class="col-md-2 product-image cart_item">

              <?php

              $image_id  = $_product->get_image_id();

              $full = wp_get_attachment_image_url( $image_id, 'full' );

              $thumbnail = wp_get_attachment_image_url( $image_id, 'thumbnail' );

              ?>

              <?php

              $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image() );

              if ( ! $_product->is_visible() ) {

                  echo $thumbnail;

              } else {

                  printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink() ), $thumbnail );

              } ?>

          </div>

          <div class="col-md-4 product-name cart_item">

              <?php if ( ! $_product->is_visible() ){

                  echo __( apply_filters( 'woocommerce_cart_item_name', esc_html( $_product->get_title() ) ) . '&nbsp;', "mpc" );

              } else{

                  echo __( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', esc_url( $_product->get_permalink() ), esc_html( $_product->get_title() ) ) ), "mpc" );

              } ?>

          </div>

          <div class="col-4 col-md-2 p-0">

              <?php if ($_product->is_type('simple_rental')) { ?>
                  <div class="d-block d-md-none product-price-top product-table-heading">4 Hours</div>
                  <div class="product-price cart_item"><?php echo __( $_product->get_4_hour_rate(), "mpc" ); ?></div>
              <?php } ?>

          </div>

          <div class="col-4 col-md-2 p-0">

              <?php if ($_product->is_type('simple_rental')) { ?>
                  <div class="d-block d-md-none product-price-top product-table-heading">Daily</div>
                  <div class="product-price cart_item"><?php echo __( $_product->get_daily_rate(), "mpc" ); ?></div>
              <?php } ?>

          </div>

          <div class="col-4 col-md-2 p-0">

              <?php if ($_product->is_type('simple_rental')) { ?>
                  <div class="d-block d-md-none product-price-top product-table-heading">Weekly</div>
                  <div class="product-price cart_item"><?php echo __( $_product->get_weekly_rate(), "mpc" ); ?></div>
              <?php } ?>

          </div>

        </div>

      <?php endwhile;?>

  <?php endif; wp_reset_query();

}
add_action( 'woocommerce_after_subcategory', 'ir_get_product_table', 15);
