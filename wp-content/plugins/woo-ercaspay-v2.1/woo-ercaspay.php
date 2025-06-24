<?php
/**
 * Ercas Pay
 *
 * @package           ErcasPay
 * @author            Michael
 * @copyright         2023 ErcasPay
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       ErcasPay Payment Gateway
 * Plugin URI:        https://www.ercaspay.com/
 * Description:       Accept and receive payments from anywhere and anyone in the world using Ercas Woocommerce Payment Gateway.
 * Version:           2.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Michael
 * Author URI:        https://www.ercaspay.com
 * Text Domain:       ercaspay.1.2
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Ercas Pay is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Ercas Pay is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Ercas Pay. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_ERCASPAY_MAIN_FILE', __FILE__ );
define( 'WC_ERCASPAY_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );

define( 'WC_ERCASPAY_VERSION', '1.2.0' );

/**
 * Initialize Ercaspay WooCommerce payment gateway.
 */
function tbz_wc_ercaspay_init() {

	load_plugin_textdomain( 'woo-ercaspay', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices', 'tbz_wc_ercaspay_wc_missing_notice' );
		return;
	}

	add_action( 'admin_init', 'tbz_wc_ercaspay_testmode_notice' );

	require_once __DIR__ . '/includes/class-wc-gateway-ercaspay.php';

	require_once __DIR__ . '/includes/class-wc-gateway-ercaspay-subscriptions.php';

	add_filter( 'woocommerce_payment_gateways', 'tbz_wc_add_ercaspay_gateway', 99 );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'tbz_woo_ercaspay_plugin_action_links' );

}
add_action( 'plugins_loaded', 'tbz_wc_ercaspay_init', 99 );

/**
 * Add Settings link to the plugin entry in the plugins menu.
 *
 * @param array $links Plugin action links.
 *
 * @return array
 **/
function tbz_woo_ercaspay_plugin_action_links( $links ) {

	$settings_link = array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ercaspay' ) . '" title="' . __( 'View ercaspay WooCommerce Settings', 'woo-ercaspay' ) . '">' . __( 'Settings', 'woo-ercaspay' ) . '</a>',
	);

	return array_merge( $settings_link, $links );

}

/**
 * Add ercaspay Gateway to WooCommerce.
 *
 * @param array $methods WooCommerce payment gateways methods.
 *
 * @return array
 */
function tbz_wc_add_ercaspay_gateway( $methods ) {

	if ( class_exists( 'WC_Subscriptions_Order' ) && class_exists( 'WC_Payment_Gateway_CC' ) ) {
		$methods[] = 'WC_Gateway_ercaspay_Subscriptions';
	} else {
		$methods[] = 'WC_Gateway_ercaspay';
	}

	if ( 'NGN' === get_woocommerce_currency() ) {

		$settings        = get_option( 'woocommerce_ercaspay_settings', '' );
		$custom_gateways = isset( $settings['custom_gateways'] ) ? $settings['custom_gateways'] : '';
	}

	return $methods;

}

/**
 * Display a notice if WooCommerce is not installed
 */
function tbz_wc_ercaspay_wc_missing_notice() {
	echo '<div class="error"><p><strong>' . sprintf( __( 'ercaspay requires WooCommerce to be installed and active. Click %s to install WooCommerce.', 'woo-ercaspay' ), '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=539' ) . '" class="thickbox open-plugin-details-modal">here</a>' ) . '</strong></p></div>';
}

/**
 * Display the test mode notice.
 **/
function tbz_wc_ercaspay_testmode_notice() {

	if ( ! class_exists( Notes::class ) ) {
		return;
	}

	if ( ! class_exists( WC_Data_Store::class ) ) {
		return;
	}

	if ( ! method_exists( Notes::class, 'get_note_by_name' ) ) {
		return;
	}

	$test_mode_note = Notes::get_note_by_name( 'ercaspay-test-mode' );

	if ( false !== $test_mode_note ) {
		return;
	}

	$ercaspay_settings = get_option( 'woocommerce_ercaspay_settings' );
	$test_mode         = $ercaspay_settings['testmode'] ?? '';

	if ( 'yes' !== $test_mode ) {
		Notes::delete_notes_with_name( 'ercaspay-test-mode' );

		return;
	}

	$note = new Note();
	$note->set_title( __( 'ercaspay test mode enabled', 'woo-ercaspay' ) );
	$note->set_content( __( 'ercaspay test mode is currently enabled. Remember to disable it when you want to start accepting live payment on your site.', 'woo-ercaspay' ) );
	$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
	$note->set_layout( 'plain' );
	$note->set_is_snoozable( false );
	$note->set_name( 'ercaspay-test-mode' );
	$note->set_source( 'woo-ercaspay' );
	$note->add_action( 'disable-ercaspay-test-mode', __( 'Disable ercaspay test mode', 'woo-ercaspay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ercaspay' ) );
	$note->save();
}

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Registers WooCommerce Blocks integration.
 */
function tbz_wc_gateway_ercaspay_woocommerce_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once __DIR__ . '/includes/class-wc-gateway-ercaspay-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			static function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_ercaspay_Blocks_Support() );
			}
		);
	}
}
add_action( 'woocommerce_blocks_loaded', 'tbz_wc_gateway_ercaspay_woocommerce_block_support' );

