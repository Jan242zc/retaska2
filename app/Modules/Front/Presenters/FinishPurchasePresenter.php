<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Front\Presenters\BaseFrontPresenter AS BasePresenter;
use App\Services\GeneralServiceInterface\IBasketService;
use Nette\Application\UI\Form;


final class FinishPurchasePresenter extends BasePresenter
{
	/** @var IBasketService */
	private $basketService;
	
	public function __construct(IBasketService $basketService){
		$this->basketService = $basketService;
	}
	
	public function renderDefault(): void
	{
		$this->template->productTotalPrice = $this->basketService->getTotalProductPrice();
		$this->template->basketItems = $this->basketService->getAllBasketItems();
	}
	
	protected function createComponentPurchaseForm(): Form
	{
		$form = new Form;
		$form->setHtmlAttribute('class', 'form');
		
		$personalData = $form->addContainer('personalData');
		
		$personalData->addText('name', 'Jméno a příjmení:')
			->setRequired('Zadejte své jméno a příjmení.');
			
		$personalData->addText('streetAndNumber', 'Ulice a číslo domu:')
			->setRequired('Zadejte ulici a číslo domu.');
			
		$personalData->addText('city', 'Město:')
			->setRequired('Zadejte město.');
			
		$personalData->addText('zip', 'PSČ:')
			//->setRule - numeric...
			->setRequired('Zadejte PSČ.');
			
		$personalData->addSelect('country', 'Stát:'); //opravdu select? nejde třeba select s možností vlastního vstupu, pokud je zaškrtnuto pole lišících se adres?
			//->setItems...
			//->setRequired('Zadejte stát.');
			
		$personalData->addEmail('email', 'E-mail:')
			->setRequired('Zadejte emailovou adresu.');
		
		$personalData->addText('phone', 'Telefonní číslo:')
			->setRequired('Zadejte telefonní číslo.');
		
		$deliveryTerms = $form->addContainer('deliveryTerms');
		
		$deliveryTerms->addRadioList('delivery', 'Doprava:')
			->setItems([
				1 => 'Osobní odběr',
				2 => 'PLL',
				3 => 'Česká pošta'
			]);
		
		$deliveryTerms->addRadioList('payment', 'Platba:')
			->setItems([
				1 => 'Hotově při převzetí',
				2 => 'Převodem'
			]);

		$deliveryTerms->addTextArea('note', 'Poznámka:');

		$deliveryTerms->addCheckbox('differentAdress', 'Doručit na jinou adresu než fakturační')
			->setHtmlAttribute('id', 'differentAdress');
			
		$deliveryAdress = $form->addContainer('deliveryAdress');
		
		$deliveryAdress->addText('streetAndNumber', 'Ulice a číslo domu:');
			
		$deliveryAdress->addText('city', 'Město:');
			
		$deliveryAdress->addText('zip', 'PSČ:');
			//->setRule - numeric...
			
		$deliveryAdress->addSelect('country', 'Stát:'); //opravdu select? nejde třeba select s možností vlastního vstupu, pokud je zaškrtnuto pole lišících se adres?
			//->setItems...
		
		$form->addSubmit('submit', 'Přejít k rekapitulaci')
			->setHtmlAttribute('class', 'submit');
		
		$form->onSuccess[] = [$this, 'purchaseFormSucceeded'];
		
		return $form;
	}
	
	public function purchaseFormSucceeded(Form $form, Array $data): void
	{
		dump($data);
		exit;
	}
}
