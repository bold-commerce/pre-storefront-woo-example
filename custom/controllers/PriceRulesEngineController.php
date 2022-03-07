<?php

namespace Bold\Controllers\PriceRulesEngine;

use Bold\Controllers\PriceRulesEngine\Components\PlatformDataController;
use Bold\Controllers\PriceRulesEngine\Components\UpdateCartController;
use Bold\Views\PriceRulesEngine\PlatformDataView;
use Bold\Views\PriceRulesEngine\ProductPriceView;
use Bold\Views\PriceRulesEngine\CartPriceView;

require_once __DIR__ . '/components/PlatformDataController.php';
require_once __DIR__ . '/components/UpdateCartController.php';
require_once __DIR__ . '/../views/PlatformDataView.php';
require_once __DIR__ . '/../views/ProductPriceView.php';
require_once __DIR__ . '/../views/CartPriceView.php';

/**
 * functions.php
 * Add PHP snippets here
 */

class PriceRulesEngineController
{
	private $productPriceView;
	private $CartPriceView;
	public function __construct()
	{
		$this->platformData = new PlatformDataController();
		$this->updateCart = new UpdateCartController();
		$this->productPriceView = new ProductPriceView();
		$this->cartPriceView = new CartPriceView();
		$this->platformDataView = new PlatformDataView();
	}

	public function onInit()
	{
		add_filter('woocommerce_get_price_html', array($this, 'prodPrice'), 10, 2);
		add_filter('woocommerce_cart_item_price', array($this, 'cartItem'), 10, 3);
		add_filter('woocommerce_cart_item_subtotal', array($this, 'cartSubtotal'), 10, 3);
		add_filter('woocommerce_cart_subtotal', array($this, 'cartTotal'));
		add_filter('woocommerce_cart_total', array($this, 'cartTotal'));
		add_action('woocommerce_after_shop_loop_item', array($this, 'lwLoopShopPerPage'), 10, 0);
		add_filter('wp_footer', array($this, 'platformData'));
		add_filter('woocommerce_after_mini_cart', array($this, 'updateCart'));
		add_filter('woocommerce_after_cart_contents', array($this, 'platformData'));
		add_action('woocommerce_cart_totals_after_order_total', array($this, 'afterCartUpdate'), 10, 4);

		// no-op
	}

	public function prodPrice($price, $instance)
	{
		return $this->productPriceView->render($price, $instance);
	}

	public function cartItem($price, $cart_item, $cart_item_key)
	{
		return $this->cartPriceView->cwChangeCartItemPriceDisplay($price, $cart_item, $cart_item_key);
	}

	public function cartSubtotal($wc, $cart_item, $cart_item_key)
	{
		return $this->cartPriceView->cwChangeCartItemSubTotalPriceDisplay($wc, $cart_item, $cart_item_key);
	}

	public function cartTotal($price)
	{
		return $this->cartPriceView->cwChangeCartTotalDisplay($price);
	}

	public function lwLoopShopPerPage()
	{
		// Creates the $products array to be used later
		global $products;
		global $product;
		$products[] = $product;
	}
	public function platformData()
	{
		$this->platformData->onInit();
		$this->platformDataView->registerScripts();
	}

	public function updateCart()
	{
		$cart = $this->updateCart->onInit();

		return $this->cartPriceView->updateCart($cart);
	}

	public function afterCartUpdate()
	{
		return $this->cartPriceView->actionWoocommerceAfterCartItemQuantityUpdate();
	}
}
