<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;


final class CategoryPresenter extends BasePresenter
{
	public function renderDefault(): void
	{
		
	}
	
	public function actionManage(): void
	{
		
	}
	
	protected function createComponentManageCategoryForm(): Form
	{
		$form = new Form;
		$form->setHtmlAttribute('class', 'form');
				
		$form->addHidden('id');
		
		$form->addText('name', 'Název:')
			->setRequired('Kategorie musí mít název.');
			
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
		
		$form->onSuccess[] = [$this, 'manageCategoryFormSucceeded'];
		
		return $form;		
	}
}
