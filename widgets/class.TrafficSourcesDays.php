<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Traffic_Sources_Days extends WP_Widget {

private $wp_remote;

/**
 * Traffic Region Constructor
 */
function __construct() {
  parent::__construct(
    'unriveio_Visits',
    __('Traffic Sources', 'text_domain'),
    array( 'description' => __( 'Traffic Sources', 'text_domain' ) )
  );
  $this->wp_remote = new Unriveio_Wp_Remote_Wrapper();
  $this->iterator = new  Unriveio_Count_Iterator();
}

function render(){
        $table = new Unriveio_DataTable();
        $table->table_data();
        $table->set_data($this->wp_remote->request('stats/'.get_option('unriveio_website_id').'?name=referrer&from='.date("Y-m-d", strtotime("-30 days")).'&to='.date("Y-m-d").'&per_page=100')['data']
                        , array('source' => 'Source','visits' => 'Visitors'));
        $table->prepare_items();
        $table->display();
}

/**
 * Creates the Widget -
 */
public function widget( $args, $instance ) {
      echo '<div class="unriveio_stats_box">
            <h2>30 Day Traffic Sources</h2><hr />';
      $this->render();
      echo '</div>';
    }

}

//Visitor summary Page
function Unriveio_Traffic_Sources_Days_load_widget() {
    register_widget( 'Unriveio_Traffic_Sources_Days' );
}
add_action( 'widgets_init', 'Unriveio_Traffic_Sources_Days_load_widget' );
