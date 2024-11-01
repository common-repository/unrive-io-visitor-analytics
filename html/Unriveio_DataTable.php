<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_DataTable extends \WP_List_Table {

    public $data = array();
    public $cols = array();

    public function set_data($array,$array_cols) {
      $this->data = $array;
      $this->cols = $array_cols;
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $perPage = 50;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        return $this->cols;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('visits' => array('visits', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        $data = array();

        foreach($this->data as $item) {

        $data[] = array(
                    'source' => $item['value'],
                    'visits' => absint($item['count']),
                    );
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item - Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'source':
            case 'visits':
            case 'rating':
                return $item[ $column_name ];

            default:
                return '';
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'visits';
        $order = 'desc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = sanitize_title($_GET['orderby']);
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = sanitize_title($_GET['order']);
        }

        $result = strnatcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }

}