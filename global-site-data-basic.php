<?php
/**
 * Plugin Name: Global Site Data
 * Plugin URI: https://example.com/global-site-data
 * Description: Manage global site data fields from a centralized admin page and use them anywhere with {{global.field_name}} placeholders.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: global-site-data
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/* ************** GLOBAL SITE DATA PAGE FOR GLOBAL FIELDS ************** */
// Create the Global Site Data page in the admin
function global_site_data_page() {
	add_menu_page(
		'Global Site Data',         // Page title
		'Global Site Data',         // Menu title
		'manage_options',           // Capability (only admins can see it)
		'global-site-data',         // Menu slug
		'global_site_data_page_html',  // Function that outputs the HTML
		'dashicons-list-view',      // Icon
		20                          // Position in the menu
	);
}
add_action('admin_menu', 'global_site_data_page');

// Display the HTML for the Global Site Data page
function global_site_data_page_html() {
	if (!current_user_can('manage_options')) {
		return;
	}
	
	// Process form submission if nonce is valid
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if ( !isset($_POST['global_site_data_nonce']) || !wp_verify_nonce($_POST['global_site_data_nonce'], 'global_site_data_update') ) {
			echo '<div class="error"><p>Security check failed. Please try again.</p></div>';
		} else {
			if (isset($_POST['company_email'])) {
				update_option('company_email', sanitize_email($_POST['company_email']));
			}
			if (isset($_POST['company_email_alt'])) {
				update_option('company_email_alt', sanitize_email($_POST['company_email_alt']));
			}
			if (isset($_POST['company_phone'])) {
				update_option('company_phone', sanitize_text_field($_POST['company_phone']));
			}
			if (isset($_POST['company_name'])) {
				update_option('company_name', sanitize_text_field($_POST['company_name']));
			}
			if (isset($_POST['company_address'])) {
				$company_address = str_replace("\n", "<br>", sanitize_textarea_field($_POST['company_address']));
				update_option('company_address', $company_address);
			}
			if (isset($_POST['website_url'])) {
				update_option('website_url', sanitize_text_field($_POST['website_url']));
			}
			echo '<div class="updated"><p>Settings saved.</p></div>';
		}
	}
	
	// Get current values
	$company_email = get_option('company_email', '');
	$company_email_alt = get_option('company_email_alt', '');
	$company_phone = get_option('company_phone', '');
	$company_name = get_option('company_name', '');
	$company_address = get_option('company_address', '');
	$website_url = get_option('website_url', '');
	?>
	<div class="wrap">
		<h2 style="font-size:2em; margin-bottom: 50px;">Global Site Data</h2>
		<form method="POST">
			<?php wp_nonce_field('global_site_data_update', 'global_site_data_nonce'); ?>
			<p>
				<label for="company_name">Company Name:</label>
				<input type="text" name="company_name" value="<?php echo esc_attr($company_name); ?>">
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>company_name</code></span>
			</p>
			<p>
				<label for="company_email">Company Email:</label>
				<input type="email" name="company_email" value="<?php echo esc_attr($company_email); ?>">
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>company_email</code></span>
			</p>
			<p>
				<label for="company_email_alt">Company Email (Alternative):</label>
				<input type="email" name="company_email_alt" value="<?php echo esc_attr($company_email_alt); ?>">
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>company_email_alt</code></span>
			</p>
			<p>
				<label for="company_phone">Company Phone:</label>
				<input type="text" name="company_phone" value="<?php echo esc_attr($company_phone); ?>">
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>company_phone</code></span>
			</p>
			<p>
				<label for="company_address">Company Address:</label><br>
				<textarea name="company_address" rows="4" cols="50"><?php echo esc_textarea(str_replace("<br>", "\n", $company_address)); ?></textarea>
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>company_address</code></span>
			</p>
			<p>
				<label for="website_url">Website URL:</label>
				<input type="text" name="website_url" value="<?php echo esc_attr($website_url); ?>">
				<span style="margin-left: 15px;"><strong>Key:</strong> <code>website_url</code></span>
				<span style="margin-left: 15px;"><strong>Placeholder Tag:</strong> <code>{{global.website_url}}</code></span>
			</p>
			<p>
				<input type="submit" value="Save Settings">
			</p>
		</form>
		<p style="margin-top:50px; font-size:1.2em;">
			<strong>Note:</strong> In order to use these global data fields anywhere on your site, use the format: <code>{{global.key_name}}</code>. For example, to use the company email, write: <code>{{global.company_email}}</code>.
		</p>
	</div>
	<?php
}


/* ************** GLOBAL PLACEHOLDER REPLACEMENT IN BLOCKS **************
 * This section hooks into the block rendering process to scan for and replace 
 * placeholders formatted as {{global.key_name}} with their corresponding option 
 * values. This ensures that global fields work even when used in block attributes.
 */

/**
 * Replace global placeholders with corresponding option values in block HTML.
 *
 * This function scans the rendered HTML output of blocks for placeholders
 * matching the pattern {{global.some_key}} and replaces them with the value 
 * stored in the WordPress options table. If no value is found, the original 
 * placeholder is preserved.
 *
 * @param string $block_content The HTML content of the block.
 * @param array  $block         The block's data array.
 * @return string Modified block content with placeholders replaced.
 */
add_filter( 'render_block', 'replace_global_placeholders_in_block', 10, 2 );
function replace_global_placeholders_in_block( $block_content, $block ) {
    // Only process content if it contains our placeholder pattern.
    if ( strpos( $block_content, '{{global.' ) !== false ) {
        // Replace each instance of the placeholder with its option value.
        $block_content = preg_replace_callback(
            '/{{global\.([^}]+)}}/', // Matches placeholders like {{global.company_email}}.
            function ( $matches ) {
                // Retrieve the option value using the key from the placeholder.
                $value = get_option( $matches[1] );
                // If a value exists, return it; otherwise, return the original placeholder.
                return $value ? $value : $matches[0];
            },
            $block_content
        );
    }
    return $block_content;
}