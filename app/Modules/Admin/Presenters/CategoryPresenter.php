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
			$formDefaults = $category->toArray();
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
			->setRequired('Kategorie musí mít název.')
			->addFilter(function($value){
				return ucfirst(mb_strtolower($value));
			});

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'manageCategoryFormSucceeded'];

		return $form;		
	}
	
	public function manageCategoryFormSucceeded(Form $form, Array $data): void
	{
		$category = CategoryFactory::createFromArray($data);
		
		//this verification must be done in the *Succeeded method as $form['id']->getValue() didn't work for me
		if(!$nameIsOriginal = $this->verifyNameOriginality($category)){
			$this->flashMessage('Kategorie s tímto názvem již existuje.');
			$this->redirect('this');
		}
		
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
	
	public function actionDelete($id){
		if($this->categoryRepository->delete($id) === 1){
			$this->flashMessage('Kategorie smazána.');
		} else {
			$this->flashMessage('Něco se pokazilo.');
		}
		$this->redirect('Category:default');
	}
	
	private function verifyNameOriginality($category): bool
	{
		if(is_null($category->getId())){	
			$usedNames = $this->categoryRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->categoryRepository->getArrayOfUsedNames($category->getId());
		}

		return !in_array(trim(mb_strtolower($category->getName())), $usedNames);
	}
}