<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Most_Read_Days extends WP_Widget {

private $wp_remote;

/**
 * Most Read Constructor
 */
  function __construct() {
    parent::__construct('unriveio_Visits',__('Visitor Summary', 'text_domain'), array( 'description' => __( 'Displays a summary of visitors', 'text_domain' ) ));
    $this->wp_remote = new Unriveio_Wp_Remote_Wrapper();
    $this->iterator = new  Unriveio_Count_Iterator();   
  }

  function render(){
        $table = new Unriveio_DataTable();
        $table->table_data();
        $table->set_data($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=landing_page&from='.date("Y-m-d", strtotime("-29 days")).'&to='.date("Y-m-d").'&per_page=100')['data']
                        ,array('source' => 'Source','visits' => 'Pageviews'));
        $table->prepare_items();
        $table->display();
  }

/**
 * Creates the Widget - 30 days
 */
  function widget($args, $instance ) {
    echo '<div class="unriveio_stats_box">
          <h2>30 Day Most Read</h2><hr />';   
    $this->render();
    echo '</div>';

  }
}

//Visitor summary Page
function Unriveio_Most_Read_Days_load_widget() {
    register_widget( 'Unriveio_Most_Read_Days' );
}

add_action( 'widgets_init', 'Unriveio_Most_Read_Days_load_widget' );