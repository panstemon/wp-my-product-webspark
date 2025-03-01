# WP My Product Webspark

## Description

**WP My Product Webspark** is a custom WooCommerce plugin that extends WooCommerce functionality by adding CRUD operations for products through the "My Account" page. It allows users to add, edit, and manage their products directly from their account.

## Features

- Adds two new pages to WooCommerce "My Account":
  - **Add Product**: A form to create a new product.
  - **My Products**: A list of user-created products with editing and deletion options.
- CRUD functionality for WooCommerce products.
- Products created or edited through the "My Account" page get the status **pending review**.
- Sends an email notification to the admin after product creation or editing.
- Custom email template using WooCommerce's `WC_Email` class.
- Email notification can be enabled/disabled from WooCommerce email settings.
- Supports Ukrainian language (translation provided via `.po` and `.mo` files using Poedit).

## Installation

1. Download or clone this repository.
2. Upload the plugin folder `wp-my-product-webspark` to `/wp-content/plugins/`.
3. Activate the plugin from the WordPress admin panel under **Plugins > Installed Plugins**.
4. Ensure that WooCommerce is installed and activated, as this plugin depends on it.

## Usage

### Add Product Page
1. Navigate to **My Account > Add Product**.
2. Fill in the product details:
   - Product name
   - Price
   - Quantity
   - Description (WYSIWYG editor)
   - Upload an image (only user-uploaded images are displayed in the WP Media Library)
3. Click **Save Product**. The product will be saved with a `pending review` status.

### My Products Page
1. Navigate to **My Account > My Products**.
2. View the list of created products.
3. Use the available actions:
   - Edit product details
   - Delete product
4. Pagination is included for easier navigation.

### Admin Email Notification
After creating or editing a product, an email is sent to the admin containing:
- Product name
- Link to the author's profile in the admin panel
- Link to edit the product in the admin panel

### Enabling/Disabling Email Notifications
1. Go to **WooCommerce > Settings > Emails**.
2. Locate the custom email notification.
3. Enable or disable it as needed.

## Dependencies
- WordPress **5.8+**
- PHP **8.1+**
- WooCommerce **9.0.0+** (tested up to **9.7.0**)

## Translation
This plugin supports the **Ukrainian** language. Translations are included using `.po` and `.mo` files. If needed, additional translations can be created using Poedit.

## Contributing
If you encounter issues or want to improve the plugin, feel free to create a pull request or submit an issue in the GitHub repository.

## License
This plugin is licensed under the **GPL-2.0+**. See [LICENSE](http://www.gnu.org/licenses/gpl-2.0.txt) for details.

---

**Author:** Bohdan Denysenko  
**GitHub:** [panstemon](https://github.com/panstemon)
