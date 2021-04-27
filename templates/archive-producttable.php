<?php
$category = $args['category'];
?>

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

if($subcategory_products->have_posts()):

    while ( $subcategory_products->have_posts() ) : $subcategory_products->the_post();

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

        <div class="price">

          <?php echo $_product->get_price_html(); ?>

        </div>

      </div>

    <?php endwhile;?>

<?php endif; wp_reset_query();
