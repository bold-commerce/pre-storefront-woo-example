<?php

namespace Bold\Controllers\PriceRulesEngine\Components;

use Bold\Views\PriceRulesEngine\PlatformDataView;

require_once __DIR__ . '/../../views/PlatformDataView.php';

class PlatformDataController
{
	private $platformDataView;

	public function __construct()
	{
		$this->platformDataView = new PlatformDataView();
	}

	public function onInit()
	{
		/*
			NOTE: This is a workaround. If pr-woocommerce.js is loaded on the
			WooCommerce Integrated Checkout page (checkout-2), a second subtotal
			amount is rendered.
		*/
		global $wp;
		if ($wp->request == 'checkout-2') {
			return '';
		}

		$domain = get_home_url();
		$currency = get_woocommerce_currency();
		$currencySymbol = get_woocommerce_currency_symbol();
		$pieces = parse_url($domain);
		$permDomain = isset($pieces['host']) ? $pieces['host'] : '';
		$user = '';
		$roles = '';
		$email = '';

		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			$rolesArray = (array) $user->roles;
			if (!empty($rolesArray)) {
				$roles = implode($rolesArray);
			}
			$email = $user->user_email;
		}
		$originalCart = WC()->cart->get_cart();

		$allCartProducts = [];
		foreach ($originalCart as $product) {
			$cartProduct = wc_get_product($product['product_id']);
			if ($cartProduct) {
				if ($cartProduct->is_type('variable')) {
					$cartVariant = wc_get_product($product['variation_id']);
					$allCartProducts[] = $cartVariant->get_data();
				} else {
					$cartItemProduct = (array) $cartProduct->get_data();
					$cartItemToAdd = [];
					$cartItemToAdd['id'] = $cartItemProduct['id'];
					$cartItemToAdd['name'] = $cartItemProduct['name'];
					$cartItemToAdd['price'] = $cartItemProduct['price'];
					$cartItemToAdd['sku'] = $cartItemProduct['sku'];

					$allCartProducts[] = $cartItemToAdd;
				}
			}
		}

		$serializedCart = [];
		$serializedCart['items'] = $originalCart;
		$serializedCart['products'] = $allCartProducts;
		$serializedCart['cart_total'] = WC()->cart->total;

		$cart = json_encode($serializedCart);

		$product = [];

		if (is_product()) {
			$rawProduct = wc_get_product();
			$arrayRawProduct = (array) $rawProduct->get_data();
			$product['id'] = $arrayRawProduct['id'];
			$product['name'] = $arrayRawProduct['name'];
			$product['price'] = $arrayRawProduct['price'];

			if ($rawProduct->is_type('variable')) {
				$product['variants'] = $rawProduct->get_available_variations();
			} else {
				$product['variants'] = [];
			}
			$product = json_encode($product);
		}

		/*
		Any shop page can have a list of products, even product pages ("Related Products" section),
		therefore, we should always get all products for collection.
		*/

		$processedProducts = [];
		global $products;
		if ($products == null) {
			$rawProducts = [];
		} else {
			$rawProducts = $products;
		}
		foreach ($rawProducts as $rawProduct) {
			$arrayRawProduct = (array) $rawProduct->get_data();
			$productToAdd = [];
			$productToAdd['id'] = $arrayRawProduct['id'];
			$productToAdd['name'] = $arrayRawProduct['name'];
			$productToAdd['price'] = $arrayRawProduct['price'];
			$processedProducts[] = $productToAdd;
		}

		$processedProducts = json_encode($processedProducts);
		$template = basename(get_permalink());
		return $this->platformDataView->render($domain, $permDomain, $currency, $currencySymbol, $user, $roles, $email, $cart, $product, $processedProducts, $template);
	}
}
