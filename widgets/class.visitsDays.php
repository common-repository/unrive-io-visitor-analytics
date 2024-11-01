<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Visits_Days extends WP_Widget {


private $wp_remote;

/**
 * Creates the Widget Titles
 */
function __construct() {
  parent::__construct(
    'unriveio_Visits',
    __('Visitor Summary', 'text_domain'),
    array( 'description' => __( 'Displays a summary of visitors', 'text_domain' ) )
  );
  $this->wp_remote = new Unriveio_Wp_Remote_Wrapper();
  $this->iterator = new  Unriveio_Count_Iterator();
}

/**
 * Generates the widget HTML - 30 Day
 */
public function widget( $args, $instance ) {
    echo '<div class="unriveio_stats_box">';
      echo '<h2>30 Day Visitor Summary</h2><hr />';
      echo '<div class="unriveio_row">
      <div class="unriveio_column">
          <div class="unriveio_stats_box"><span class="unriveio_daily">'.esc_html(Unriveio_Utils::api_property_iterate($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=visitors&from='.date('Y-m-d', strtotime("-29 days")).'&to='.date('Y-m-d').'&per_page=100'))).'</span> Visitors</div>
      </div>
      <div class="unriveio_column">
          <div class="unriveio_stats_box"><span class="unriveio_daily">'.esc_html(Unriveio_Utils::api_property_iterate($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=pageviews&from='.date('Y-m-d', strtotime("-29 days")).'&to='.date('Y-m-d').'&per_page=100'))).'</span> Pageviews</div>
      </div>
    </div>
    </div>';
}

}

/**
 * Loads the summary widget on summary page
 */
function Unriveio_Visits_Days_load_widget() {
    register_widget( 'Unriveio_Visits_Days' );
}

add_action( 'widgets_init', 'Unriveio_Visits_Days_load_widget' );

