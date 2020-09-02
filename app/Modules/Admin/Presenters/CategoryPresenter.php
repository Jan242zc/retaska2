<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Entity\Category;
use App\Entity\Factory\CategoryFactory;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;

final class CategoryPresenter extends BasePresenter
{
	private $categoryRepository;
	
	public function __construct(ICategoryRepository $categoryRepository){
		$this->categoryRepository = $categoryRepository;
	}
	
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
	
	public function manageCategoryFormSucceeded(Form $form, Array $data): void
	{
		$category = CategoryFactory::create($data);
		if(!$category->getId()){
			if($this->categoryRepository->insert($category) === 1){
				$this->flashMessage('Kategorie uložena.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			//call update method
			$this->flashMessage('Změny uloženy.');
		}
		$this->redirect('Category:default');
	}
}
