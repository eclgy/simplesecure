<?php
/*
Plugin Name: SimpleSecure
Plugin URI: http://verysimple.com/products/simplesecure/
Description: SimpleSecure is a secure contact form plugin that uses GPG to encrypt messages.  Proper!
Version: 0.0.2
Author: VerySimple
Author URI: http://verysimple.com/
License: GPL2
*/

define('SIMPLESECURE_VERSION','0.0.2');
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
add_action('wp_logout', 'simplesecure_end_session');
add_action('wp_login', 'simplesecure_end_session');
add_action('wp_enqueue_scripts', 'simplesecure_load_scripts');

/* keeps track of weather enqueued scripts have been written to the browser */
$simplesecure_styles_were_loaded = false;

/**
 * Fired on initialization.  Allows initialization to occur after page render.
 * Currently this is used only to register the MCE editor button
 */
function simplesecure_init()
{
	// we'll be needed the session for storing tokens
	if (!session_id()) session_start();
}

/**
 * Fired session expires or user logs out
 */
function simplesecure_end_session() {
	session_destroy ();
}

/**
 * Enqueue the scripts and registers stylesheets needed by the plugin, but only 
 * enqueue the stylesheets if simplesecure_do_shortcode() if fired
 * @param string $hook
 */
function simplesecure_load_scripts($hook)
{
	wp_enqueue_script('jquery');
	wp_register_style('font-awesome', plugins_url('simplesecure/styles/font-awesome.min.css') );
	wp_register_style( 'ss-css', plugins_url('simplesecure/styles/simplesecure.css') );
}

/**
 * Generate a new token, store it in the server and return it
 */
function simplesecure_generate_token()
{
	$token = md5(mt_rand(1111111111,9999999999).microtime().mt_rand(1111111111,9999999999));
	$_SESSION['simplesecure_token'] = $token;
	return $token;
}

/**
 * returns true if the given token matches the session.  either way the token is cleared from the session
 * @param string $token
 * @return bool
 */
function simplesecure_validate_token($token)
{
	$valid = array_key_exists('simplesecure_token', $_SESSION) 
		&& $_SESSION['simplesecure_token'] 
		&& $_SESSION['simplesecure_token'] == $token;
	
	unset($_SESSION['simplesecure_token']);
	
	return $valid;
}

/**
 * Process the shortcode and return the html output that will be embeded
 * into the page
 * @param array $params
 * @return string HTML
 */
function simplesecure_do_shortcode($params)
{
	// lazy load the stylesheets so they only appear if the simplesecure shortcode is used
	global $simplesecure_styles_were_loaded;	
	if (!$simplesecure_styles_were_loaded) {
		wp_enqueue_style('font-awesome');
		wp_enqueue_style( 'ss-css' );
		$simplesecure_styles_were_loaded = true;
	}
	
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
 * Displays the RSS feed on the page
 * @param unknown_type $params
 */
function simplesecure_display_form($params)
{
	global $post;
	
	// grab the email and the GPG key, exit if either isn't found
	$email = is_array($params) && array_key_exists('email', $params) ? $params['email'] : '';
	if (!$email) return '<div class="ss-error"><i class="icon-warning-sign"></i> Configuration Error: The shortcode requires an "email" parameter</div>';
	$key = simplesecure_get_key($email);
	if (!$key) return '<div class="ss-error"><i class="icon-warning-sign"></i> Configuration Error: No GPG key was found for the specified email address.</div>';
	
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
	
	$output .= '<form class="simplesecure-form" onsubmit="return ss_validate_form(jQuery);" action="'. get_permalink( $post->ID ) . '" method="post" enctype="multipart/form-data" >';
	$output .= '<input name="ss_action" type="hidden" value="send" />';
	$output .= '<input name="ss_token" type="hidden" value="'. simplesecure_generate_token() .'" />';
	$output .= "<div><label>Name:</label><span><input id='ss_name' name='ss_name' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Email:</label><span><input id='ss_email' name='ss_email' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Subject:</label><span><input id='ss_subject' name='ss_subject' type='text' value='' /></span></div>\n";
	$output .= "<div><label>Message:</label><span><textarea id='ss_message' name='ss_message'></textarea></span></div>\n";
	$output .= "<div class='simplesecure-submit-container'><label></label><span><input type='submit' value='Send Message'></span></div>\n";
	$output .= "</form>\n";
	$output .= "</div>\n";
	
	// TODO: make this a little more slick
	$output .= "<script type='text/javascript'>\n";
	$output .= "function ss_validate_form($) {\n";
	$output .= "if ($('#ss_name').val() == '') {alert('Please enter your name'); return false;}\n";
	$output .= "if ($('#ss_email').val() == '') {alert('Please enter your email'); return false;}\n";
	$output .= "if ($('#ss_subject').val() == '') {alert('Please enter a subject'); return false;}\n";
	$output .= "if ($('#ss_message').val() == '') {alert('Please enter a message'); return false;}\n";
	$output .= "return true;\n";
	$output .= "}\n";
	$output .= "</script>\n";
	
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
	
	// verify our token to prevent re-submitting the form or certain types of abuse
	$token = htmlspecialchars(get_query_var('ss_token'),ENT_NOQUOTES);
	if (!simplesecure_validate_token($token)) return '<div class="ss-error"><i class="icon-warning-sign"></i> Your message was not sent due to a missing or invalid security token</div>';
	
	// grab the email and the GPG key, exit if either isn't found
	$email = is_array($params) && array_key_exists('email', $params) ? $params['email'] : '';
	if (!$email) return '<div class="ss-error"><i class="icon-warning-sign"></i> Configuration Error: The shortcode requires an "email" parameter</div>';
	$key = simplesecure_get_key($email);
	if (!$key) return '<div class="ss-error"><i class="icon-warning-sign"></i> Configuration Error: No GPG key was found for the specified email address.</div>';
	
	
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
