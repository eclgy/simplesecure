<?php
/**
 * ################################################################################
 * SIMPLESECURE ADMIN/SETTINGS UI
 * ################################################################################
 */

// register the plugin settings menu and scripts
add_action('admin_menu', 'simplesecure_create_menu');
add_action('admin_enqueue_scripts', 'simplesecure_load_admin_scripts');
add_filter("plugin_action_links_simplesecure/simplesecure.php", 'simplesecure_settings_link' );

// activation hooks
// register_activation_hook('simplesecure/simplesecure.php', 'simplesecure_registration' );


/**
 * Settings link that appears on the plugins overview page
 * @param array $links
 * @return array
 */
function simplesecure_settings_link($links) {
	$links[] = '<a href="'. get_admin_url(null, 'options-general.php?page='.__FILE__) .'">Settings</a>';
	return $links;
}

/**
 * Load client-side scripts necessary for settings page
 * @param string $hook
 */
function simplesecure_load_admin_scripts($hook) 
{
	// only load if we are on the simplesecure settings page
	if( strpos($hook, 'simplesecure/settings') !== false )
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('ss-settings-js', plugins_url('simplesecure/scripts/settings.js') );
		
		wp_register_style('font-awesome', plugins_url('simplesecure/styles/font-awesome.min.css') );
		wp_enqueue_style('font-awesome');
		
		wp_register_style('ss-settings-css', plugins_url('simplesecure/styles/settings.css') );
		wp_enqueue_style('ss-settings-css');
	}
}

/**
 * Create the settings menu item in the WordPress admin navigation and
 * link it to the plugin settings page
 */
function simplesecure_create_menu()
{
	// create new menu for site configuration
	add_options_page(__('SimpleSecure Plugin Settings','SimpleSecure'), 'SimpleSecure', 'administrator', __FILE__, 'simplesecure_settings_page');

	// call register settings function
	add_action( 'admin_init', 'simplesecure_register_settings' );
}

/**
 * Register the configuration settings that the plugin will use
 */
function simplesecure_register_settings()
{
	//register our settings
	register_setting( 'simplesecure-settings-group', 'simplesecure_data' );
}

/**
 * Render the settings page by writing directly to stdout.  if multi-site is enabled
 * and simplesecure_override_site is true, then display a notice message that settings
 * are not editable instead of the settings form
 */
function simplesecure_settings_page()
{
	?>
	<div class="wrap simplesecure-settings">
	
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>SimpleSecure Settings</h2>
	
		<div id="simplesecure-header">
		
			<div id="simplesecure-icon-container"><i id="simplesecure-icon" class="icon-lock"></i></div>
			
			<h3>SimpleSecure securely encrypts contact form emails with GPG</h3>
			
			<p>SimpleSecure is a basic contact form plugin with the added feature of encypting the email
			message contents using strong GPG public key encryption.  Standard form processing plugins send
			the email contents in plain text which is not safe for sending sensitive or private information.</p>
			
			<p><em>This plugin is BETA.  Please post feedback or suggestions in the support forum.</em></p>	
			
			<h3>Instructions</h3>
			
			<p>First add at least one GPG key below.  In the Email field enter the address where you would
			like secure messages to be sent.  In the GPG Key field, copy/paste your GPG key in ASCII format.
			One you've at least one key you can place a secure contact form on any page with the following shortcode:</p>
			
			<pre>[simplesecure email="your@email.address"]</pre>
			
			<h3>Support</h3>
			
			<p>The support forum for SimpleSecure is located at <a href="http://wordpress.org/support/plugin/simplesecure">wordpress.org/support/plugin/simplesecure</a></p>
			
			<p>Help with GPG configuration or decryption is at <a href="http://www.gnupg.org/">www.gnupg.org/</a>,
			<a href="http://www.gpg4win.org/">www.gpg4win.org/</a> and <a href="https://gpgtools.org/">gpgtools.org/</a></p>
			
		</div>
	
		<form method="post" action="options.php">

			<?php settings_fields( 'simplesecure-settings-group' ); ?>
			
			<input type="hidden" id="simplesecure_data" name="simplesecure_data" value="<?php echo str_replace('"', '&quot;', get_option('simplesecure_data','[]') ); ?>" />
			
			<h2><i class="icon-lock"></i> GPG Keys</h2>
			
			<div id="simplesecure-settings-content"></div>
			
			<div id="simplesecure-settings-footer">
				<button id="simplesecure-add-key" class="button"><i class="icon-plus-sign"></i> Add New Key</button>
			</div>
			
			<p class="submit"><input type="submit" class="button-primary" value="Save Settings" /></p>
			
		</form>
		
		<p><em>SimpleSecure Version <?php echo SIMPLESECURE_VERSION; ?> by <a href="http://verysimple.com/">Jason Hinkle</a>.  Respect!</em></p>

	</div> <!-- /wrap -->
	<?php
}

?>