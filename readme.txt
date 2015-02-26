=== Plugin Name ===
Contributors: coinsimple
Tags: bitcoin, coinsimple, woocommerce, e-commerce, ecommerce
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: /trunk/
License: MIT
License URI: http://opensource.org/licenses/MIT

Accept Bitcoin on your WooCommerce-powered website with CoinSimple.

== Description ==

The CoinSimple WooCommerce plugin is an extension of the WooCommerce plugin for Wordpress, and allows e-commerce stores, running on Wordpress and WooCommerce, to accept bitcoin through CoinSimple.

CoinSimple supports BitPay, Coinbase, GoCoin, hierarchical deterministic wallets (Electrum, Armory, Trezor) and regular bitcoin addresses. Sign up for an account at [CoinSimple.com](http://coinsimple.com).

== Installation ==

**Prerequisites**

1. **Installed WooCommerce plugin on your Wordpress site**. (Installation instructions can be found at [www.woothemes.com/woocommerce](http://www.woothemes.com/woocommerce/)).
2. **A Business ID and API key from CoinSimple.com**. To find your information, go to your [CoinSimple.com](http://coinsimple.com) dashboard, select your business, and go to "Settings". You will find your *Business ID* and *API key* under "Busines Information" and "API Credentials".

**Installation**

1. [Download a zip file of the plugin from this link](https://github.com/coinsimple/coinsimple-woocommerce/archive/v1.zip).

2. Log in to your WordPress site as administrator and click on "Plugins"->"Add New"->"Upload"->"Choose file" and select the zip file you downloaded. Click on "Install Now" followed by "Activate Plugin".

3. Log into the WordPress dashboard and click "WooCommerce" and then "Settings".

4. In the screen that appears click on "Checkout" and then "CoinSimple".

5. Enter your CoinSimple.com business ID and API in the appropriate fields. Enter the URL of the page that your customers will be directed to after they make their payment.

6. Click "Save changes".


== Screenshots ==

1. Log into the WordPress dashboard and click "WooCommerce" and then "Settings".

2. In the screen that appears click on "Checkout" and then "CoinSimple".

3. Enter your CoinSimple.com business ID and API in the appropriate fields. Enter the URL of the page that your customers will be directed to after they make their payment.

== Changelog ==

= 1.0 =
* Initial version of the plugin

== Usage ==

- When a buyer selects "Bitcoin" as their payment method, an invoice is generated at CoinSimple.
- After the buyer pays, CoinSimple directs the buyer to the page that you selected in your Settings above.

== CoinSimple Support ==

* If you are having a problem with this plugin, please use [this Github issues page](https://github.com/coinsimple/coinsimple-woocommerce/issues) to report your problem.

== WooCommerce Support ==

* [Homepage](http://www.woothemes.com/woocommerce/)
* [Documentation](http://docs.woothemes.com)
* [Support](https://support.woothemes.com)

== Troubleshooting ==

* This plugin requires PHP 5.4 or higher to function correctly. Contact your webhosting provider or server administrator if you are unsure which version is installed on your web server.
* Ensure a valid SSL certificate is installed on your server. Also ensure your root CA cert is updated. If your CA cert is not current, you will see curl SSL verification errors.
* Verify that your web server is not blocking POSTs from servers it may not recognize. Double check this on your firewall as well, if one is being used.
* Check the system error log file (usually the web server error log) for any errors during CoinSimple payment attempts. If you contact CoinSimple support, they will ask to see the log file to help diagnose the problem.
* If all else fails, send an email describing your issue in detail to [support@coinsimple.com](mailto:support@coinsimple.com). In your support request, please provide:
	* Wordpress version
	* WooCommerce version
	* PHP version
	* Other plugins installed
	* Configuration settings for the CoinSimple plugin
	* Screenshot of error messages, if any== Screenshots ==
