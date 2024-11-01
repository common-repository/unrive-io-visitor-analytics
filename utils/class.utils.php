<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Specific and General Utility Methods
 */
class Unriveio_Utils {

    /**
    * Custom method to check for the existance of an option 
    */
    public static function option_exists( $option_name, $site_wide = false ) {
      global $wpdb; 
      return $wpdb->query( $wpdb->prepare( "SELECT * FROM ". ($site_wide ? $wpdb->base_prefix : $wpdb->prefix). "options WHERE option_name ='%s' LIMIT 1", $option_name ) );
    }

    /**
     * Checks if the API property exists - checks dates which don't exist yet
     */
    public static function api_property($url) {
      return (isset($url['data'][0])) ? $url['data'][0]['count'] : "0";
    }

    //iterates and check each enumerable - 30 days
     public static function api_property_iterate($url) {
      $total = 0;
      foreach($url['data'] as $item) { 
      $total += (isset($item['count'])) ? $item['count'] : "0";
      }
      return $total;
    }

    /**
     * Gets the default website
     */
    public static function get_selected($id) {
      return (get_option('unriveio_website_id') == $id) ? "selected" : "";
    }

    /**
     * HTML to generate the website select form
     */
    public static function get_website_form() {
      $sanitized_url = esc_url( admin_url( 'admin-post.php' ) );
      echo'<form method="post" action="'.$sanitized_url.'?action=unriveio_update_website" novalidate="novalidate">
      <input type="hidden" name="unriveio_update_website_form" value="'.esc_attr(wp_create_nonce('unriveio_update_website')).'" />
      <input type="hidden" name="action" value="unriveio_update_website" />';
      echo '<select id="website" name="website">';
        foreach(Unriveio_Wp_Remote_Static::request('websites')['data'] as $item) {
          echo '<option value="'.esc_attr($item['id']) .'" '.esc_attr(Unriveio_Utils::get_selected($item['id'])).'>'.esc_url($item['domain']).'</option>';
        }
      echo '</select>';
      echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Update 》">';
      echo '</form>';
    }

    /**
     * Stores Key Encrypted
     */
    public static function store_encrypted($api_key) {
          //encrypt the key while in storage
          $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes128"));
          $key = openssl_encrypt($api_key,"aes128", wp_salt( 'AUTH_SALT' ), 0, $iv);  

          //update or add to wp_options
          if (self::option_exists('unriveio_api_key')) {
              update_option( 'unriveio_api_keyiv' , bin2hex($iv) , '' , 'no'); 
              update_option( 'unriveio_api_key',  $key , '' , 'no' );
              update_option( 'unriveio_api_key_encrypted', 'yes' , '' , 'no' );
              update_option('unriveio_website_id','','' , 'no' );
              
            } else {
              add_option( 'unriveio_api_keyiv' , bin2hex($iv) , '' , 'no'); 
              add_option( 'unriveio_api_key' , $key , '' , 'no'); 
              add_option( 'unriveio_api_key_encrypted', 'yes' , '' , 'no' );
              add_option('unriveio_website_id','','' , 'no' );
            }
    }
    
    /**
     * Stores Key Unencrypted
     */
    public static function store_unencrypted($api_key) {
        //store without encryption
        if (self::option_exists('unriveio_api_key')) {
          update_option( 'unriveio_api_keyiv' ,' ' , '' , 'no'); 
          update_option( 'unriveio_api_key',   $api_key, '' , 'no' );
          update_option( 'unriveio_api_key_encrypted', 'no' , '' , 'no' );
          update_option('unriveio_website_id','','' , 'no' );
        } else {
          add_option( 'unriveio_api_keyiv' ,'' , '' , 'no'); 
          add_option( 'unriveio_api_key' ,  $api_key , '' , 'no'); 
          add_option( 'unriveio_api_key_encrypted', 'no' , '' , 'no' );
          add_option('unriveio_website_id','','' , 'no' );
        }
    }

} 