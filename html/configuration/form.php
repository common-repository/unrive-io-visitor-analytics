<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Configuration Form HTML
 */

function unriveio_configuration_form() { 

$sanitized_url = esc_url( admin_url( 'admin-post.php' ) );

echo '<div id="wpbody" role="main">
                    
<div class="wrap">
<h1>Unrive.io Configuration</h1>

<form method="post" action="'.$sanitized_url.'?action=unriveio_api_key" novalidate="novalidate">
<input type="hidden" name="add_unriveio_api_key_validation_form" value="'.esc_attr(wp_create_nonce( 'add_unriveio_api_key_validation' )).'" />
<input type="hidden" name="action" value="unriveio_api_key" />
<table class="form-table" role="presentation">

<tbody>

<tr>
<th scope="row"><label for="blogdescription">API Key</label></th>
<td><input name="apikey" type="text" id="apikey" aria-describedby="apikey" value="'.esc_attr(Unriveio_Admin::get_api_key(get_option('unriveio_api_key'))).'" class="regular-text" placeholder="Enter your API Key">
<p class="description" id="tagline-description">You can find your API key in the account section within your unrive.io account. <a href="https://unrive.io/account/api">Get your API Key</a></p></td>
</tr>

<tr>
<th scope="row"><label for="blogdescription">Encrypt Key?</label></th>
<td><select name="encrypt" id="encrypt">
<option value="yes">Yes, encrypt (Recommended)</option>
<option value="no">No, store without encrypting</option>
</select>
<input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
<p class="description" id="tagline-description">If you have openssl installed on your server, you can encrypt the API Key while in storage, your auth salt will be used, please keep it unique. Storing your API key without encryption is a security risk (Recommended). Other plugins which use auth salt also have access to this key. This API Key stored in the database isn\'t deleted on deactivation or deletion of this plugin. This will be added in future versions.</p></td>
</tr>

</tbody>
</table>
</form>
</div>
</div>';

}