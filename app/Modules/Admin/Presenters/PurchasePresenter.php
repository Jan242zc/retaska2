<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Services\Repository\RepositoryInterface\IPurchaseRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use Nette\Application\UI\Form;


final class PurchasePresenter extends BasePresenter
{
	/** @var IPurchaseRepository */
	private $purchaseRepository;
	
	/** @var IPurchaseStatusRepository */
	private $purchaseStatusRepository;
	
	public function __construct(IPurchaseRepository $purchaseRepository, IPurchaseStatusRepository $purchaseStatusRepository){
		$this->purchaseRepository = $purchaseRepository;
		$this->purchaseStatusRepository = $purchaseStatusRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->purchases = $this->purchaseRepository->findAll();
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
		$newPurchaseStatus = $this->purchaseStatusRepository->findById(intval($data['status']));

		$purchase->setPurchaseStatus($newPurchaseStatus);
		$purchaseId = $purchase->getId();
		
		if($this->purchaseRepository->update($purchase) === 1){
			$this->flashMessage('Stav změněn.');
			$this->redirect('this');
		} else {
			$this->flashMessage('Nebyla provedena změna nebo došlo k chybě.');
			$this->redirect('this');
		}
	}
}
