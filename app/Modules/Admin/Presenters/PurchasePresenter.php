<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Services\Repository\RepositoryInterface\IPurchaseRepository;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use Nette\Application\UI\Form;


final class PurchasePresenter extends BasePresenter
{
	/** @var IPurchaseRepository */
	private $purchaseRepository;
	
	/** @var IPurchaseStatusRepository */
	private $purchaseStatusRepository;
	
	/** @var IProductRepository */
	private $productRepository;
	
	public function __construct(IPurchaseRepository $purchaseRepository, IPurchaseStatusRepository $purchaseStatusRepository, IProductRepository $productRepository){
		$this->purchaseRepository = $purchaseRepository;
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->productRepository = $productRepository;
	}
	
	public function renderDefault(): void
	{
		try{
			$this->template->purchases = $this->purchaseRepository->findAll();
		} catch (\Exception $ex){
			throw $ex;
		}
	}
	
	public function renderDetail($id): void
	{
		try{
			$purchase = $this->purchaseRepository->findById(intval($id));
		} catch(\Exception $ex){
			$this->flashMessage('Objednávka nenalezena.');
			$this->redirect('Purchase:default');
		}
		
		$this['purchaseStatusForm']->setDefaults([
			'id' => $purchase->getId(),
			'status' => $purchase->getPurchaseStatus()->getId()
		]);
		
		$this->template->purchase = $purchase;
	}
	
	protected function createComponentPurchaseStatusForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		$form->setHtmlAttribute('class', 'purchase-status');

		$form->addHidden('id');

		$form->addSelect('status', 'Stav:')
			->setItems($this->purchaseStatusRepository->findAllForForm());

		$form->addSubmit('save', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'purchaseStatusFormSucceeded'];

		return $form;
	}

	public function purchaseStatusFormSucceeded(Form $form, Array $data): void
	{
		$purchase = $this->purchaseRepository->findById(intval($data['id']));
		$purchaseId = $purchase->getId();

		$newPurchaseStatus = $this->purchaseStatusRepository->findById(intval($data['status']));
		$purchase->setPurchaseStatus($newPurchaseStatus);

		try {
			$purchaseTableRowsAffected = $this->purchaseRepository->update($purchase);
		} catch (\Exception $ex) {
			$this->flashMessage('Nebyla provedena změna nebo došlo k chybě.');
			$this->redirect('this');
		}

		if($purchaseTableRowsAffected === 1){
			$this->flashMessage('Stav změněn.');
			$this->redirect('this');
		} else {
			$this->flashMessage('Nebyla provedena změna nebo došlo k chybě.');
			$this->redirect('this');
		}
	}

	public function actionDelete($id): void
	{
		try{
			$deleteSuccessful = $this->purchaseRepository->delete($id) === 1;			
		} catch (\Exception $ex) {
			$this->flashMessage('Došlo k chybě.');
			$this->redirect('Purchase:default');
		}	

		if($deleteSuccessful){
			$this->flashMessage('Objednávka úspěšně smazána.');
		} else {
			$this->flashMessage('Něco se pokazilo.');			
		}
		$this->redirect('Purchase:default');
	}
}
