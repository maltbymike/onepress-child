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
