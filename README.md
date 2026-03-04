# WordPress Amazon Price Updater

Experimental WordPress plugin that retrieves Amazon Product Advertising API (PA-API) pricing and displays it on a page using a shortcode.

The plugin fetches pricing in the background and caches results to avoid slowing down page loads.

Status: Experimental / untested proof-of-concept.  
This was primarily created as a quick “vibe coded” experiment and a convenient way to move the code onto my server. I have not yet done full testing or production hardening.

---

## Features

- WordPress shortcode for embedding Amazon prices
- Background refresh using WP-Cron
- Cached price storage via WordPress transients
- Admin settings page for API credentials
- Verification button to confirm API configuration
- Uses Amazon PA-API GetItems endpoint

Example shortcode:

    [amz_price asin="B00X4WHP5E"]

On first page load the plugin schedules a background refresh.  
After the refresh runs, subsequent page loads will display the cached price.

---

## How It Works

1. A page containing `[amz_price asin="..."]` is rendered.
2. The shortcode checks for a cached price in WordPress transients.
3. If no cached value exists:
   - A background job is scheduled using WP-Cron.
   - A placeholder is displayed.
4. The background job calls Amazon PA-API and stores the result.
5. Subsequent page views display the cached price.

---

## Installation

Copy the plugin folder into your WordPress plugins directory:

    wp-content/plugins/wordpress-amazon-price-updater

Activate the plugin from the WordPress admin panel.

---

## Configuration

### 1. Add your Secret Key to wp-config.php

    define('AMZPU_SECRET_KEY', 'YOUR_SECRET_KEY');

### 2. Enter the remaining settings

Go to:

    Settings → AMZ Price Updater

Enter:

- Amazon Access Key
- Amazon Associate Partner Tag

---

## Verify Configuration

The settings page includes a Verify Configuration button which performs a test PA-API request and reports whether the connection is working.

---

## Security Note

The Amazon Secret Key is intentionally stored in wp-config.php rather than the database so it does not appear in the plugin repository or WordPress admin UI.

---

## Requirements

- WordPress
- Amazon Product Advertising API credentials
- An approved Amazon Associates account

---

## Notes

This project currently exists mainly as a technical experiment and portfolio example demonstrating:

- WordPress shortcode processing
- API integration
- background job scheduling with WP-Cron
- caching using WordPress transients

It should be reviewed and tested before any production use.

---

## License

GPL-2.0-or-later
