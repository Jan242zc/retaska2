<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Entity\Factory\PurchaseStatusFactory;
use Nette\Application\UI\Form;


final class PurchaseStatusPresenter extends BasePresenter
{
	public function __construct(){
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
		
		$form->addSelect('meansCancelled', 'Znamená, že objednávka je zrušena:')
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
		dump($data);
		exit;
	}
}
