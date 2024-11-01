<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(UNRIVEIO__PLUGIN_DIR . 'html/configuration/faq.php' );
require_once(UNRIVEIO__PLUGIN_DIR . 'html/configuration/form.php' );

/**
 * Unriveio.io Headers
 * */
function unriveio_headers() {
   echo  '<h1 class="unriveio_blog_header">Your Blog Visitor Summary</h1><hr />
          <h3 class="unriveio_blog_subheader">A convenient summary of your visitor traffic. For a full breakdown of your visitor traffic, login to your <a href="https://unrive.io/login" target="_blank">unrive.io</a> account</h3>';
}

?>