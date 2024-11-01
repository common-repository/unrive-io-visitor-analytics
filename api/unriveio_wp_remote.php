<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Wp_Remote_Wrapper {

//data property
public $data;

/**
  * Creates the curl wrapper in object form
 */
public function request($uri) { 
   $args = array( 
        "method" => "GET",
        'headers' => array( 
                "Authorization" => "Bearer ". Unriveio_Admin::get_api_key(get_option('unriveio_api_key'))
        ),
        'timeout'=> '20',
        'redirection' => '0',
    ); 

    $response = wp_remote_request( "https://unrive.io/api/v1/".$uri, $args ); 
    return json_decode( $response['body'], true ); 
}
}