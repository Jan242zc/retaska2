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
			->setHtmlAttribute('class', 'submit fullWidth');
		
		$form->onSuccess[] = [$this, 'editBasketFormSucceeded'];
		
		return $form;
	}
	
	public function editBasketFormSucceeded(Form $form, Array $data)
	{
		dump($data);
		$idValues = $form->getHttpData($form::DATA_LINE, 'id[]');
		$quantityValues = $form->getHttpData($form::DATA_LINE, 'quantity[]');
		$toBeDeletedValues = $form->getHttpData($form::DATA_TEXT, 'toBeDeleted[]');
		dump($idValues);
		dump($quantityValues);
		dump($toBeDeletedValues);
		exit;
	}
}
