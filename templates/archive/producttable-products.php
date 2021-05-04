<?php
$category_slug = $args['category_slug'];
$subcategory_products = new WP_Query(
  array(
    'post_type' => 'product',
    'product_cat' => $category_slug,
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

        <div class="price col-12 col-md-6 p-0">

          <?php echo $_product->get_price_html(); ?>

        </div>

      </div>

    <?php endwhile;?>

<?php endif; wp_reset_query(); ?>
