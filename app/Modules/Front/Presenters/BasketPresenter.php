<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Modules\Front\Presenters\BaseFrontPresenter AS BasePresenter;
use App\Services\GeneralServiceInterface\IBasketService;
use Nette\Application\UI\Form;


final class BasketPresenter extends BasePresenter
{
	/** @var IBasketService */
	private $basketService;
	
	public function __construct(IBasketService $basketService){
		$this->basketService = $basketService;
	}

	public function renderDefault(): void
	{
		$this->template->basketItems = $this->basketService->getAllBasketItems();
	}
	
	protected function createComponentEditBasketForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'add-to-basket border-all');
		
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit halfWidth')
			->setHtmlAttribute('id', 'submit');
		
		$form->onSuccess[] = [$this, 'editBasketFormSucceeded'];
		
		return $form;
	}
	
	public function editBasketFormSucceeded(Form $form, Array $data)
	{
		$idValues = $form->getHttpData($form::DATA_LINE, 'id[]');
		if(!$this->valuesAreNotStringOrDecimalOrNegative($idValues) || !$this->basketService->verifyThatAllTheseItemsInBasket($idValues)){
			$this->flashMessage('Ale no tak!', 'naughty');
			$this->redirect('Basket:default');
		}
		$quantityValues = $form->getHttpData($form::DATA_LINE, 'quantity[]');
		if(!$this->valuesAreNotStringOrDecimalOrNegative($quantityValues) || count($idValues) != count($quantityValues)){
			$this->flashMessage('Množství musí být celé kladné číslo.');
			$this->redirect('Basket:default');
		}
		$toBeDeletedValues = $form->getHttpData($form::DATA_LINE | $form::DATA_KEYS, 'toBeDeleted[]');
		if(!$this->valuesAreNotStringOrDecimalOrNegative($toBeDeletedValues) || !$this->basketService->verifyThatAllTheseItemsInBasket($toBeDeletedValues)){
			$this->flashMessage('Ale no tak!', 'naughty');
			$this->redirect('Basket:default');
		}
		$visibleQuantityValues = $form->getHttpData($form::DATA_LINE, 'visibleQuantity[]');
		if(!$this->valuesAreNotStringOrDecimalOrNegative($visibleQuantityValues)){
			$this->flashMessage('Množství musí být celé kladné číslo.');
			$this->redirect('Basket:default');
		}
	
		$this->basketService->adjustBasketByBasketFormData($idValues, $quantityValues, $toBeDeletedValues);
		
		$this->flashMessage('Změny uloženy.');
		$this->redirect('Basket:default');
	}
	
	public function actionDeleteAllItems(): void
	{
		$this->basketService->removeAllItemsFromBasket();
		$this->flashMessage('Všechno zboží smazáno z košíku.');
		$this->redirect('Basket:default');  
	}
	
	private function valuesAreNotStringOrDecimalOrNegative(Array $array): bool
	{
		foreach($array as $value){
			if(!is_numeric($value) || intval($value) != $value || intval($value) < 0){
				return false;
			}
		}
		return true;
	}
}
