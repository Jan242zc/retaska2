<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Entity\Factory\PurchaseStatusFactory;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;


final class PurchaseStatusPresenter extends BasePresenter
{
	private $purchaseStatusRepository;
	private $purchaseStatusFactory;

	public function __construct(IPurchaseStatusRepository $purchaseStatusRepository, PurchaseStatusFactory $purchaseStatusFactory){
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->purchaseStatusFactory = $purchaseStatusFactory;
	}
	
	public function renderDefault(): void
	{
		$this->template->purchaseStatuses = $this->purchaseStatusRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$purchaseStatus = $this->purchaseStatusRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Stav objednávky nenalezen.');
				$this->redirect('PurchaseStatus:default');
			}
			$formDefaults = $purchaseStatus->toArray();
		}
		$this['managePurchaseStatusEntityForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManagePurchaseStatusEntityForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');

		$form->addHidden('id')
			->addFilter(function($value){
					return intval($value);
				});

		$form->addText('name', 'Název:')
			->setRequired('Stav musí mít název.');

		$form->addSelect('means_cancelled', 'Znamená, že objednávka je zrušena:')
			->setItems([
				0 => 'Ne',
				1 => 'Ano'
			])
			->setRequired('Vyplňte druhé pole.');

		$form->addHidden('default_for_new_purchases');

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'managePurchaseStatusEntityFormSucceeded'];

		return $form;
	}
	
	public function managePurchaseStatusEntityFormSucceeded(Form $form, Array $data): void
	{
		$purchaseStatus =  $this->purchaseStatusFactory->createFromArray($data);
		
		if(!$nameIsOriginal = $this->verifyNameOriginality($purchaseStatus)){
			$this->flashMessage('Druh stavu objednávky s tímto názvem již existuje. Zvolte jiný.');
			$this->redirect('this');
		}
		
		if(!$purchaseStatus->getId()){
			try{
				$rowsAffected = $this->purchaseStatusRepository->insert($purchaseStatus);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet stavů objednávky, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Stav objednávky uložen.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->purchaseStatusRepository->update($purchaseStatus) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}

		$this->redirect('PurchaseStatus:default');
	}

	protected function createComponentPickNewDefaultStatusForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');

		$form->addSelect('newDefault', 'Původní stav pro nové objednávky:')
			->setItems($this->purchaseStatusRepository->findAllForNewDefaultForm())
			->setPrompt('Zvolte možnost.');

		$form->addSubmit('save', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'pickNewDefaultStatusFormSucceeded'];

		return $form;
	}

	public function pickNewDefaultStatusFormSucceeded(Form $form, Array $data): void
	{
		$purchaseStatus = $this->purchaseStatusRepository->findById($data['newDefault']);

		if($this->purchaseStatusRepository->setDefaultStatusForNewPurchases($purchaseStatus) === 1){
			$this->flashMessage('Změny uloženy.');
			$this->redirect('PurchaseStatus:default');
		} else {
			$this->flashMessage('Něco se pokazilo.');
			$this->redirect('this');
		}
	}

	public function actionDelete($id): void
	{
		if($this->purchaseStatusRepository->delete($id) === 1){
			$this->flashMessage('Stav objednávky smazán.');
		} else {
			$this->flashMessage('Stav objednávky nenalezen.');
		}
		$this->redirect('PurchaseStatus:default');
	}

	private function verifyNameOriginality($purchaseStatus): bool
	{
		if(is_null($purchaseStatus->getId())){	
			$usedNames = $this->purchaseStatusRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->purchaseStatusRepository->getArrayOfUsedNames($purchaseStatus->getId());
		}

		return !in_array(trim(mb_strtolower($purchaseStatus->getName())), $usedNames);
	}
}
