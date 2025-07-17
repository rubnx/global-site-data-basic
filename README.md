# Global Site Data Plugin

A WordPress plugin that allows you to manage global site data fields from a centralized admin page and use them anywhere on your site with simple placeholder syntax.

## Features

- **Centralized Management**: Manage all your global site data from one admin page
- **Simple Placeholder System**: Use `{{global.field_name}}` anywhere on your site
- **Automatic Replacement**: Placeholders are automatically replaced in block content
- **Security First**: Built with WordPress security best practices (nonces, sanitization)
- **Easy Integration**: Works with any theme or page builder

## Installation

1. Upload `global-site-data-basic.php` to your `/wp-content/plugins/` directory
2. Go to **Plugins** in your WordPress admin
3. Find "Global Site Data" and click **Activate**
4. Navigate to "Global Site Data" in your admin menu to configure

## Usage

### Managing Global Data

1. Go to **Global Site Data** in your WordPress admin menu
2. Fill in your company information:
   - Company Name
   - Company Email
   - Company Email (Alternative)
   - Company Phone
   - Company Address
   - Website URL
3. Click "Save Settings"

### Using Global Data on Your Site

Use the placeholder syntax `{{global.field_name}}` anywhere on your site:

```
Contact us at {{global.company_email}}
Call us: {{global.company_phone}}
Visit us at: {{global.company_address}}
```

### Available Fields

| Field Name | Placeholder | Description |
|------------|-------------|-------------|
| Company Name | `{{global.company_name}}` | Your company's name |
| Company Email | `{{global.company_email}}` | Primary email address |
| Alternative Email | `{{global.company_email_alt}}` | Secondary email address |
| Company Phone | `{{global.company_phone}}` | Phone number |
| Company Address | `{{global.company_address}}` | Physical address |
| Website URL | `{{global.website_url}}` | Your website URL |

## How It Works

The plugin hooks into WordPress's block rendering system and automatically scans for placeholder patterns. When it finds `{{global.field_name}}`, it replaces it with the corresponding value from your global data settings.

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Development Status

This is currently a prototype/experimental plugin. Features planned for future versions:

- Dynamic field management (add custom fields)
- Field type options (text, email, URL, number, etc.)
- Import/export functionality
- Enhanced UI styling
- Field validation rules
- Conditional field display

## Security

The plugin follows WordPress security best practices:
- Nonce verification for form submissions
- Proper data sanitization and escaping
- Capability checks for admin access
- SQL injection prevention

## Support

This is an experimental plugin. Use at your own risk in production environments.

## License

This plugin is provided as-is for educational and development purposes.