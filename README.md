# WordPress Amazon Price Updater

Experimental WordPress plugin that retrieves Amazon Product Advertising API (PA-API) pricing and displays it on a page using a shortcode.

The plugin fetches pricing in the background and caches results to avoid slowing down page loads.

Status: Experimental / untested proof-of-concept.

This project was primarily created as a quick “vibe coded” experiment and as a convenient way to move the code onto my server. It has not yet been fully tested or production hardened. The repository exists mainly to document the approach and make the code easier to deploy and iterate on.

---

## Features

- WordPress shortcode for embedding Amazon prices
- Background refresh using WP-Cron
- Cached price storage via WordPress transients
- Admin settings page for API credentials
- Basic configuration verification
- Amazon Product Advertising API (PA-API) integration
- AWS Signature Version 4 request signing

Example shortcode:

    [amz_price asin="B00X4WHP5E"]

---

## Example Output

The shortcode:

    [amz_price asin="B00X4WHP5E"]

currently renders something similar to:

    $19.95 as of 3/10/2026

If the price has not yet been cached, a temporary placeholder is displayed:

    Price updating…

The background refresh process then retrieves the price using the Amazon Product Advertising API and stores the result using WordPress transients.

---

## How It Works

1. A page containing a shortcode such as:

       [amz_price asin="B00X4WHP5E"]

   is rendered by WordPress.

2. The shortcode handler checks whether a cached value exists using WordPress transients.

3. If a cached price exists, it displays the stored price along with the last time the price was checked.

4. If no cached value exists:
   - A background job is scheduled using WP-Cron
   - A placeholder message is displayed

5. The background job calls the Amazon Product Advertising API (PA-API) and stores the returned price.

6. Future page views display the cached value until the next refresh occurs.

---

## Architecture Overview

The plugin is intentionally simple and organized into a few small components.

    amz-price-updater.php
        Plugin bootstrap and loader

    includes/amz-shortcode.php
        Registers the shortcode and handles caching logic

    includes/admin-settings.php
        Provides a WordPress settings page for configuration

    includes/paapi-client.php
        Handles the Amazon PA-API request and AWS request signing

    includes/amz-config.sample.php
        Example configuration file for credentials

The general execution flow is:

    Shortcode → Cache Check → PA-API Client → Amazon API → Cache Result

This approach avoids slowing down page loads while still keeping prices relatively fresh.

---

## Installation

Copy the plugin folder into the WordPress plugins directory:

    wp-content/plugins/wordpress-amazon-price-updater

Then activate the plugin through the WordPress admin interface.

---

## Configuration

### 1. Add your Amazon Secret Key to wp-config.php

For security reasons, the Amazon secret key is expected to be defined in the WordPress configuration file instead of the database.

Add a line similar to this to wp-config.php:

    define('AMZPU_SECRET_KEY', 'YOUR_SECRET_KEY');

### 2. Configure remaining credentials

Navigate to:

    Settings → AMZ Price Updater

Enter:

- Amazon Access Key
- Amazon Associate Partner Tag

---

## Verify Configuration

The settings page includes a **Verify Configuration** button that performs a basic check to confirm the required credentials exist.

This does not perform a full Amazon API request but helps confirm that the plugin has access to the required values.

---

## Repository Structure

The repository is intentionally small and focused.

    wordpress-amazon-price-updater
    │
    ├── .gitignore
    ├── LICENSE
    ├── README.md
    ├── amz-price-updater.php
    │
    └── includes
        ├── admin-settings.php
        ├── amz-config.sample.php
        ├── amz-shortcode.php
        └── paapi-client.php

A real configuration file such as:

    includes/amz-config.php

should exist only locally and should not be committed to version control.

---

## Security Note

The Amazon Secret Key is intentionally stored in wp-config.php rather than in the WordPress database. This prevents the secret from appearing in the plugin repository or the WordPress admin interface.

---

## Planned Improvements

This project is still an experimental prototype. Possible future improvements include:

- Adding an affiliate product link
- Displaying the Amazon-required pricing disclaimer
- Improved error handling and logging
- Batch refreshing multiple ASINs
- CDN purge integration when cached prices change
- Better validation of API configuration
- Additional shortcode options

Some of these features were intentionally left out in this initial version to keep the prototype simple.

---

## Requirements

- WordPress
- Amazon Product Advertising API credentials
- An approved Amazon Associates account

---

## Notes

This project primarily exists as a technical experiment and portfolio example demonstrating:

- WordPress shortcode processing
- WordPress Settings API usage
- API integration
- AWS Signature Version 4 request signing
- Background processing using WP-Cron
- Caching using WordPress transients

It should be reviewed and tested before any production use.

---

## License

GPL-2.0-or-later
