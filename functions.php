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

function ir_product_search_header() {
  ?>
  <div class="wp-block-group full-width-background-colour">
    <div class="wp-block-group__inner-container">
      <div class="wp-block-woocommerce-product-search homepage-product-search container">
        <div class="wc-block-product-search homepage-product-search container">
          <form role="search" method="get" action="/">
            <label for="wc-block-product-search-3" class="wc-block-product-search__label screen-reader-text">Search</label>
            <div class="wc-block-product-search__fields">
              <input type="search" id="wc-block-product-search-3" class="wc-block-product-search__field" placeholder="Find your next rental..." name="s"/>
              <input type="hidden" name="post_type" value="product"/>
              <button type="submit" class="wc-block-product-search__button" label="Search">
                <svg aria-hidden="true" role="img" focusable="false" class="dashicon dashicons-arrow-right-alt2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewbox="0 0 20 20"><path d="M6 15l5-5-5-5 1-2 7 7-7 7z"></path></svg>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php
}
add_action( 'onepress_page_before_content', 'ir_product_search_header');
