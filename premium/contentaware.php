<?php

class MeowPro_MWAI_ContentAware {
  private $core = null;

  function __construct( $core ) {
    $this->core = $core;
    add_filter( 'mwai_chatbot_params', array( $this, 'chatbot_params' ) );
  }

  function chatbot_params( $params ) {
    if ( !isset( $params['content_aware'] ) && !isset( $params['contentAware'] ) ) {
      return $params;
    }
    $post = get_post( isset( $params['contextId'] ) ? $params['contextId'] : null );
    if ( empty( $post ) ) {
      return $params;
    }

    // Content
    if ( !strpos( $params['context'], '{CONTENT}' ) === false ) {
      $content = $this->core->getCleanPostContent( $post->ID );

      // If WooCommerce, get the Product Description
      if ( class_exists( 'WooCommerce' ) ) {
        if ( is_product() ) {
          global $product;
          $shortDescription = $this->core->cleanText( $product->get_short_description() );
          if ( !empty( $shortDescription ) ) {
            $content .= $shortDescription;
          }
        }
      }
      $content = $this->core->cleanSentences( $content );
      $content = apply_filters( 'mwai_contentaware_content', $content, $post );
      $params['context'] = str_replace( '{CONTENT}', $content, $params['context'] );
    }

    // Excerpt
    if ( !strpos( $params['context'], '{EXCERPT}' ) === false ) {
      if ( !empty( $post ) ) {
        $excerpt = $this->core->cleanText( $post->post_excerpt );
        $params['context'] = str_replace( '{EXCERPT}', $excerpt, $params['context'] );
      }
    }

    return $params;
  }
}
