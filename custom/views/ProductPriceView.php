<?php

namespace Bold\Views\PriceRulesEngine;

class ProductPriceView
{
	public function render($price, $instance)
	{
		global $variationPage;

		if ($instance->is_type('variable')) {
			$variationPage = true;
		}

		if ($variationPage) {
			if ($instance->is_type('variable')) {
				$variationArray = (array) $instance->get_variation_prices();

				// Keys are not in order and can start from anywhere
				reset($variationArray); // Make sure the keys are in order
				$minVariationId = key($variationArray['price']); // This will resolve to the cheapest variation
				$minVariationPrice = $variationArray['price'][$minVariationId];

				// This will resolve to the most expensive variation; we are using array_slice() for compatibility with shops that work with an older php version.
				$maxVariationId = key(array_slice($variationArray['price'], -1, 1, true));
				$maxVariationPrice = $variationArray['price'][$maxVariationId];

				$combinedSpan = '<span class="money price price--withTax" data-variant-id="' . $minVariationId . '">' . get_woocommerce_currency_symbol() . "{$minVariationPrice}" . '</span>' .
				' - ' . '<span class="money price price--withTax" data-variant-id="' . $maxVariationId . '">' . get_woocommerce_currency_symbol() . "{$maxVariationPrice}" . '</span>';

				return $combinedSpan;
			} else {
				return '<span class="money price price--withTax" data-variant-id="' . $instance->get_id() . '">' . "{$price}" . '</span>';
			}
		} else {
			return '<span class="money price price--withTax" data-product-id="' . $instance->get_id() . '">' . "{$price}" . '</span>';
		}
	}
}
