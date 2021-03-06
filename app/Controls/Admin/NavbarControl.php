<?php

namespace App\Controls\Admin;

use Nette; 
use Nette\Application\UI\Control;


final class NavbarControl extends Control
{
	public function render(): void
	{
		$activeLinkCssClass = 'active';
		
		$linksCssClasses = [
			'Admin:Homepage' => '',
			'Admin:Purchase' => '',
			'Admin:Product' => '',
			'Admin:Category' => '',
			'Admin:Country' => '',
			'Admin:Delivery' => '',
			'Admin:Payment' => '',
			'Admin:DeliveryCountryPaymentPrices' => '',
			'Admin:PurchaseStatus' => '',
			'Admin:Role' => '',
			'Admin:UserData' => '',
			'Admin:Entity' => ''
		];
		
		$linksCssClasses[$this->getPresenter()->getName()] = $activeLinkCssClass;

		$this->template->linksCssClasses = $linksCssClasses;
		// $this->template->userName = $this->
		$this->template->render(__DIR__ . '/templates/navbar.latte');
	}
}
