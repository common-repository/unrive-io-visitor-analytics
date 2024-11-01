<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Admin {

    private static $initiated = false;
    private $curl;

    /**
     * Init Admin
     */
    public static function init() {
      if ( ! self::$initiated ) {
			  self::init_hooks();
		  }
    }

    /**
     * Performs checks including setting default website, are php dependent modules installed, checks openSSL and Curl, then checks the API key is valid.
     */
    public static function perform_checks() { 

      if (!(self::is_default_website())) {
            self::set_default_website();
      }
      
      $error_count = 0;

      if (!extension_loaded('openssl')) {
        $error= new WP_Error( 'Unrive.io Error', __( "OpenSSL module is required to retreive data from unrive.io, please install or contact your webhost.", "" ) );
        echo '<div class="unriveio_error">'.esc_html($error->get_error_message()).'</div>';
        $error_count++;
      }

      if(!isset(Unriveio_Wp_Remote_Static::request('stats/'.get_option('unriveio_website_id').'?name=visitors&from='.date("Y-m-d").'&to='.date("Y-m-d").'&per_page=1')['data'])) {
        $error= new WP_Error( 'Unrive.io Error', __( "Error With API, Please use the configuration section to input your API key (found in your unrive.io account). Be sure to check your API key and ensure you have a website profile created in your unrive.io account.", "" ) );
        echo '<div class="unriveio_error">'.esc_html($error->get_error_message()).'</div>';
        $error_count++;
      }

      if($error_count >0 )  { 
        die();
       }

    }

      /**
       * Bootstrap the Hooks
       */
      public static function init_hooks() {
          self::$initiated = true;
          add_action( 'admin_init', array( 'Unriveio_Admin', 'admin_init' ) );
          add_action( 'admin_menu', array( 'Unriveio_Admin', 'admin_menu' ), 5 );

          //form reponses
          add_action( 'admin_post_unriveio_api_key',  array ( __CLASS__, 'unriveio_api_key' ));
          add_action( 'admin_post_unriveio_update_website',  array ( __CLASS__, 'unriveio_update_website' ));
      }

      /**
       * Init the Admin Panel
       */
    public static function admin_init() {
      if ( get_option( 'Activated_Unriveio' ) ) {
          delete_option( 'Activated_Unriveio' );
          if ( ! headers_sent() ) {
            $admin_url = self::get_page_url( 'init' );
            wp_redirect( $admin_url );
            exit;
          }  
        }
    } 


    /**
     * Updates the website account form 
     */
    public static function unriveio_update_website() { 

      //sanitize and verify the form
      if(!wp_verify_nonce(sanitize_text_field($_POST['unriveio_update_website_form']), 'unriveio_update_website' )) {
        wp_die('Form cannot be verified.');
      }

      //sanitize 
      $website_id = sanitize_text_field($_POST['website']);
      
      //validate unriveio_website_id
      if (is_numeric($website_id)) {
        update_option('unriveio_website_id', $website_id);
      } else {
        wp_die('Invalid Website ID');
      }

      wp_redirect(admin_url('admin.php?page=unriveio_stats'));
      exit;
    } 

    /**
     * Creates the website select form on the admin view
     */
    public static function create_website_select() { 
      Unriveio_Utils::get_website_form();
    }


    /**
     * Sets the default website ID for the API - Unriveio accounts can have multiple blogs
     */
    public static function set_default_website() {
      $id = 0;
      if(isset(Unriveio_Wp_Remote_Static::request('websites')['data'][0])) {
        $id = Unriveio_Wp_Remote_Static::request('websites')['data'][0]['id'];
        update_option("unriveio_website_id",$id);
      }
      return (int) $id;
    }

    /**
     * Gets the default website account from unrive.io
     */
    public static function is_default_website() {
      return get_option("unriveio_website_id");
    }

    /**
      * Create the Menu System
      */
    public static function admin_menu() {
      add_menu_page(__('Unrive.io','menu-test'), __('Unrive.io','menu-test'), '', 'mt-top-level-handle','statistics', plugins_url( 'unriveio/favicon.png' ), null);
      add_submenu_page('mt-top-level-handle', __('Visitor Summary','menu-test'), __('Visitor Summary','menu-test'), 'manage_options', 'unriveio_stats',  array ( __CLASS__, 'unriveio_stats' )); 
      add_submenu_page('mt-top-level-handle', __('Configuration','menu-test'), __('Configuration','menu-test'), 'manage_options', 'unriveio_configuration',  array ( __CLASS__, 'unriveio_configuration' ));
      add_submenu_page('mt-top-level-handle', __('FAQ','menu-test'), __('FAQ','menu-test'), 'manage_options', 'unriveio_faq',  array ( __CLASS__, 'unriveio_faq' ));          
    } 

    /**
    * Process and store the API Key
    */
    public static function unriveio_api_key() {

      //sanitize and validate api key for whitespaces etc..
      $api_key = sanitize_text_field($_POST['apikey']);

      //validate
      if (isset($api_key)) {
        
        //sanitize and verify the nonce
        if(!wp_verify_nonce(sanitize_text_field($_POST['add_unriveio_api_key_validation_form']), 'add_unriveio_api_key_validation' )) {
            wp_die('Form cannot be verified.');
        }

        //store key encrypted or without
        (sanitize_text_field($_POST['encrypt']) == 'yes') ? Unriveio_Utils::store_encrypted($api_key) : Unriveio_Utils::store_unencrypted($api_key);
          
      }
      wp_redirect( admin_url( 'admin.php?page=unriveio_configuration' ) );
      exit;
    }

    /**
     * Get API Key
     */
    public static function get_api_key($api_key)  {
      return (get_option( 'unriveio_api_key_encrypted')== "yes") ? openssl_decrypt($api_key,"aes128", wp_salt( 'AUTH_SALT' ), 0, hex2bin(get_option( 'unriveio_api_keyiv'))) : $api_key; 
    }

    /**
      * Display the Visitor Summary through widgets, with row, column css.
    */
    public static function unriveio_stats() {

      self::perform_checks();
      unriveio_headers();

      self::create_website_select();

      echo '<div class="unriveio_row"><div class="unriveio_column">';
      the_widget( 'Unriveio_Visits' );
      
      echo '</div><div class="unriveio_column">';
      the_widget( 'Unriveio_Visits_Days' );
      echo '</div></div><hr />';
      
      echo '<div class="row"><div class="unriveio_column">';
      the_widget( 'Unriveio_Most_Read_Days' );
      echo '</div>';
      
      echo '<div class="unriveio_column">';
      the_widget('Unriveio_Traffic_Sources_Days');
      echo'</div></div>';
      
      echo '<div class="unriveio_row">';
      the_widget( 'Unriveio_Traffic_Region_Days' );
      echo '</div>';
      
    }

    /**
     * Generates the configuration form -
     */
    public static function unriveio_configuration() {
      unriveio_configuration_form();
    }

    /**
     * Generates the FAQ
     */
    public static function unriveio_faq(){ 
      unriveio_configuration_faq();
    }


    }
