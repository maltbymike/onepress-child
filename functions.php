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
//Remove product count
add_filter( 'woocommerce_subcategory_count_html', '__return_false' );

//Change Category Link Opening and closing to allow content toggle
function ir_template_loop_category_link_open( $category ) {
  echo '<a class="product-category-title-link collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-' . $category->slug . '" href="#collapse-' . $category->slug . '">';
}

function ir_template_loop_category_link_close() {
  echo '</a>';
}

function ir_template_loop_category_title_wrapper_open() {
  echo '<div class="product-category-content-toggle">';
}

function ir_template_loop_category_title_wrapper_close() {
  echo '</div>';
}
remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );

add_action( 'woocommerce_before_subcategory', 'ir_template_loop_category_title_wrapper_open', 10 );
add_action( 'woocommerce_before_subcategory', 'ir_template_loop_category_link_open', 10 );
add_action( 'woocommerce_after_subcategory', 'ir_auto_subcategory_thumbnail_wrapper_open', 10 );
add_action( 'woocommerce_after_subcategory', 'ir_auto_subcategory_thumbnail', 10 );
add_action( 'woocommerce_after_subcategory', 'ir_auto_subcategory_thumbnail_wrapper_close', 10 );
add_action( 'woocommerce_after_subcategory', 'ir_template_loop_category_link_close', 10 );
add_action( 'woocommerce_after_subcategory', 'ir_template_loop_category_title_wrapper_close', 15);

//Override Default setting for product # per row to force list view
function loop_columns() {
  return 1; // 1 product per row
}
add_filter ( 'loop_shop_columns', 'loop_columns', 999);

// Add subcategory rate header below subcategories
function ir_get_product_table( $category ) {
  echo '<div class="collapse" id="collapse-' . $category->slug . '">';

      echo '<div class="container product-table">';

          get_template_part( 'templates/archive/producttable', 'products', array ( 'category_slug' => $category->slug ) );

          $parentid = $category->term_id;
          $args = array(
              'parent' => $parentid
          );
          $categories = get_terms(
              'product_cat', $args
          );
          if ( $categories ) :

            echo '<ul class="products subcategory-products column-1">';

              foreach ( $categories as $category ) :

                  if ( $category->count > 0 ) :

                      echo '<li class="product-category product-subcategory product">';

                        echo '<div class="product-category-content-toggle">';

                          echo '<a class="product-category-title-link collapsed" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-' . $category->slug . '" href="#collapse-' . $category->slug . '">';

                            echo '<h3 class="woocommerce-loop-category__title">' . esc_html($category->name) . '</h3>';

                            ir_auto_subcategory_thumbnail_wrapper_open();
                            ir_auto_subcategory_thumbnail( $category );
                            ir_auto_subcategory_thumbnail_wrapper_close();

                          echo '</a>';

                        echo '</div>';

                        echo '<div class="collapse" id="collapse-' . $category->slug . '">';

                          echo '<div class="container product-table">';

                              get_template_part( 'templates/archive/producttable', 'products', array ( 'category_slug' => $category->slug ) );

                          echo '</div>';

                        echo '</div>';

                      echo '</li>';

                  endif;

              endforeach;

            echo '</ul>';

          endif;

      echo '</div>';

  echo '<div>';

}
add_action( 'woocommerce_after_subcategory', 'ir_get_product_table', 15);


/* Get Thumbnails from Category Products */
function ir_auto_subcategory_thumbnail( $category ) {

    $show_multiple = true;
    $recurse_category_ids = true;
    $limit = 4;

    // does this category already have a thumbnail defined? if so, use that instead
    if ( get_term_meta( $category->term_id, 'thumbnail_id', true ) ) {
        woocommerce_subcategory_thumbnail( $category );
        return;
    }

    // get a list of category IDs inside this category (so we're fetching products from all subcategories, not just the top level one)
    if ( $recurse_category_ids ) {
        $category_ids = get_sub_category_ids( $category );
        $category_slugs = get_sub_category_slugs( $category->term_id );
    } else {
        $category_ids = array( $category->term_id );
        $category_slugs = $category->term_id;
    }

    $query_args = array(
        'posts_per_page' => $show_multiple ? $limit : 1,
        'post_status' => 'publish',
        'post_type' => 'product',
        'meta_query' => array(
            array(
                'key' => '_thumbnail_id',
                'value' => '',
                'compare' => '!=',
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_ids,
                'operator' => 'IN',
            ),
        ),
    );

    $products = get_posts( $query_args );


    $query_args = array(
        'limit' => $show_multiple ? $limit : 1,
        'status' => 'publish',
        'featured' => true,
        'category' => $category_slugs,
    );

    $products = wc_get_product( $query_args );

    if ( $products ) {
        $image_size = 'shop_thumbnail';
        foreach ( $products as $product ) {
            echo get_the_post_thumbnail( $product->ID, $image_size );
        }
    } else {
        // show the default placeholder category image if there's no products inside this one
        woocommerce_subcategory_thumbnail( $category );
    }
}

function ir_auto_subcategory_thumbnail_wrapper_open() {
  echo '<div class="product-category-thumbnail">';
}

function ir_auto_subcategory_thumbnail_wrapper_close() {
    echo '</div>';
}


/* Recursive function to fetch a list of child category IDs for the one passed */
function get_sub_category_ids( $start, $results = array() ) {
  if ( !is_array( $results ) ) $results = array();

  $results[] = $start->term_id;
  $cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => $start->term_id ) );
  if ( is_array( $cats ) ) {
    foreach( $cats as $cat ) {
      $results = get_sub_category_ids( $cat, $results );
    }
  }

  return $results;
}

function get_sub_category_slugs( $term_id ) {
    $parent = get_term_by( 'id', $term_id, 'product_cat' );
    $term_slugs = (get_categories([
        'taxonomy' => 'category',
        'child_of' => $parent->term_id,
        'hide_empty' => false, // in the test, have no posts
    ]));

    return $term_slugs;
}
