<?php

declare(strict_types=1);

namespace PriceRulesEngine;

require_once 'controllers/PriceRulesEngineController.php';

use Bold\Controllers\PriceRulesEngine\PriceRulesEngineController;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class BoldPriceRulesEngine
{
	public function __construct()
	{
		add_action('init', array($this, 'onInit'));
		$this->controller = new PriceRulesEngineController();
	}


	public function onInit()
	{
		if (!is_admin() && class_exists('Woocommerce') && class_exists('\\Bold\\Plugin')) {
			$this->controller->onInit();
		}
	}
}

$scaPriceRulesPlugin = new BoldPriceRulesEngine();
