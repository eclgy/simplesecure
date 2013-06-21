<?php
/*
Plugin Name: SimpleSecure
Plugin URI: http://verysimple.com/products/simplesecure/
Description: SimpleSecure is a secure contact form plugin that uses GPG to encrypt messages.  Proper!
Version: 0.0.1
Author: VerySimple
Author URI: http://verysimple.com/
License: GPL2
*/

define('SIMPLESECURE_VERSION','0.0.1');
define('SIMPLESECURE_SCHEMA_VERSION',1.0);

/**
 * import supporting libraries
 */
include_once(plugin_dir_path(__FILE__).'settings.php');
include_once(plugin_dir_path(__FILE__).'ajax.php');
include_once(plugin_dir_path(__FILE__).'libs/utils.php');

add_shortcode('simplesecure', 'simplesecure_do_shortcode');
add_filter('query_vars', 'simplesecure_queryvars' );

// handle any post-render intialization
add_action('init', 'simplesecure_init');


/**
 * Fired on initialization.  Allows initialization to occur after page render.
 * Currently this is used only to register the MCE editor button
 */
function simplesecure_init()
{

	// TODO register the MCE editor plugin if necessary
// 	if ( current_user_can('edit_posts') || current_user_can('edit_pages') )
// 	{
// 		if ( get_user_option('rich_editing') == 'true' && get_option('simplesecure_enable_editor_button',SIMPLESECURE_DEFAULT_ENABLE_EDITOR_BUTTON))
// 		{
// 			add_filter("mce_external_plugins", "simplesecure_register_mce_plugin");
// 			add_filter('mce_buttons', 'simplesecure_register_mce_buttons');
// 		}
// 	}
}

/**
 * Process the shortcode and return the html output that will be embeded
 * into the page
 * @param array $params
 * @return string HTML
 */
function simplesecure_do_shortcode($params)
{
	$action = get_query_var('ss_action');

	switch($action)
	{
		case "send":
			return simplesecure_send_message($params);
			break;
		default:
			return simplesecure_display_form($params);
	}
}

/**
 * Register the SimpleSecure MCE Editor Plugin
 * @param array $plugin_array
 * @return array
 */
function simplesecure_register_mce_plugin($plugin_array)
{
	$plugin_array['simplesecure'] = plugins_url('/simplesecure/scripts/editor_plugin.js');
	return $plugin_array;
}

/**
 * Add the SimpleSecure button to the MCE Editor
 * @param array $buttons
 * @return array
 */
function simplesecure_register_mce_buttons($buttons)
{
	array_push($buttons, "simplesecureButton");
	return $buttons;
}

/**
 * Displays the RSS feed on the page
 * @param unknown_type $params
 */
function simplesecure_display_form($params)
{
	global $post;
	
	// grab the email and the GPG key, exit if either isn't found
	$email = is_array($params) && array_key_exists('email', $params) ? $params['email'] : '';
	if (!$email) return '<div class="ss-error">Configuration Error: The shortcode requires an "email" parameter</div>';
	$key = simplesecure_get_key($email);
	if (!$key) return '<div class="ss-error">Configuration Error: No GPG key was found for the specified email address.</div>';
	
	if (simplesecure_is_ssl()) {
		$output = "<div class='simplesecure-container simplesecure-secure'>\n";
		$output .= "<h3 class='simplesecure-header'><i class='icon-lock'></i> Secure Contact Form</h3>\n";
		
		$output .= "<div class='ss-success'>This is a secure form.  Your information will
			be encrypted at all times while in transit.</div>\n";
	}
	else {
		$output = "<div class='simplesecure-container simplesecure-insecure'>\n";
		$output .= "<h3 class='simplesecure-header'><i class='icon-unlock'></i> Non-Secure Contact Form</h3>\n";
		
		$output .= "<div class='ss-error'>Warning: You are viewing this page over an insecure connection.  Please
		try changing the URL prefix in your browser from 'http://' to 'https://' to enable SSL.  Otherwise 
		you should avoid sending any private or sensitive information.</div>\n";
	}
	
	$output .= '<form class="simplesecure-form" action="'. get_permalink( $post->ID ) . '" method="post" enctype="multipart/form-data" >';
	$output .= '<input name="ss_action" type="hidden" value="send" />';
	$output .= "<div><label>Name:</label><span><input name='ss_name' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Email:</label><span><input name='ss_email' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Subject:</label><span><input name='ss_subject' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Message:</label><span><textarea name='ss_message'></textarea></span></div>\n";
	$output .= "<div class='simplesecure-submit-container'><label></label><span><input type='submit' value='Send Message'></span></div>\n";
	$output .= "</form>\n";
	$output .= "</div>\n";
	
	return $output;
}

/**
 * Return true if the page is being viewed in SSL mode
 * @return bool
 */
function simplesecure_is_ssl()
{
	return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);
}

/**
 * Returns the public GPG key for the given email
 * @param string $email
 * @return string
 */
function simplesecure_get_key($email)
{
	$json = get_option('simplesecure_data','[]');
	
	$keys = json_decode($json);
	
	foreach ($keys as $key) {
		if ($key->email == $email) return $key->key;
	}
	
	// no matching key found
	return null;
}

/**
 * Submit the candidate application
 * into the page
 * @param array $params
 * @return string HTML
 */
function simplesecure_send_message($params)
{
	global $post;
	
	// grab the email and the GPG key, exit if either isn't found
	$email = is_array($params) && array_key_exists('email', $params) ? $params['email'] : '';
	if (!$email) return '<div class="ss-error">Configuration Error: The shortcode requires an "email" parameter</div>';
	$key = simplesecure_get_key($email);
	if (!$key) return '<div class="ss-error">Configuration Error: No GPG key was found for the specified email address.</div>';
	
	$message = htmlspecialchars(get_query_var('ss_message'),ENT_NOQUOTES);
	$name = htmlspecialchars(get_query_var('ss_name'),ENT_NOQUOTES);
	$email = htmlspecialchars(get_query_var('ss_email'),ENT_NOQUOTES);
	$subject = htmlspecialchars(get_query_var('ss_subject'),ENT_NOQUOTES);
	
	$output = '';
	
	try
	{
		$body = "A secure message was submitted from the form at " . get_permalink($post->ID) . "\n\n";

		$body .= "Subject: " . $subject . "\n";
		$body .= "Name: " . $name . "\n";
		$body .= "Email: " . $email . "\n";
		$body .= "Message: " . $message . "\n";

		// let's do the magic
		require_once 'libs/GPG.php';
		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($key);
		$encrypted = $gpg->encrypt($pub_key,$body);
		
		wp_mail($email, 'Secure Message', $encrypted);

		$output .= "<div class='ss-thankyou'><i class='icon-ok'></i> Thank you.  Your message has been submitted.</div>";
		
		// $output .= "<pre>$encrypted</pre>"; // debugging
	}
	catch (Exception $ex)
	{
		$output .= "<div class='ss-error'>Error sending message: " . htmlspecialchars($ex->getMessage()) . "  Please use the back button to return to the previous page.</div>\n";
		$output .= "<!--\n\n" . htmlentities($ex->getTraceAsString()) . "\n\n-->";
	}

	return $output;
}

/**
 * registration for queryvars used by simplesecure.  this registers any
 * querystring variables that simplesecure requires so that wordpress will
 * process them
 *
 * @param array original array of allowed wordpress query vars
 * @return array $qvars with extra allowed vars added to the array
 */
function simplesecure_queryvars( $qvars )
{
	$qvars[] = 'ss_action';
	$qvars[] = 'ss_subject';
	$qvars[] = 'ss_name';
	$qvars[] = 'ss_email';
	$qvars[] = 'ss_message';
	$qvars[] = 'ss_token';
 	return $qvars;
}
