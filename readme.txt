=== Zota for WooCommerce ===
Tags: woocommerce, payments, alternative payments, asia payments
Requires at least: 4.7
Tested up to: 5.7.1
Requires PHP: 7.2
Stable tag: 1.1.4
License: Apache 2.0
License URI: https://github.com/zotapay/zota-woocommerce/blob/master/LICENSE

Widest range of global and local payment solutions available today. Connected to more than 500 banks, acquirers, e-Wallets, PSPs. Multi-currency.

== Description ==

Zota for WooCommerce is complete and secure solution for alternative payment systems with WooCommerce. With Zota for WooCommerce your customers enjoy payments with Zotapay supporting multiple currencies and payment methods. See [Zotapay](https://zotapay.com)'s website and our [Developer's Portal](https://developers.zotapay.com/).

=== Benefits and possibilities of using zota for woocommerce plugin ===

* Accept Payments locally and globally: Zotapay offers merchants the widest range of global and local payment solutions available today.
* We are connected to more than 500 banks, acquirers, e-Wallets, PSPs
* Multi-currency support
* Europe, South East Asia, China, Africa, Japan, Latin America and more.

**Increase your market reach and meet the payment needs of your clients worldwide:**

* Local Payment Methods & E-wallet
* Credit & Debit Card Solutions
* Online Bank & Wire Transfers
* Plug ‘N’ Pay

=== Supported industries ===

We work with Low to High risk merchants in the industries:

* [Supported Industries](https://zotapay.com/wp-content/uploads/2021/03/Zotapay-Supported-Industries.pdf)
* [Prohibited Industries](https://zotapay.com/wp-content/uploads/2021/03/Zotapay-Prohibited-Industries.pdf)

== Instalation ==

=== Install from within WordPress ===

1. Visit the plugins page within your dashboard and select ‘Add New’;
2. Search for ‘Zota for wooCommerce’;
3. Activate Zota for wooCommerce from your Plugins page;
4. Go to ‘after activation’ below.

=== Install manually ===

1. Upload the ‘zota-for-woocommerce’ folder to the /wp-content/plugins/ directory;
2. Activate the Zota for wooCommerce plugin through the ‘Plugins’ menu in WordPress;
3. Go to ‘after activation’ below.

=== After activation ===

After activation, click on plugin's Settings button near Deactivate in the Plugins list page. Alternatively, go to WooCommerce -> Settings -> Zotapay to setup your credentials, set test mode and add endpoints. Then go to WooCommerce -> Settings -> Payments to activate Zotapay for WooCommerce payment gateways.

== Frequently Asked Questions ==

=== What is Zota for WooCommerce?
Payment gateway to Zotapay for WooCommerce that allows you to accept alternative payments from all over the world.

=== Is Zotapay account required?
Yes! Zotapay account required to receive the needed credentials for Zotapay. In order to sign up, please contact [Zotapay Sales](https://zotapay.com/contact/).

=== Is test/sandbox mode available?
Yes! Test/sandbox mode is available. Credentials for Zotapay sandbox are required.

=== How to get started?
Download, install and activate Zotapay for WooCommerce from Plugins page in WordPress administration.

=== How do I find the Settings button?
After activation, click on plugin's Settings button near Deactivate in the Plugins list page. Alternatively, go to WooCommerce -> Settings -> Payments and click on Zotapay for WooCommerce.

=== How do I set up credentials?
On Zota for WooCommerce settings page Enable Zota and fill in the provided credentials.

=== How do I setup multiple payment methods?
ZotaPay can provide multiple Endpoints, which represent different local payment methods. “Add Payment Method” button allows configuring additional Endpoints, add custom names, description and logo of the payment method.

=== Where can I get support?
Zotapay for WooCommerce is supported by Zotapay. For sign-up and sales inquiries, please contact sales@zotapay.com. For Support, please use support@zotapay.com and include customer identifiable information, along with a description of the issue.

== Screenshots ==

1. Sandbox Config Page. Test/sandbox mode is available. Credentials for Zotapay sandbox are required.
2. Download, install and activate Zotapay for WooCommerce from Plugins page in WordPress administration.
3. After activation, click on plugin's Settings button near Deactivate in the Plugins list page.
4. Adding a Payment Method.

== Changelog ==

= 1.1.5 =
* New: Support of partial and overpayments.
* New: Set expired for cancelled orders.

= 1.1.4 =
* Fix: Scheduled order status check.

= 1.1.3 =
* Fix: Github Release action
* Fix: Settings shortcut on plugins page
* Fix: Fix main file renamed to be equal to plugin's slug
* Fix: plugin's main file name in tests bootsrap

= 1.1.2 =
* Fix: Github Release action

= 1.1.1 =
* Fixed logging treshold
* Fixed ZotaPay order ID on multiply payment attempts
* Fixed Scheduled actions
* Added check for multiple callback attempts

= 1.1.0 =
* Added support of multiple payment methods
* Fixed ZotaPay settings link on plugins page
* Fixed column ZotaPay OrderID on orders list

= 1.0.2 =
* Undefined index fix in settings init
* Zotapay OrderID added to order notes on initial request
* Zotapay response status always added to order notes
* Settings page link added to plugins page
* Payment method description updated
* Added column ZotaPay OrderID on orders list with settings control
* Any scheduled cron jobs or actions removed on plugin deactivation

= 1.0.1 =
* Check requirements update
* Billing state fix

= 1.0.0 =
* Initial release
