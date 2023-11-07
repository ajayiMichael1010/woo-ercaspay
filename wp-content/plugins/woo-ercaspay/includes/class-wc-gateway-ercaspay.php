<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Gateway_ErcasPay extends WC_Payment_Gateway_CC {

	/**
	 * Is test mode active?
	 *
	 * @var bool
	 */
	public $testmode;

	/**
	 * Should orders be marked as complete after payment?
	 * 
	 * @var bool
	 */
	public $autocomplete_order;

	/**
	 * Ercaspay payment page type.
	 *
	 * @var string
	 */
	public $payment_page;

	/**
	 * Ercaspay test public key.
	 *
	 * @var string
	 */
	public $test_public_key;

	/**
	 * Ercaspay test secret key.
	 *
	 * @var string
	 */
	public $test_secret_key;

	/**
	 * Ercaspay live public key.
	 *
	 * @var string
	 */
	public $live_public_key;

	/**
	 * Ercaspay live secret key.
	 *
	 * @var string
	 */
	public $live_secret_key;

	/**
	 * Should we save customer cards?
	 *
	 * @var bool
	 */
	public $saved_cards;


	/**
	 * Should the cancel & remove order button be removed on the pay for order page.
	 *
	 * @var bool
	 */
	public $remove_cancel_order_button;


	/**
	 * Who bears Ercaspay charges?
	 *
	 * @var string
	 */
	public $charges_account;

	/**
	 * A flat fee to charge the sub account for each transaction.
	 *
	 * @var string
	 */
	public $transaction_charges;

	/**
	 * Should custom metadata be enabled?
	 *
	 * @var bool
	 */
	public $custom_metadata;

	/**
	 * Should the order id be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_order_id;

	/**
	 * Should the customer name be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_name;

	/**
	 * Should the billing email be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_email;

	/**
	 * Should the billing phone be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_phone;

	/**
	 * Should the billing address be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_billing_address;

	/**
	 * Should the shipping address be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_shipping_address;

	/**
	 * Should the order items be sent as a custom metadata to Ercaspay?
	 *
	 * @var bool
	 */
	public $meta_products;

	/**
	 * API public key
	 *
	 * @var string
	 */
	public $public_key;

	/**
	 * API secret key
	 *
	 * @var string
	 */
	public $secret_key;

	/**
	 * Gateway disabled message
	 *
	 * @var string
	 */
	public $msg;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'ercaspay';
		$this->method_title       = __( 'ErcasPay', 'woo-ercaspay' );
		$this->method_description = sprintf( __( 'ErcasPay provide merchants with the tools and services needed to accept online payments from local and international customers using Mastercard, Visa, Verve Cards and Bank Accounts. <a href="%1$s" target="_blank">Sign up</a> for a Ercaspay account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'woo-ercaspay' ), 'https://merchant.ercaspay.com/auth/register', 'https://merchant.ercaspay.com/' );
		$this->has_fields         = true;

		$this->payment_page = $this->get_option( 'payment_page' );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Get setting values

		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->testmode           = $this->get_option( 'testmode' ) === 'yes' ? true : false;
		$this->autocomplete_order = $this->get_option( 'autocomplete_order' ) === 'yes' ? true : false;

		$this->test_public_key = $this->get_option( 'test_public_key' );
		$this->test_secret_key = $this->get_option( 'test_secret_key' );

		$this->live_public_key = $this->get_option( 'live_public_key' );
		$this->live_secret_key = $this->get_option( 'live_secret_key' );

		$this->saved_cards = $this->get_option( 'saved_cards' ) === 'yes' ? true : false;

        $this->remove_cancel_order_button = $this->get_option( 'remove_cancel_order_button' ) === 'yes' ? true : false;

		$this->custom_metadata = $this->get_option( 'custom_metadata' ) === 'yes' ? true : false;

		$this->meta_order_id         = $this->get_option( 'meta_order_id' ) === 'yes' ? true : false;
		$this->meta_name             = $this->get_option( 'meta_name' ) === 'yes' ? true : false;
		$this->meta_email            = $this->get_option( 'meta_email' ) === 'yes' ? true : false;
		$this->meta_phone            = $this->get_option( 'meta_phone' ) === 'yes' ? true : false;
		$this->meta_billing_address  = $this->get_option( 'meta_billing_address' ) === 'yes' ? true : false;
		$this->meta_shipping_address = $this->get_option( 'meta_shipping_address' ) === 'yes' ? true : false;
		$this->meta_products         = $this->get_option( 'meta_products' ) === 'yes' ? true : false;

		$this->public_key = $this->testmode ? $this->test_public_key : $this->live_public_key;
		$this->secret_key = $this->testmode ? $this->test_secret_key : $this->live_secret_key;

		// Hooks
		//add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		//add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

        // Payment listener/API hook.
        add_action( 'woocommerce_api_wc_gateway_ercaspay', array( $this, 'verify_ercaspay_transaction') );


		// Check if the gateway can be used.
		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}

	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 */
	public function is_valid_for_use() {

		if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_ercaspay_supported_currencies', array( 'NGN', 'USD', 'ZAR', 'GHS', 'KES', 'XOF', 'EGP' ) ) ) ) {

			$this->msg = sprintf( __( 'ErcasPay does not support your store currency. Kindly set it to either NGN (&#8358), GHS (&#x20b5;), USD (&#36;), KES (KSh), ZAR (R), XOF (CFA), or EGP (E£) <a href="%s">here</a>', 'woo-ercaspay' ), admin_url( 'admin.php?page=wc-settings&tab=general' ) );

			return false;

		}

		return true;

	}

	/**
	 * Display ercaspayicon payment icon.
	 */
	public function get_icon() {

		$base_location = wc_get_base_location();

        $icon = '<img src="' . WC_HTTPS::force_https_url( plugins_url( 'assets/images/Ercas-badge-cards-ngn-1.1.png', WC_ERCASPAY_MAIN_FILE ) ) . '" alt="ErcasPay Payment Options" />';

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );

	}

	/**
	 * Check if Ercaspay merchant details is filled.
	 */
	public function admin_notices() {

		if ( $this->enabled == 'no' ) {
			return;
		}

		// Check required fields.
		if ( ! ( $this->public_key && $this->secret_key ) ) {
			echo '<div class="error"><p>' . sprintf( __( 'Please enter your Ercas merchant details <a href="%s">here</a> to be able to use the Ercaspay WooCommerce plugin.', 'woo-ercaspay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ercaspay' ) ) . '</p></div>';
			return;
		}

	}

	/**
	 * Check if Ercaspay gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available() {

		if ( 'yes' == $this->enabled ) {

			if ( ! ( $this->public_key && $this->secret_key ) ) {

				return false;

			}

			return true;

		}

		return false;

	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options() {

		?>

		<h2><?php _e( 'ErcasPay', 'woo-ercaspay' ); ?>
		<?php
		if ( function_exists( 'wc_back_link' ) ) {
			wc_back_link( __( 'Return to payments', 'woo-ercaspay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) );
		}
		?>
		</h2>

		<?php

		if ( $this->is_valid_for_use() ) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';

		} else {
			?>
			<div class="inline error"><p><strong><?php _e( 'ErcasPay Payment Gateway Disabled', 'woo-ercaspay' ); ?></strong>: <?php echo $this->msg; ?></p></div>

			<?php
		}

	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$form_fields = array(
			'enabled'                          => array(
				'title'       => __( 'Enable/Disable', 'woo-ercaspay' ),
				'label'       => __( 'Enable ErcasPay', 'woo-ercaspay' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable ErcasPay as a payment option on the checkout page.', 'woo-ercaspay' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __( 'Title', 'woo-ercaspay' ),
				'type'        => 'text',
				'description' => __( 'This controls the payment method title which the user sees during checkout.', 'woo-ercaspay' ),
				'default'     => __( 'ErcasPay', 'woo-ercaspay' ),
				'desc_tip'    => true,
			),
			'description'                      => array(
				'title'       => __( 'Description', 'woo-ercaspay' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the payment method description which the user sees during checkout.', 'woo-ercaspay' ),
				'default'     => __( 'Make payment using your debit and credit cards', 'woo-ercaspay' ),
				'desc_tip'    => true,
			),
			'testmode'                         => array(
				'title'       => __( 'Test mode', 'woo-ercaspay' ),
				'label'       => __( 'Enable Test Mode', 'woo-ercaspay' ),
				'type'        => 'checkbox',
				'description' => __( 'Test mode enables you to test payments before going live. <br />Once the LIVE MODE is enabled on your Ercaspay account uncheck this.', 'woo-ercaspay' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'test_secret_key'                  => array(
				'title'       => __( 'Test Secret Key', 'woo-ercaspay' ),
				'type'        => 'password',
				'description' => __( 'Enter your Test Secret Key here', 'woo-ercaspay' ),
				'default'     => '',
			),
			'test_public_key'                  => array(
				'title'       => __( 'Test Public Key', 'woo-ercaspay' ),
				'type'        => 'text',
				'description' => __( 'Enter your Test Public Key here.', 'woo-ercaspay' ),
				'default'     => '',
			),
			'live_secret_key'                  => array(
				'title'       => __( 'Live Secret Key', 'woo-ercaspay' ),
				'type'        => 'password',
				'description' => __( 'Enter your Live Secret Key here.', 'woo-ercaspay' ),
				'default'     => '',
			),
			'live_public_key'                  => array(
				'title'       => __( 'Live Public Key', 'woo-ercaspay' ),
				'type'        => 'text',
				'description' => __( 'Enter your Live Public Key here.', 'woo-ercaspay' ),
				'default'     => '',
			),
			'remove_cancel_order_button'       => array(
				'title'       => __( 'Remove Cancel Order & Restore Cart Button', 'woo-ercaspay' ),
				'label'       => __( 'Remove the cancel order & restore cart button on the pay for order page', 'woo-ercaspay' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),

		);

		if ( 'NGN' !== get_woocommerce_currency() ) {
			unset( $form_fields['custom_gateways'] );
		}

		$this->form_fields = $form_fields;

	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {

		if ( $this->description ) {
			echo wpautop( wptexturize( $this->description ) );
		}
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id
	 *
	 * @return array|void
	 */
	public function process_payment( $order_id ) {
        return $this->initiate_checkout($order_id);
	}


    private function initiate_checkout($order_id){
        //$access_token = $this->generate_access_token();

        $headers = array(
            'Authorization' => "Bearer ". $this->secret_key,
            'Content-Type'  => 'application/json',
        );

        $ercas_data = [];

        $order        = wc_get_order( $order_id );
        $amount       = $order->get_total();
        $txnref       = $order_id . '_' . time();
        $callback_url = WC()->api_request_url('WC_Gateway_ErcasPay');

        $ercas_data["amount"] = $amount;
        $ercas_data["currency"] = "NGN";
        $ercas_data["paymentReference"] = $txnref;
        $ercas_data["paymentMethods"] = "card,bank-transfer";
        $ercas_data["customerName"] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $ercas_data["redirectUrl"] = $callback_url;
        $ercas_data["customerEmail"] = "ola@gmail.com";
        $ercas_data["description"] = "Payment for customer order";
        $ercas_data["metadata"]["firstname"] = $order->get_billing_first_name();
        $ercas_data["metadata"]["lastname"] = $order->get_billing_last_name();
        $ercas_data["metadata"]["email"] = $order->get_billing_email();

        $args = array(
            'headers' => $headers,
            'timeout' => 60,
            'body'    => json_encode( $ercas_data ),
        );

        $checkout_endpoint = "https://gw.ercaspay.com/api/v1/payment/initiate";

        $checkout_request = wp_remote_post( $checkout_endpoint, $args );

        if ( ! is_wp_error( $checkout_request )  ) {

            $checkout_response = json_decode( wp_remote_retrieve_body( $checkout_request ) );

			return array(
				'result'   => 'success',
				'redirect' => $checkout_response->responseBody->checkoutUrl
			);

        } else {
            wc_add_notice( __( 'Unable to process payment try again', 'woo-ercaspay' ), 'error' );

            return ;
        }
    }


    /**
     * Verify Ercaspay payment.
     */
    public function verify_ercaspay_transaction() {

        if ( isset( $_REQUEST['reference'] ) && isset($_REQUEST['status']) ) {
            $reference = sanitize_text_field( $_REQUEST['reference'] );
            $status = sanitize_text_field($_REQUEST['status']);
        } else {
            $reference = false;
            $status = false;
        }

        @ob_clean();

        if ( $reference  && $status) {

            $order_details = explode("_", $reference);
            $order_id = $order_details[0];

            $order = wc_get_order($order_id);

            $order->payment_complete( $reference );
            $order->add_order_note( sprintf( __( 'Payment via Ercaspay is successful (Transaction Reference: %s)', 'woo-ercaspay' ), $reference ) );

            if($status ==="PAID"){
                $order->update_status( 'processing' );

                $order->save();

                WC()->cart->empty_cart();

				wp_redirect( $this->get_return_url( $order ) );
            }
			elseif ($status === "CANCELLED"){
				$order->update_status( 'cancelled' );
				wp_redirect( wc_get_page_permalink( 'cart' ) );
			}
			else{
				$order->update_status( 'failed' );
				wp_redirect( wc_get_page_permalink( 'cart' ) );
			}

        }

        exit;

    }


//	/**
//	 * Displays the payment page.
//	 *
//	 * @param $order_id
//	 */
//	public function receipt_page( $order_id ) {
//
//		$order = wc_get_order( $order_id );
//
//		echo '<div id="wc-ercaspay-form">';
//
//		echo '<p>' . __( 'Thank you for your order, please click the button below to pay with Ercaspay.', 'woo-ercaspay' ) . '</p>';
//
//		echo '<div id="ercaspay_form"><form id="order_review" method="post" action="' . WC()->api_request_url('WC_Gateway_ErcasPay') . '"></form><button class="button" id="ercaspay-payment-button">' . __( 'Pay Now', 'woo-ercaspay' ) . '</button>';
//
//		if ( ! $this->remove_cancel_order_button ) {
//			echo '  <a class="button cancel" id="ercaspay-cancel-payment-button" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . __( 'Cancel order &amp; restore cart', 'woo-ercaspay' ) . '</a></div>';
//		}
//
//		echo '</div>';
//
//	}
}
