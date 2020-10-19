<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Front\Presenters\BaseFrontPresenter AS BasePresenter;
use App\Services\GeneralServiceInterface\IBasketService;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;
use App\Services\DeliveryCountryPaymentPricesArrayGenerator;
use App\Entity\Factory\PurchaseFactory;


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

	private $deliveryCountryPaymentPricesArrayGenerator;

	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;

	public function __construct(IBasketService $basketService, ICountryRepository $countryRepository, IDeliveryRepository $deliveryRepository, IPaymentRepository $paymentRepository, DeliveryCountryPaymentPricesArrayGenerator $deliveryCountryPaymentPricesArrayGenerator, IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository){
		$this->basketService = $basketService;
		$this->countryRepository = $countryRepository;
		$this->deliveryRepository = $deliveryRepository;
		$this->paymentRepository = $paymentRepository;
		$this->deliveryCountryPaymentPricesArrayGenerator = $deliveryCountryPaymentPricesArrayGenerator;
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
	}

	public function renderDefault(): void
	{
		$this->template->productTotalPrice = $this->basketService->getTotalProductPrice();
		$this->template->basketItems = $this->basketService->getAllBasketItems();
		$this->template->deliveryServicesGroupedByDelivery = $this->deliveryCountryPaymentPricesArrayGenerator->generateByDeliveryArray();
		$this->template->deliveryServicesGroupedByCountry = $this->deliveryCountryPaymentPricesArrayGenerator->generateByCountryArray();
		$this->template->countryIndependentDeliveryServicesGroupedByDelivery = $this->deliveryCountryPaymentPricesArrayGenerator->generateCountryIndependentServicesArray();
		$this->template->deliveryNames = $this->deliveryRepository->findAllForForm();
		$this->template->paymentNames = $this->paymentRepository->findAllForForm();
		
		if(!is_null($this->basketService->getPurchase())){
			$this['purchaseForm']->setDefaults($this->basketService->getPurchase()->toFinishPurchaseArray());
		}
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
			->setRequired('Zadejte PSČ.');

		$personalData->addSelect('country', 'Stát:')
			->setItems($this->countryRepository->findAllForForm())
			->setRequired('Zadejte stát.');

		$personalData->addEmail('email', 'E-mail:')
			->setRequired('Zadejte emailovou adresu.');

		$personalData->addText('phone', 'Telefonní číslo:')
			->addRule($form::PATTERN, 'Zadejte platné telefonní číslo.', '^([+]|[00])+\d{7,15}')
			->setRequired('Zadejte telefonní číslo.');

		$personalData->addCheckbox('differentAdress', 'Doručit na jinou adresu než fakturační')
			->setHtmlAttribute('id', 'differentAdress');

		$deliveryTerms = $form->addContainer('deliveryTerms');

		$deliveryTerms->addSelect('delivery', 'Doprava:')
			->setItems($this->deliveryRepository->findAllForForm());

		$deliveryTerms->addSelect('payment', 'Platba:')
			->setItems($this->paymentRepository->findAllForForm());

		$deliveryTerms->addTextArea('note', 'Poznámka:');

		$deliveryAdress = $form->addContainer('deliveryAdress');

		$deliveryAdress->addText('streetAndNumber', 'Ulice a číslo domu:')
			->addConditionOn($form['personalData']['differentAdress'], $form::EQUAL, true)
			->setRequired('Zadejte ulici a číslo domu u doručovací adresy.');

		$deliveryAdress->addText('city', 'Město:')
			->addConditionOn($form['personalData']['differentAdress'], $form::EQUAL, true)
			->setRequired('Zadejte město u doručovací adresy.');

		$deliveryAdress->addText('zip', 'PSČ:')
			->addConditionOn($form['personalData']['differentAdress'], $form::EQUAL, true)
			->setRequired('Zadejte PSČ u doručovací adresy.');

		$deliveryAdress->addSelect('country', 'Stát:')
			->setItems($this->countryRepository->findAllForForm());

		$form->addSubmit('submit', 'Uložit a přejít k rekapitulaci')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'purchaseFormSucceeded'];

		return $form;
	}

	public function purchaseFormSucceeded(Form $form, Array $data): void
	{
		if($data['personalData']['differentAdress'] === false){
			$countryId = $data['personalData']['country'];
			$countryIgnorable = true; //country ignorable = country independent services are acceptable
		} else {
			$countryId = $data['deliveryAdress']['country'];
			$countryIgnorable = false;
		}

		try{
			$deliveryService = $this->deliveryCountryPaymentPricesRepository->findByDefiningStuff($data['deliveryTerms']['delivery'], $data['deliveryTerms']['payment'], $countryIgnorable, $countryId);
		} catch(\Exception $ex){
			$this->flashMessage('Zadejte platnou kombinaci státu, dopravy a platby.');
			$this->redirect('this');
		}

		$data['personalData']['country'] = $this->countryRepository->findById($data['personalData']['country']);
		$data['deliveryAdress']['country'] = $this->countryRepository->findById($data['deliveryAdress']['country']);
		
		$purchase = PurchaseFactory::createFromFinishPurchaseFormData($data, $deliveryService);
		$this->basketService->setPurchase($purchase);
		$this->redirect('FinishPurchase:purchaseRecap');
	}
	
	public function renderPurchaseRecap(): void
	{
		$this->template->productTotalPrice = $this->basketService->getTotalProductPrice();
		$this->template->basketItems = $this->basketService->getAllBasketItems();
		$this->template->purchaseInfoRecap = $this->basketService->getPurchase();
	}
}
