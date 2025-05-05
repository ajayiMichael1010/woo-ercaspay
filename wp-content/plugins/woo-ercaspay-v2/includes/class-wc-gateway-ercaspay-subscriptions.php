<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Gateway_ercaspay_Subscriptions
 */
class WC_Gateway_ercaspay_Subscriptions extends WC_Gateway_ercaspay {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {

			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );

		}
	}


	/**
	 * Process a trial subscription order with 0 total.
	 *
	 * @param int $order_id WC Order ID.
	 *
	 * @return array|void
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Check for trial subscription order with 0 total.
		if ( $this->order_contains_subscription( $order ) && $order->get_total() == 0 ) {

			$order->payment_complete();

			$order->add_order_note( __( 'This subscription has a free trial, reason for the 0 amount', 'woo-ercaspay' ) );

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);

		} else {
            return parent::process_payment( $order_id );
		}

	}

	/**
	 * Process a subscription renewal.
	 *
	 * @param float    $amount_to_charge Subscription payment amount.
	 * @param WC_Order $renewal_order Renewal Order.
	 */
	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$response = $this->process_subscription_payment( $renewal_order, $amount_to_charge );

		if ( is_wp_error( $response ) ) {

			$renewal_order->update_status( 'failed', sprintf( __( 'ercaspay Transaction Failed (%s)', 'woo-ercaspay' ), $response->get_error_message() ) );

		}

	}

	/**
	 * Process a subscription renewal payment.
	 *
	 * @param WC_Order $order  Subscription renewal order.
	 * @param float    $amount Subscription payment amount.
	 *
	 * @return bool|WP_Error
	 */
	public function process_subscription_payment( $order, $amount ) {

		$order_id = $order->get_id();

		$ercaspay_token = $order->get_meta( '_ercaspay_token' );

		if ( ! empty( $ercaspay_token ) ) {

			$order_amount = $amount * 100;
			$txnref       = $order_id . '_' . time();

			$order->update_meta_data( '_ercaspay_txn_ref', $txnref );
			$order->save();

			$ercaspay_url = 'https://api.ercaspay.co/transaction/charge_authorization';

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->secret_key,
			);

			$metadata['custom_fields'] = $this->get_custom_fields( $order_id );

			if ( strpos( $ercaspay_token, '###' ) !== false ) {
				$payment_token  = explode( '###', $ercaspay_token );
				$auth_code      = $payment_token[0];
				$customer_email = $payment_token[1];
			} else {
				$auth_code      = $ercaspay_token;
				$customer_email = $order->get_billing_email();
			}

			$body = array(
				'email'              => $customer_email,
				'amount'             => absint( $order_amount ),
				'metadata'           => $metadata,
				'authorization_code' => $auth_code,
				'reference'          => $txnref,
				'currency'           => $order->get_currency(),
			);

			$args = array(
				'body'    => json_encode( $body ),
				'headers' => $headers,
				'timeout' => 60,
			);

			$request = wp_remote_post( $ercaspay_url, $args );

			if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {

				$ercaspay_response = json_decode( wp_remote_retrieve_body( $request ) );

				if ( 'success' == $ercaspay_response->data->status ) {

					$ercaspay_ref = $ercaspay_response->data->reference;

					$order->payment_complete( $ercaspay_ref );

					$message = sprintf( __( 'Payment via ercaspay successful (Transaction Reference: %s)', 'woo-ercaspay' ), $ercaspay_ref );

					$order->add_order_note( $message );

					if ( parent::is_autocomplete_order_enabled( $order ) ) {
						$order->update_status( 'completed' );
					}

					return true;

				} else {

					$gateway_response = __( 'ercaspay payment failed.', 'woo-ercaspay' );

					if ( isset( $ercaspay_response->data->gateway_response ) && ! empty( $ercaspay_response->data->gateway_response ) ) {
						$gateway_response = sprintf( __( 'ercaspay payment failed. Reason: %s', 'woo-ercaspay' ), $ercaspay_response->data->gateway_response );
					}

					return new WP_Error( 'ercaspay_error', $gateway_response );

				}
			}
		}

		return new WP_Error( 'ercaspay_error', __( 'This subscription can&#39;t be renewed automatically. The customer will have to login to their account to renew their subscription', 'woo-ercaspay' ) );

	}

}
