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
		$this->template->categories = $this->categoryRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		if(is_null($id)){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$category = $this->categoryRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Kategorie nenalezena.');
				$this->redirect('Category:default');
			}
			
			//in case the category is found...
			$formDefaults = [
				'id' => $category->getId(),
				'name' => $category->getName()
			];
		}
		$this['manageCategoryForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManageCategoryForm(): Form
	{
		$form = new Form;
		$form->setHtmlAttribute('class', 'form');
				
		$form->addHidden('id')
			//converts sent value to int, as hidden field returns string as default
			->addFilter(function($value){
				return intval($value);
			});
		
		$form->addText('name', 'Název:')
			->setRequired('Kategorie musí mít název.');
			
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
		
		$form->onSuccess[] = [$this, 'manageCategoryFormSucceeded'];
		
		return $form;		
	}
	
	public function manageCategoryFormSucceeded(Form $form, Array $data): void
	{
		$category = CategoryFactory::createFromArray($data);
		if(!$category->getId()){
			if($this->categoryRepository->insert($category) === 1){
				$this->flashMessage('Kategorie uložena.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->categoryRepository->update($category) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		}
		$this->redirect('Category:default');
	}
}
