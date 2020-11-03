<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;
use Nette\Application\UI\Form;
use App\Entity\Factory\DeliveryCountryPaymentPricesFactory;


final class DeliveryCountryPaymentPricesPresenter extends BasePresenter
{
	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;

	/** @var IDeliveryRepository */
	private $deliveryRepository;

	/** @var ICountryRepository */
	private $countryRepository;

	/** @var IPaymentRepository */
	private $paymentRepository;
	
	/** @var DeliveryCountryPaymentPricesFactory */
	private $deliveryCountryPaymentPricesFactory;

	public function __construct(IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository, IDeliveryRepository $deliveryRepository, ICountryRepository $countryRepository, IPaymentRepository $paymentRepository, DeliveryCountryPaymentPricesFactory $deliveryCountryPaymentPricesFactory){
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
		$this->deliveryRepository = $deliveryRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentRepository = $paymentRepository;
		$this->deliveryCountryPaymentPricesFactory = $deliveryCountryPaymentPricesFactory;
	}

	public function renderDefault(): void
	{
		try{
			$this->template->deliveryCountryPaymentPrices = $this->deliveryCountryPaymentPricesRepository->findAll();
		} catch(\Exception $ex){
			$this->flashMessage('Došlo k chybě.');
			$this->template->deliveryCountryPaymentPrices = null;
		}
	}

	public function actionManage($id = null): void
	{
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$deliveryCountryPaymentPrices = $this->deliveryCountryPaymentPricesRepository->findById(intval($id));
			} catch (\Exception $ex){
				$this->flashMessage('Služba dopravy nenalezena.');
				$this->redirect('DeliveryCountryPaymentPrices:default');
			}
			$formDefaults = $deliveryCountryPaymentPrices->toArray();
		}
		$this['manageDeliveryCountryPaymentPricesForm']->setDefaults($formDefaults);
	}

	protected function createComponentManageDeliveryCountryPaymentPricesForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');

		$form->addHidden('id')
			->addFilter(function($value){
					return intval($value);
				});

		$form->addSelect('delivery', 'Způsob dopravy nebo předání:')
			->setRequired('Je nutné zadat způsob dopravy nebo předání.')
			->setItems($this->deliveryRepository->findAllForForm())
			->setPrompt('Zvolte způsob dopravy nebo předání.');

		$form->addSelect('payment', 'Způsob platby:')
			->setRequired('Je nutné zadat způsob platby.')
			->setItems($this->paymentRepository->findAllForForm())
			->setPrompt('Zvolte způsob platby.');

		$form->addSelect('country', 'Pro stát:')
			->setItems($this->countryRepository->findAllForForm())
			->setPrompt('Zvolte stát.');

		$form->addText('deliveryPrice', 'Cena dopravy / předání:')
			->setRequired('Zadejte cenu dopravy / předání.')
			->addRule($form::FLOAT, 'Cena musí dopravy být kladné číslo.')
			->addFilter(function($value){
				return floatval($value);
			});

		$form->addText('paymentPrice', 'Cena platby:')
			->setRequired('Zadejte cenu platby.')
			->addRule($form::FLOAT, 'Cena platby musí být kladné číslo.')
			->addFilter(function($value){
				return floatval($value);
			});

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'manageDdeliveryCountryPaymentPricesFormSucceeded'];

		return $form;
	}
	
	public function manageDdeliveryCountryPaymentPricesFormSucceeded(Form $form, Array $data): void
	{
		$deliveryCountryPaymentPrices = $this->deliveryCountryPaymentPricesFactory->createFromArray($data);

		if(!$deliveryCountryPaymentPrices->getId()){
			try{
				$rowsAffected = $this->deliveryCountryPaymentPricesRepository->insert($deliveryCountryPaymentPrices);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet služeb, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Služba dopravy uložena.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->deliveryCountryPaymentPricesRepository->update($deliveryCountryPaymentPrices) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		$this->redirect('DeliveryCountryPaymentPrices:default');
	}

	public function actionDelete($id): void
	{
		if($this->deliveryCountryPaymentPricesRepository->delete($id) === 1){
			$this->flashMessage('Služba doručení smazána.');
		} else {
			$this->flashMessage('Služba doručení nenalezena.');
		}
		$this->redirect('DeliveryCountryPaymentPrices:default');
	}
}
