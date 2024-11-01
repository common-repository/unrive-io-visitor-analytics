<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Visits extends WP_Widget {

private $wp_remote;

/**
 * Creates the Widget Titles
 */
function __construct() {
  parent::__construct(
    'unriveio_Visits',
    __('Today\'s Visitor Summary', 'text_domain'),
    array( 'description' => __( 'Displays a summary of visitors', 'text_domain' ) )
  );
  $this->wp_remote = new Unriveio_Wp_Remote_Wrapper();  
}


/**
 * Generates the widget HTML - Today -
 */
public function widget( $args, $instance ) {
    echo '<div class="unriveio_stats_box">';
      echo '<h2>Today\'s Visitor Summary</h2><hr />';
      echo '<div class="unriveio_row">
      <div class="unriveo_column">
          <div class="unriveio_stats_box"><span class="unriveio_daily">'.esc_html(number_format(Unriveio_Utils::api_property($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=visitors&from='.date('Y-m-d').'&to='.date('Y-m-d').'&per_page=100')))).' </span> Visitors</div>
      </div>
      <div class="unriveio_column">
          <div class="unriveio_stats_box"><span class="unriveio_daily">'.esc_html(number_format(Unriveio_Utils::api_property($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=pageviews&from='.date('Y-m-d').'&to='.date('Y-m-d').'&per_page=100')))).'</span> Pageviews</div>
      </div>
    </div>
    </div>';
}

}

/**
 * 
 */
function unriveio_dashboard_widget_check() {

      $error_count = 0;

      if (!extension_loaded('openssl')) {
        $error= new WP_Error( 'Unrive.io Error', __( "OpenSSL module is required for this plugin, please install or contact your webhost.", "" ) );
        echo '<div class="unriveio_error">'.esc_html($error->get_error_message()).'</div>';
        $error_count++;
      }

      if(!isset(Unriveio_Wp_Remote_Static::request('stats/'.get_option('unriveio_website_id').'?name=visitors&from='.date("Y-m-d").'&to='.date("Y-m-d").'&per_page=1')['data'])) {
        $error= new WP_Error( 'Unrive.io Error', __( "Error With API, Please check your API key and ensure you have a website profile created in your unrive.io account.", "" ) );
        echo'<div class="unriveio_error">'.esc_html($error->get_error_message()).'</div>';
        $error_count++;
      }

      if($error_count==0) {
         if (!Unriveio_Admin::is_default_website()) {
        Unriveio_Admin::set_default_website();
      }
      } 

      return $error_count;
}

/**
 * Generates the dashboard widget
 */
function Unriveio_Visits_Summary() {

  if(unriveio_dashboard_widget_check() == 0) {
    $wp_remote_wrapper = new Unriveio_Wp_Remote_Wrapper();
    echo '<div class="unriveio_stats_box_">';
    echo '<div class="unriveio_row_">
      <div class="unriveio_column_">
          <div class="unriveio_stats_box_">
          <span class="unriveio_daily_">Today your website has received <b>'.esc_html(absint(Unriveio_Utils::api_property( $wp_remote_wrapper->request('stats/'.get_option('unriveio_website_id').'?name=visitors&from='.date('Y-m-d').'&to='.date('Y-m-d').'&per_page=1')))).'</b></span> Visitor(s) 
          and <b>'.esc_html(absint(Unriveio_Utils::api_property( $wp_remote_wrapper->request('stats/'.get_option('unriveio_website_id').'?name=pageviews&from='.date('Y-m-d').'&to='.date('Y-m-d').'&per_page=1')))).'</b></span> Pageview(s).
          </div>
      </div>
  </div></div>';
  } else {
    echo '<p>Stats cannot be display, please see Unrive.io plugin for configuration.';
  }
}

/**
 * Loads the summary widget on summary page
 */
function Unriveio_Visits_load_widget() {
    register_widget( 'Unriveio_Visits' );
}

add_action( 'widgets_init', 'Unriveio_Visits_load_widget' );

/**
 * Adds the dashboard widget
 */
function unriveio_add_dashboard_widgets() {
	wp_add_dashboard_widget( 'Unriveio_Visits_dashboard_widget', esc_html__( 'Unrive.io Visitor Summary', 'wporg' ), 'Unriveio_Visits_Summary');
}

add_action( 'wp_dashboard_setup', 'unriveio_add_dashboard_widgets' );