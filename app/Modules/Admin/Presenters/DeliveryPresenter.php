<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use Nette\Application\UI\Form;
use App\Entity\Factory\DeliveryFactory;


final class DeliveryPresenter extends BasePresenter
{
	/** @var IDeliveryRepository */
	private $deliveryRepository;
	
	/** @var DeliveryFactory */
	private $deliveryFactory;
	
	public function __construct(IDeliveryRepository $deliveryRepository, DeliveryFactory $deliveryFactory){
		$this->deliveryRepository = $deliveryRepository;
		$this->deliveryFactory = $deliveryFactory;
	}
	
	public function renderDefault(): void
	{
		$this->template->deliveries = $this->deliveryRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$delivery = $this->deliveryRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Možnost dopravy nenalezena.');
				$this->redirect('Delivery:default');
			}
			$formDefaults = $delivery->toArray();
		}
		$this['manageDeliveryForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManageDeliveryForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id')
			->addFilter(function($value){
					return intval($value);
				});
		
		$form->addText('name', 'Název:')
			->setRequired('Stát musí mít název.');
		
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'manageDeliveryFormSucceeded'];
		
		return $form;
	}
	
	public function manageDeliveryFormSucceeded(Form $form, Array $data): void
	{
		$delivery = $this->deliveryFactory->createFromArray($data);
		
		if(!$nameIsOriginal = $this->verifyNameOriginality($delivery)){
			$this->flashMessage('Možnost dopravy s tímto názvem již existuje. Zvolte jiný.');
			$this->redirect('this');
		}
		
		if(!$delivery->getId()){
			try{
				$rowsAffected = $this->deliveryRepository->insert($delivery);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet možností dopravy, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Možnost dopravy uložena.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->deliveryRepository->update($delivery) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		$this->redirect('Delivery:default');
	}
	
	public function actionDelete($id): void
	{
		if($this->deliveryRepository->delete($id) === 1){
			$this->flashMessage('Možnost dopravy smazána.');
		} else {
			$this->flashMessage('Možnost dopravy nenalezena.');
		}
		$this->redirect('Delivery:default');
	}
	
	private function verifyNameOriginality($delivery): bool
	{
		if(is_null($delivery->getId())){	
			$usedNames = $this->deliveryRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->deliveryRepository->getArrayOfUsedNames($delivery->getId());
		}

		return !in_array(trim(mb_strtolower($delivery->getName())), $usedNames);
	}
}
