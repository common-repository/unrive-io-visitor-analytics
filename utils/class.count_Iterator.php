<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio_Count_Iterator {

/**
 * Calculates the monthly count totals
 */
public function iterate($data) {
    $count = 0;
    foreach($data as $item) {
        $count += $item['count'];
    }

    return $count;
}

}