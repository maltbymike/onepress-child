<div class="container product-table">

  <div class="row product-table-rate-heading">

      <div class="d-none d-md-inline col-md-2 product-image product-table-heading"><?php echo __( 'Image', 'mpc' ); ?></div>

      <div class="d-none d-md-inline col-md-4 product-name product-table-heading"><?php echo __( 'Product', 'mpc' ); ?></div>

      <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( '4 Hour', 'mpc' ); ?></div>

      <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( 'Daily', 'mpc' ); ?></div>

      <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( 'Weekly', 'mpc' ); ?></div>

  </div>

</div>

<?php
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

      $_product = new WC_Product($subcategory_products->post->ID); ?>

      <div class="row align-items-center <?php echo esc_attr( sanitize_title( $_product->get_type() ) ); ?>">

        <div class="col-md-2 product-image cart_item">
          <?php woocommerce_template_loop_product_thumbnail(); ?>
        </div>

        <div class="col-md-4 product-name cart_item">
          <a href="<?php echo get_permalink( $subcategory_products->post->ID ) ?>">
              <?php echo $_product->get_name(); ?>
          </a>
        </div>

      </div>

    <?php endwhile;?>

<?php endif; wp_reset_query();
