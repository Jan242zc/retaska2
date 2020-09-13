<?php

declare(strict_types=1);

namespace App\Controls\Front\Factory;

use App\Controls\Front\ProductsOnFrontendControl;


interface IProductsOnFrontendControlFactory //Nette automatically generates a class that implements this interface
{
	public function create(): ProductsOnFrontendControl;
}
