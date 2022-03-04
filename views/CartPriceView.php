<?php

namespace Bold\Views\PriceRulesEngine;

class CartPriceView
{
	public function cwChangeCartItemPriceDisplay($price, $cart_item, $cart_item_key)
	{
		return '<span class="money" data-item-key="' . $cart_item_key . '">' . "{$price}" . '</span>';
	}

	public function cwChangeCartItemSubTotalPriceDisplay($wc, $cart_item, $cart_item_key)
	{
		return '<span class="money" data-line-total="' . $cart_item['line_subtotal'] . '" data-item-key="' . $cart_item_key . '"></span>';
	}

	public function cwChangeCartTotalDisplay($price)
	{
		return '<span class="money" data-cart-total="">' . "{$price}" . '</span>';
	}


	public function updateCart($cart)
	{
		
		?>
		<script type="text/javascript">
			let items = {lineItems: <?php echo $cart ?>}
			console.log(items)
			window.BOLD && window.BOLD.common && window.BOLD.common.eventEmitter.emit("BOLD_NEW_CART", items);
		</script>
		<?php
	}

	public function actionWoocommerceAfterCartItemQuantityUpdate()
	{
		// This function needs to be called for post back, do not delete
		//This is necessary for the function to be called, even though it does nothing
		return null;
	}
}
