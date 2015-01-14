<?php
/*
Plugin Name: WooCommerce CoinSimple payment gateway
Plugin URI: https://coinsimple.com
Description: CoinSimple plugin for woocommerce
Version: 1.0
Author: CoinSimple
Author URI: https://coinsimple.com
*/

/**
* @author Conformal Systems LLC.
* @copyright Copyright (c) 2014 Conformal Systems LLC. <support@conformal.com>
* @license
* Copyright (c) Conformal Systems LLC. <support@conformal.com>
*
* Permission to use, copy, modify, and distribute this software for any
* purpose with or without fee is hereby granted, provided that the above
* copyright notice and this permission notice appear in all copies.
*
* THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
* WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
* MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
* ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
* WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
* ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
* OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
*/

add_action('plugins_loaded', 'woocommerce_coinsimple_init', 0);

/**
* This function gets called by WooCommerce when an instantiation of coinsimple is required.
*
* @param void
* @return void
*/
function woocommerce_coinsimple_init() {
	if (!class_exists('WC_Payment_Gateway'))
		return;

	// make the coinsimple API available
	require (dirname(__FILE__).'/coinsimple_php/src/coinsimple.php');

	/**
	* The WC_coinsimple class contains all required functionality to proxy WooCommerce payments into coinsimple.
	*/
	class WC_coinsimple extends WC_Payment_Gateway {

		/**
		* Notification callback.
		*
		* @param void
		* @return void
		*/
		public function coinsimple_callback() {
			// obtain body
			@ob_clean();
			$body = file_get_contents('php://input');
			$data = json_decode($body);

			$business = new \CoinSimple\Business($this->get_option("business_id"), $this->get_option('api_key'));

			if (!$business->validateHash($data->hash, $data->timestamp)) {
				$this->debug(__METHOD__, 'invalid callback hash');
				return;
			}

			$order = new WC_Order($data->custom->order_id);
			if (!isset($order->id)) {
				// orderId invalid, try alternate find
				$orderId = wc_get_order_id_by_order_key($data->custom->order_key);
				$order = new WC_Order($orderId);
			}
			if ($order->order_key !== $data->custom->order_key) {
				$this->debug(__METHOD__, 'invalid order key');
				return;
			}
			$order->payment_complete();
		}

		/**
		* WC_coinsimple constructor.
		*
		* @access public
		* @param void
		* @return void
		*/
		public function __construct() {

			$this->id = 'coinsimple';
			$this->icon = plugin_dir_url(__FILE__).'coinsimple.png';
			$this->has_fields = false;
			$this->method_title = 'CoinSimple';
			$this->method_description = 'CoinSimple payment gateway';
			$this->debug = false;

			$this->init_form_fields();
			$this->init_settings();

			$this->title = "CoinSimple";
			$this->order_span_text = __('Proceed to CoinSimple', 'woocommerce');

			// save hook for settings
			add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this,
				'process_admin_options'));

			// enable callback when coinsimple invoice is confirmed
			add_action('woocommerce_api_wc_coinsimple', array($this, 'coinsimple_callback'));
		}

		/**
		* Setup coinsimple settings in WooCommerce "Payment Gateways" tab.
		*
		* @access public
		* @param void
		* @return void
		*/
		public function init_form_fields() {
			$this->form_fields = array(
					// required plugin entries
					'enabled' => array(
						'title'=>__('Enable/disable', 'woocommerce'),
						'type'=>'checkbox',
						'label'=>__('Enable CoinSimple payments for woocommerce', 'woocommerce'),
						'default'=>'yes',
						),
					'notes'=>array(
						'title'=>__('Invoice Notes', 'woocommerce'),
						'type'=>'textarea',
						'placeholder'=>__('Replace this text with your message!', 'woocommerce'),
						'description'=>__('Message customer will see on the invoice', 'woocommerce'),
						'default'=>__('Thank you for using CoinSimple.', 'woocommerce'),
						'desc_tip'=>true,
						),
					'api_key'=>array(
						'title'=>__('API key', 'woocommerce'),
						'type'=>'text',
						'placeholder'=>__('Replace this text with your API key!', 'woocommerce'),
						'description'=>__('The API key can be found on the setting page of a business on CoinSimple.', 'woocommerce'),
						'desc_tip'=>true,
						),
					'redirect_url'=>array(
							'title'=>__('Redirect URL', 'woocommerce'),
							'type'=>'text',
							'placeholder'=>__('Replace this text with the redirect url', 'woocommerce'),
							'description'=>__('This is the page where the customer will be redirected to after payment', 'woocommerce'),
							'default'=> '',
							'desc_tip'=>true,
						),
					'business_id'=>array(
						'title'=>__('Business ID', 'woocommerce'),
						'type'=>'text',
						'placeholder'=>__('Replace this text with your Business ID!', 'woocommerce'),
						'description'=>__('The Business ID can be found on the setting page of a business on CoinSimple.', 'woocommerce'),
						'desc_tip'=>true,
							)
					);
		}

		/**
		* Generate HTML for coinsimple settings in WooCommerce "Payment Gateways" tab.
		*
		* @access public
		* @param void
		* @return void
		*/
		public function admin_options() {
			?>
			<img style="float:right" src="<?php echo plugin_dir_url(__FILE__); ?>coinsimple.png" />
			<h3><?php _e('CoinSimple', 'woocommerce');?></h3>

			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table><!--/.form-table-->
			<?php
		}

		/**
		* Method to create the actual invoice and redirect the customer on success
		*
		* @access public
		* @param string $order_id rendezvous token that identifies a WooCommerce order.
		* @return void
		*/
		public function process_payment($order_id) {
			if (!$this->post_invoice($order_id, $reply)) {
				return;
			}

			return array(
				'result'=>'success',
				'redirect'=> $reply->url
			);
		}

		/**
		* Translate WooCommerce order into coinsimple invoice and POST invoice to coinsimple.com.
		*
		* @access public
		* @todo determine how to handle order statuses in this function.
		* @param string $order_id rendezvous token that identifies a WooCommerce order.
		* @param $reply passed by reference, returned if successful.
		* @return boolean true if successful or false if unsuccessful.
		*/
		public function post_invoice($order_id, &$reply) {
			$wc_order = new WC_Order($order_id);

			$invoice = new \CoinSimple\Invoice();
			$invoice->setName($wc_order->billing_first_name . ' ' . $wc_order->billing_last_name);
			$invoice->setEmail($wc_order->billing_email);
			$invoice->setCurrency(strtolower(get_woocommerce_currency()));

			// create line items
			$wc_items = $wc_order->get_items();
			foreach ($wc_items as $wc_item) {
				if (get_option('woocommerce_prices_include_tax') === 'yes') {
					$line_total = $wc_order->get_line_subtotal($wc_item, true /*tax*/, true /*round*/);
				} else {
					$line_total = $wc_order->get_line_subtotal($wc_item, false /*tax*/, true /*round*/);
				}

				$item = array(
					"description" => $wc_item['name'],
					"quantity" => floatval($wc_item['qty']),
					"price" => round($line_total / $wc_item['qty'], 2)
				);

				$invoice->addItem($item);
			}

			$invoice->setNotes($this->get_option("notes"));

			//tax
			if ($wc_order->get_total_tax() != 0 && get_option('woocommerce_prices_include_tax') !== 'yes') {
				$tax = 0;
				foreach ($wc_order->get_tax_totals() as $value) {
					$tax += $value->amount;
				}
				$tax = round($tax, 2);

				$invoice->addItem(array(
						"description" => "Sales tax",
						"quantity" => 1,
						"price" => $tax
				));
			}

			// shipping
			if ($wc_order->get_total_shipping() != 0) {
				$shipping = $wc_order->get_total_shipping();
				if (get_option('woocommerce_prices_include_tax') === 'yes') {
					$shipping += $wc_order->get_shipping_tax();
				}

				$invoice->addItem(array(
						"description" => "Shipping and handling",
						"quantity" => 1,
						"price" => round($shipping, 2)
				));
			}

			// coupens
			if ($wc_order->get_total_discount() != 0) {
				$invoice->addItem(array(
						"description" => "Discounts",
						"quantity" => 1,
						"price" => -round($wc_order->get_total_discount(), 2)
				));
			}

			// settings part
			$invoice->setCallbackUrl(add_query_arg('wc-api', 'wc_coinsimple', home_url('/')));
			$invoice->setCustom(array(
				"order_id" => $wc_order->id,
				"order_key" => $wc_order->order_key
			));

			if ($this->get_option("redirect_url") != "") {
				$invoice->setRedirectUrl($this->get_option("redirect_url"));
			}

			$business = new \CoinSimple\Business($this->get_option("business_id"), $this->get_option('api_key'));
			$res = $business->sendInvoice($invoice);

			if ($res->status == "ok") {
				$reply = $res;
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	* Add CoinSimple to the list of available gateways in WooCommerce.
	*
	* @access public
	* @param array available gateways.
	* @return void
	*/
	function woocommerce_add_coinsimple_gateway($methods) {
		$methods[] = 'WC_coinsimple';
		return $methods;
	}
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_coinsimple_gateway');
}
