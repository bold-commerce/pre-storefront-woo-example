<?php

namespace Bold\Views\PriceRulesEngine;

class PlatformDataView
{
	public function render(
		$domain,
		$permDomain,
		$currency,
		$currencySymbol,
		$user,
		$roles,
		$email,
		$cart,
		$product,
		$processedProducts,
		$template
	) {
		$cartArray = $cart === [] ? "null" : $cart;
		$prod = empty($product) ? "null" : $product;
		$collection = empty($processedProducts) ? "null" : $processedProducts;

		?> <script id="bold-platform-data" type="application/json">
			{
				"shop": {
					"domain": "<?php esc_html_e($domain) ?>",
					"permanent_domain": "<?php esc_html_e($permDomain) ?>",
					"currency": "<?php esc_html_e($currency) ?>",
					"currency_symbol": "<?php esc_html_e($currencySymbol) ?>"
				},
				"customer": {
					"id": <?php esc_html_e($user->id) ?>,
					"tags": "<?php esc_html_e($roles) ?>"
				},
				"cart": <?php echo wp_kses_post($cartArray) ?>,
				"product": <?php echo wp_kses_post($prod) ?>,
				"collection": <?php echo wp_kses_post($collection) ?>,
				"template": "<?php esc_html_e($template) ?>"
			}
		</script>


		<style>
			.money[data-product-id],
			.money[data-product-handle],
			.money[data-variant-id],
			.money[data-line-index],
			.money[data-cart-total] {
				animation: moneyAnimation 0s 2s forwards;
				visibility: hidden;
			}

			@keyframes moneyAnimation {
				to {
					visibility: visible;
				}
			}
		</style> <?php
	}

	public function registerScripts()
	{
		wp_register_script('pr-woocommerce', 'https://static.boldcommerce.com/bold-platform/sf/pr-woocommerce.js', '', '', true);
		wp_enqueue_script('pr-woocommerce');
	}
}
