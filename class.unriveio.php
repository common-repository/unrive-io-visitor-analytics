<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Unriveio {

private static $initiated = false;

/**
 * Init Admin
 */
public static function init() {
    if ( ! self::$initiated ) {
        self::init_hooks();
    }
}

/**
 * Bootstrap the Hooks -
 */
public static function init_hooks() {
  
}

}