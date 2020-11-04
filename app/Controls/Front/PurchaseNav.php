<?php

declare(strict_types=1);

namespace App\Controls\Front;

use Nette;
use Nette\Application\UI\Control;


final class PurchaseNav extends Control
{
	const LINK_NOT_CLICKABLE_CLASSES = [
		'aHref' => '#',
		'wrapper' => ' step-link-wrapper-not-clickable',
		'number' => ' step-link-number-not-clickable',
		'text' => ' step-link-text-not-clickable'
	];

	const CURRENT_PAGE_CLASSES = [
		'aHref' => '#',
		'wrapper' => ' step-link-wrapper-current',
		'number' => ' step-link-number-current',
		'text' => ''
	];
	
	const DEFAULT_HREFS = [
		'basket' => true,
		'customerData' => true,
		'purchaseRecap' => true
	];
	
	const NOT_CLICKABLE_HREF = false;

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
		$this->setLinkClassesAndHrefs();
		$this->template->render(__DIR__ . '/templates/purchaseNav.latte');
	}
	
	private function setLinkClassesAndHrefs(): void
	{
		$basketLinkClasses = $customerDataLinkClasses = $purchaseRecapClasses = [
			'wrapper' => '',
			'number' => '',
			'text' => ''
		];

		$hrefs = self::DEFAULT_HREFS;

		if(!$this->basketNotEmpty){
			$customerDataLinkClasses = $purchaseRecapClasses = self::LINK_NOT_CLICKABLE_CLASSES;
			$hrefs['customerData'] = $hrefs['purchaseRecap'] = self::NOT_CLICKABLE_HREF;
		}

		if($this->basketNotEmpty && !$this->customerDataSaved){
			$purchaseRecapClasses = self::LINK_NOT_CLICKABLE_CLASSES;
			$hrefs['purchaseRecap'] = self::NOT_CLICKABLE_HREF;
		}

		switch($this->getPresenter()->getName()){
			case 'Front:Basket':
				$basketLinkClasses = self::CURRENT_PAGE_CLASSES;
				$hrefs['basket'] = self::NOT_CLICKABLE_HREF;
				break;
			case 'Front:FinishPurchase':
				switch($this->getPresenter()->getAction()){
					case 'default':
						$customerDataLinkClasses = self::CURRENT_PAGE_CLASSES;
						$hrefs['customerData'] = self::NOT_CLICKABLE_HREF;
						break;
					case 'purchaseRecap':
						$purchaseRecapClasses = self::CURRENT_PAGE_CLASSES;
						$hrefs['purchaseRecap'] = self::NOT_CLICKABLE_HREF;
						break;
				}
				break;
		}

		$this->template->basketLinkClasses = $basketLinkClasses;
		$this->template->customerDataLinkClasses = $customerDataLinkClasses;
		$this->template->purchaseRecapClasses = $purchaseRecapClasses;
		$this->template->hrefs = $hrefs;
	}
}
