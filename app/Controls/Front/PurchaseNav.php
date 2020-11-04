<?php

declare(strict_types=1);

namespace App\Controls\Front;

use Nette;
use Nette\Application\UI\Control;


final class PurchaseNav extends Control
{
	/** @var bool */
	private $basketNotEmpty;
	
	/** @var bool */
	private $customerDataSaved;
	
	public function __construct(bool $basketNotEmpty, bool $customerDataSaved){
		$this->basketNotEmpty = $basketNotEmpty;
		$this->customerDataSaved = $customerDataSaved;
	}
	
	public function getBasketNotEmpty(): bool
	{
		return $this->basketNotEmpty;
	}
	
	public function setBasketNotEmpty(bool $basketNotEmpty): void
	{
		$this->basketNotEmpty = $basketNotEmpty;
	}
	
	public function getCustomerDataSaved(): bool
	{
		return $this->customerDataSaved;
	}
	
	public function setCustomerDataSaved(bool $customerDataSaved): void
	{
		$this->customerDataSaved = $customerDataSaved;
	}
	
	public function render(): void
	{
		$this->template->render(__DIR__ . '/templates/purchaseNav.latte');
	}
	
	private function setLinkClasses(): void
	{
		// $this->getPresenter()->getName();
		// $this->template->basketLinkClasses = 
		// $this->template->customerDataLinkClasses = 
		// $this->template->purchaseRecapClass = 
	}
}
