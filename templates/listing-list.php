<?php

/**

 * This template can be overridden by copying it to yourtheme/templates/listing-list.php.

 */

$wmc_button_text = ( get_option( 'wmc_button_text' ) ? get_option( 'wmc_button_text' ) : __( 'Add to Cart', 'mpc' ) );

$wmc_button_color = ( get_option( 'wmc_button_color' ) ? get_option( 'wmc_button_color' ) : '#000000' );

$wmc_thead_back_color = ( get_option( 'wmc_thead_back_color' ) ? get_option( 'wmc_thead_back_color' ) : '#000000' );

$variation = mpc_check_if_variation_exists( $ids );

?>

<div class="woo-notices"></div>

<div class="woocommerce-page woocommerce">

    <?php //include( WMC_DIR . '/assets/css/style.php' ); ?>

    <style type="text/css">

    <?php if( $wmc_thead_back_color ) echo '.product-table-heading div{ background: ' . $wmc_thead_back_color . '}'; ?>

    </style>

    <!--
    <form class="cart" method="post" enctype="multipart/form-data">
    -->
    <div class="container product-table">

      <div class="row product-table-rate-heading">

          <div class="d-none d-md-inline col-md-2 product-image product-table-heading"><?php echo __( 'Image', 'mpc' ); ?></div>

          <div class="d-none d-md-inline col-md-4 product-name product-table-heading"><?php echo __( 'Product', 'mpc' ); ?></div>

          <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( 'Daily', 'mpc' ); ?></div>

          <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( 'Weekly', 'mpc' ); ?></div>

          <div class="d-none d-md-inline col-4 col-md-2 product-price-top product-table-heading"><?php echo __( 'Weekend', 'mpc' ); ?></div>

      </div>

      <?php

      if( count( $ids ) > 0 ) :

      	foreach( $ids as $id ) :

              $post_obj = get_post( $id );

              $_product = wc_get_product( $id );

              if( !$_product->is_type( 'simple_rental' ) ) continue;

              if( $_product->get_catalog_visibility() == 'hidden' ) continue;
              if( $_product->get_catalog_visibility() == 'search' ) continue;

              if( isset( $post_obj->post_parent ) ){

                  $pp = wc_get_product( $post_obj->post_parent );

                  if( !empty( $pp ) && $pp->is_type( 'grouped' ) ) continue;

              }

              if ( isset( $_product ) && $_product->exists() ) { ?>

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

                        // printf( '<img width="300" height="254" src="%s" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" data-fullimage="%s">', $thumbnail, $full );

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

                <div class="col-4 col-md-2 p-0">

                    <?php if ($_product->is_type('simple_rental')) { ?>
                        <div class="d-block d-md-none product-price-top product-table-heading">Weekend</div>
                        <div class="product-price cart_item"><?php echo __( $_product->get_weekend_fri_rate(), "mpc" ); ?></div>
                    <?php } ?>

                </div>

            </div>

            <?php

                }

        	endforeach;

        endif;

        ?>

      </div>

    <div id="mpcpop"></div>

    <?php include( WMC_DIR . '/assets/js/scripts.php'); ?>

</div>
