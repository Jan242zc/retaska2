<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Front\Presenters\BaseFrontPresenter AS BasePresenter;
use App\Services\GeneralServiceInterface\IBasketService;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;


final class FinishPurchasePresenter extends BasePresenter
{
	/** @var IBasketService */
	private $basketService;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	/** @var IDeliveryRepository */
	private $deliveryRepository;
	
	/** @var IPaymentRepository */
	private $paymentRepository;
	
	public function __construct(IBasketService $basketService, ICountryRepository $countryRepository, IDeliveryRepository $deliveryRepository, IPaymentRepository $paymentRepository){
		$this->basketService = $basketService;
		$this->countryRepository = $countryRepository;
		$this->deliveryRepository = $deliveryRepository;
		$this->paymentRepository = $paymentRepository;
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
			
		$personalData->addSelect('country', 'Stát:') //opravdu select? nejde třeba select s možností vlastního vstupu, pokud je zaškrtnuto pole lišících se adres?
			->setItems($this->countryRepository->findAllForForm());
			//->setRequired('Zadejte stát.');
			
		$personalData->addEmail('email', 'E-mail:')
			->setRequired('Zadejte emailovou adresu.');
		
		$personalData->addText('phone', 'Telefonní číslo:')
			->setRequired('Zadejte telefonní číslo.');
			
		$personalData->addCheckbox('differentAdress', 'Doručit na jinou adresu než fakturační')
			->setHtmlAttribute('id', 'differentAdress');

		$deliveryTerms = $form->addContainer('deliveryTerms');
		
		$deliveryTerms->addRadioList('delivery', 'Doprava:')
			->setItems($this->deliveryRepository->findAllForForm());
		
		$deliveryTerms->addRadioList('payment', 'Platba:')
			->setItems($this->paymentRepository->findAllForForm());

		$deliveryTerms->addTextArea('note', 'Poznámka:');

		$deliveryAdress = $form->addContainer('deliveryAdress');
		
		$deliveryAdress->addText('streetAndNumber', 'Ulice a číslo domu:');
			
		$deliveryAdress->addText('city', 'Město:');
			
		$deliveryAdress->addText('zip', 'PSČ:');
			//->setRule - numeric...
			
		$deliveryAdress->addSelect('country', 'Stát:') //opravdu select? nejde třeba select s možností vlastního vstupu, pokud je zaškrtnuto pole lišících se adres?
			->setItems($this->countryRepository->findAllForForm());
		
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
