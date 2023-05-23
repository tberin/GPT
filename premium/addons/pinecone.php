<?php

class MeowPro_MWAI_Addons_Pinecone {
  private $core = null;
  private $pcApiKey = null;
  private $pcServer = null;
  private $pcIndex = null;
  private $pcHost = null;
  private $pcNamespace = null;
  private $options = [];

  function __construct() {
    global $mwai_core;
    $this->core = $mwai_core;
    $this->options = $this->core->get_option( 'pinecone' );
    $this->pcServer = $this->options['server'];
    $this->pcApiKey = $this->options['apikey'];
    $this->pcIndex = $this->options['index'];
    $this->pcNamespace = isset( $this->options['namespace'] ) ?
      $this->options['namespace'] : MWAI_DEFAULT_NAMESPACE;
    $this->pcHost = $this->pinecone_get_host( $this->pcIndex );

    add_filter( 'mwai_embeddings_add_index', array( $this, 'add_index' ), 10, 3 );
    add_filter( 'mwai_embeddings_list_indexes', array( $this, 'list_indexes' ), 10, 1 );
    add_filter( 'mwai_embeddings_delete_index', array( $this, 'delete_index' ), 10, 2 );
    add_filter( 'mwai_embeddings_add_vector', [ $this, 'add_vector' ], 10, 3 );
    add_filter( 'mwai_embeddings_query_vectors', [ $this, 'query_vectors' ], 10, 4 );
    add_filter( 'mwai_embeddings_delete_vectors', [ $this, 'delete_vectors' ], 10, 4 );
  }

  function pinecone_get_host( $indexName ) {
    $host = null;
    if ( !empty( $this->options['indexes'] ) ) {
      foreach ( $this->options['indexes'] as $i ) {
        if ( $i['name'] === $indexName ) {
          $host = $i['host'];
          break;
        }
      }
    }
    return $host;
  }

  function run( $method, $url, $query = null, $json = true, $isAbsoluteUrl = false )
  {
    $headers = "accept: application/json, charset=utf-8\r\ncontent-type: application/json\r\n" . 
      "Api-Key: " . $this->pcApiKey . "\r\n";
    $body = $query ? json_encode( $query ) : null;
    $url = $isAbsoluteUrl ? $url : "https://controller." . $this->pcServer . ".pinecone.io" . $url;
    $options = [
      "headers" => $headers,
      "method" => $method,
      "timeout" => MWAI_TIMEOUT,
      "body" => $body,
      "sslverify" => false
    ];

    try {
      $response = wp_remote_request( $url, $options );
      if ( is_wp_error( $response ) ) {
        throw new Exception( $response->get_error_message() );
      }
      $response = wp_remote_retrieve_body( $response );
      $data = $response === "" ? true : ( $json ? json_decode( $response, true ) : $response );
      if ( !is_array( $data ) && empty( $data ) && is_string( $response ) ) {
        throw new Exception( $response );
      }
      return $data;
    }
    catch ( Exception $e ) {
      error_log( $e->getMessage() );
      throw new Exception( 'Error while calling PineCone: ' . $e->getMessage() );
    }
    return [];
  }

  function add_index( $index, $name, $params ) {
    $podType = $params['podType'];
    $dimension = 1536;
    $metric = 'cosine';
    $index = $this->run( 'POST', '/databases', [
      'name' => $name,
      'metric' => $metric,
      'dimension' => $dimension,
      'pod_type' => "{$podType}.x1"
    ], true );
    return $index;
  }

  function delete_index( $success, $name ) {
    $index = $this->run( 'DELETE', "/databases/{$name}", null, true );
    $success = !empty( $index );
    return $success;
  }

  function list_indexes( $indexes ) {
    $indexesIds = $this->run( 'GET', '/databases', null, true );
    $indexes = [];
    foreach ( $indexesIds as $indexId ) {
      $index = $this->run( 'GET', "/databases/{$indexId}", null, true );
      $indexes[] = [
        'name' => $index['database']['name'],
        'metric' => $index['database']['metric'],
        'dimension' => $index['database']['dimension'],
        'host' => $index['status']['host'],
        'ready' => $index['status']['ready']
      ];
    }
    return $indexes;
  }

  function delete_vectors( $results, $ids, $deleteAll = false, $namespace = null ) {
    if ( empty( $namespace ) ) {
      $namespace = $this->pcNamespace;
    }
    $results = $this->run( 'POST', "https://{$this->pcHost}/vectors/delete", [
      'ids' => $deleteAll ? null : $ids,
      'deleteAll' => $deleteAll,
      'namespace' => $namespace
    ], true, true );
    return $results;
  }

  function add_vector( $success, $vector, $namespace = null ) {
    if ( empty( $namespace ) ) {
      $namespace = $this->pcNamespace;
    }
    $res = $this->run( 'POST', "https://{$this->pcHost}/vectors/upsert", [
      'vectors' => [
        'id' => (string)$vector['id'],
        'values' => $vector['embedding'],
        'metadata' => [
          'type' => $vector['type'],
          'title' => $vector['title']
        ]
      ],
      'namespace' => $namespace
    ], true, true );
    $success = isset( $res['upsertedCount'] ) && $res['upsertedCount'] > 0;
    return $success;
  }

  function query_vectors( $vectors, $vector, $indexName = null, $namespace = null ) {
    if ( empty( $namespace ) ) {
      $namespace = $this->pcNamespace;
    }
    $indexName = !empty( $indexName ) ? $indexName : $this->pcIndex;
    $host = $this->pinecone_get_host( $indexName );
    $res = $this->run( 'POST', "https://{$host}/query", [
      'topK' => 10,
      'vector' => $vector,
      'namespace' => $namespace
    ], true, true );
    $vectors = isset( $res['matches'] ) ? $res['matches'] : [];
    return $vectors;
  }
}
