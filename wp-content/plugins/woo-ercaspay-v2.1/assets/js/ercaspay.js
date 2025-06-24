jQuery( function( $ ) {

	let ercaspay_submit = false;

	$( '#wc-ercaspay-form' ).hide();

	wcercaspayFormHandler();

	jQuery( '#ercaspay-payment-button' ).click( function() {
		return wcercaspayFormHandler();
	} );

	jQuery( '#ercaspay_form form#order_review' ).submit( function() {
		return wcercaspayFormHandler();
	} );

	function wcercaspayCustomFields() {

		let custom_fields = [
			{
				"display_name": "Plugin",
				"variable_name": "plugin",
				"value": "woo-ercaspay"
			}
		];

		if ( wc_ercaspay_params.meta_order_id ) {

			custom_fields.push( {
				display_name: "Order ID",
				variable_name: "order_id",
				value: wc_ercaspay_params.meta_order_id
			} );

		}

		if ( wc_ercaspay_params.meta_name ) {

			custom_fields.push( {
				display_name: "Customer Name",
				variable_name: "customer_name",
				value: wc_ercaspay_params.meta_name
			} );
		}

		if ( wc_ercaspay_params.meta_email ) {

			custom_fields.push( {
				display_name: "Customer Email",
				variable_name: "customer_email",
				value: wc_ercaspay_params.meta_email
			} );
		}

		if ( wc_ercaspay_params.meta_phone ) {

			custom_fields.push( {
				display_name: "Customer Phone",
				variable_name: "customer_phone",
				value: wc_ercaspay_params.meta_phone
			} );
		}

		if ( wc_ercaspay_params.meta_billing_address ) {

			custom_fields.push( {
				display_name: "Billing Address",
				variable_name: "billing_address",
				value: wc_ercaspay_params.meta_billing_address
			} );
		}

		if ( wc_ercaspay_params.meta_shipping_address ) {

			custom_fields.push( {
				display_name: "Shipping Address",
				variable_name: "shipping_address",
				value: wc_ercaspay_params.meta_shipping_address
			} );
		}

		if ( wc_ercaspay_params.meta_products ) {

			custom_fields.push( {
				display_name: "Products",
				variable_name: "products",
				value: wc_ercaspay_params.meta_products
			} );
		}

		return custom_fields;
	}

	function wcercaspayCustomFilters() {

		let custom_filters = {};

		if ( wc_ercaspay_params.card_channel ) {

			if ( wc_ercaspay_params.banks_allowed ) {

				custom_filters[ 'banks' ] = wc_ercaspay_params.banks_allowed;

			}

			if ( wc_ercaspay_params.cards_allowed ) {

				custom_filters[ 'card_brands' ] = wc_ercaspay_params.cards_allowed;
			}

		}

		return custom_filters;
	}

	function wcPaymentChannels() {

		let payment_channels = [];

		if ( wc_ercaspay_params.bank_channel ) {
			payment_channels.push( 'bank' );
		}

		if ( wc_ercaspay_params.card_channel ) {
			payment_channels.push( 'card' );
		}

		if ( wc_ercaspay_params.ussd_channel ) {
			payment_channels.push( 'ussd' );
		}

		if ( wc_ercaspay_params.qr_channel ) {
			payment_channels.push( 'qr' );
		}

		if ( wc_ercaspay_params.bank_transfer_channel ) {
			payment_channels.push( 'bank_transfer' );
		}

		return payment_channels;
	}

	function wcercaspayFormHandler() {

		$( '#wc-ercaspay-form' ).hide();

		if ( ercaspay_submit ) {
			ercaspay_submit = false;
			return true;
		}

		let $form = $( 'form#payment-form, form#order_review' ),
			ercaspay_txnref = $form.find( 'input.ercaspay_txnref' ),
			subaccount_code = '',
			charges_account = '',
			transaction_charges = '';

		ercaspay_txnref.val( '' );

		if ( wc_ercaspay_params.subaccount_code ) {
			subaccount_code = wc_ercaspay_params.subaccount_code;
		}

		if ( wc_ercaspay_params.charges_account ) {
			charges_account = wc_ercaspay_params.charges_account;
		}

		if ( wc_ercaspay_params.transaction_charges ) {
			transaction_charges = Number( wc_ercaspay_params.transaction_charges );
		}

		let amount = Number( wc_ercaspay_params.amount );

		let ercaspay_callback = function( transaction ) {
			$form.append( '<input type="hidden" class="ercaspay_txnref" name="ercaspay_txnref" value="' + transaction.reference + '"/>' );
			ercaspay_submit = true;

			$form.submit();

			$( 'body' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				},
				css: {
					cursor: "wait"
				}
			} );
		};

		let paymentData = {
			key: wc_ercaspay_params.key,
			email: wc_ercaspay_params.email,
			amount: amount,
			ref: wc_ercaspay_params.txnref,
			currency: wc_ercaspay_params.currency,
			subaccount: subaccount_code,
			bearer: charges_account,
			transaction_charge: transaction_charges,
			metadata: {
				custom_fields: wcercaspayCustomFields(),
			},
			onSuccess: ercaspay_callback,
			onCancel: () => {
				$( '#wc-ercaspay-form' ).show();
				$( this.el ).unblock();
			}
		};

		if ( Array.isArray( wcPaymentChannels() ) && wcPaymentChannels().length ) {
			paymentData[ 'channels' ] = wcPaymentChannels();
			if ( !$.isEmptyObject( wcercaspayCustomFilters() ) ) {
				paymentData[ 'metadata' ][ 'custom_filters' ] = wcercaspayCustomFilters();
			}
		}

		const ercaspay = new ercaspayPop();
		ercaspay.newTransaction( paymentData );
	}

} );