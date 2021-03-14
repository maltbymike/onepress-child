<?php
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

?>
