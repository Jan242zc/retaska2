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

	public function __construct(IPurchaseStatusRepository $purchaseStatusRepository){
		$this->purchaseStatusRepository = $purchaseStatusRepository;
	}
	
	public function renderDefault(): void
	{
	}
	
	public function actionManage($id = null): void
	{
	
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
		
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'managePurchaseStatusEntityFormSucceeded'];
		
		return $form;
	}
	
	public function managePurchaseStatusEntityFormSucceeded(Form $form, Array $data): void
	{
		$purchaseStatus = PurchaseStatusFactory::createFromArray($data);
		
		if(!$nameIsOriginal = $this->verifyNameOriginality($purchaseStatus)){
			$this->flashMessage('Druh stavu objednávky s tímto názvem již existuje. Zvolte jiný.');
			$this->redirect('this');
		}
		
		if(!$purchaseStatus->getId()){
			//try{
				$rowsAffected = $this->purchaseStatusRepository->insert($purchaseStatus);
			// } catch (\Exception $ex) {
				// $this->flashMessage('Počet možných ID je nižší než počet stavů objednávky, zvyšte jej.');
				// $this->redirect('Entity:default');
			// }
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
