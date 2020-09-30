<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;
use Nette\Application\UI\Form;
use App\Entity\Factory\PaymentFactory;


final class PaymentPresenter extends BasePresenter
{
	/** @var IPaymentRepository */
	private $paymentRepository;
	
	public function __construct(IPaymentRepository $paymentRepository){
		$this->paymentRepository = $paymentRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->payments = $this->paymentRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$payment = $this->paymentRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Druh platby nenalezen.');
				$this->redirect('Payment:default');
			}
			$formDefaults = $payment->toArray();
		}
		$this['managePaymentForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManagePaymentForm(): Form
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
			
		$form->onSuccess[] = [$this, 'managePaymentFormSucceeded'];
		
		return $form;
	}
	
	public function managePaymentFormSucceeded(Form $form, Array $data): void
	{
		$payment = PaymentFactory::createFromArray($data);
		
		if(!$nameIsOriginal = $this->verifyNameOriginality($payment)){
			$this->flashMessage('Druh platby s tímto názvem již existuje. Zvolte jiný.');
			$this->redirect('this');
		}
		
		if(!$payment->getId()){
			try{
				$rowsAffected = $this->paymentRepository->insert($payment);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet druhů platby, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Druh platby uložen.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->paymentRepository->update($payment) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		$this->redirect('Payment:default');
	}
	
	public function actionDelete($id): void
	{
		if($this->paymentRepository->delete($id) === 1){
			$this->flashMessage('Druh platby smazán.');
		} else {
			$this->flashMessage('Druh platby nenalezen.');
		}
		$this->redirect('Payment:default');
	}
	
	private function verifyNameOriginality($payment): bool
	{
		if(is_null($payment->getId())){	
			$usedNames = $this->paymentRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->paymentRepository->getArrayOfUsedNames($payment->getId());
		}

		return !in_array(trim(mb_strtolower($payment->getName())), $usedNames);
	}
}
