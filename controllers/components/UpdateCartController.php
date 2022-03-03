<?php

namespace Bold\Controllers\PriceRulesEngine\Components;

class UpdateCartController
{
	public function onInit()
	{
		$originalCart = WC()->cart->get_cart();

		$allCartProducts = [];
		foreach ($originalCart as $product) {
			$cartProduct = wc_get_product($product['product_id']);
			if ($cartProduct) {
				if ($cartProduct->is_type('variable')) {
					$cartVariant = wc_get_product($product['variation_id']);
					$allCartProducts[] = $cartVariant->get_data();
				} else {
					$allCartProducts[] = $cartProduct->get_data();
				}
			}
		}

		$serializedCart = [];
		$serializedCart['items'] = $originalCart;
		$serializedCart['products'] = $allCartProducts;
		$serializedCart['cart_total'] = WC()->cart->total;

		$cart = json_encode($serializedCart);
		return $cart;
	}
}
