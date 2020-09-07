<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Entity\Product;
use App\Entity\Factory\ProductFactory;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;


final class ProductPresenter extends BasePresenter
{
	private $productRepository;
	private $categoryRepository;
	
	public function __construct(IProductRepository $productRepository, ICategoryRepository $categoryRepository){
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
	}
	
	public function renderDefault(): void
	{
		
	}
	
	public function actionManage(): void
	{
		
	}
	
	protected function createComponentManageProductForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id')
			->addFilter(function($value){
					return intval($value);
				});
		
		$form->addText('name', 'Název:')
			->setRequired('Zboží musí mít název');
			
		$form->addText('price', 'Cena:')
			->setRequired('Zboží musí mít cenu.')
			->addRule($form::NUMERIC, 'Cena musí být kladné číslo.')
			->addFilter(function($value){
				return intval($value);
			});
			
		$form->addSelect('category', 'Kategorie:')
			->setRequired('Zboží musí mít určenou kategorii.')
			->setPrompt('Vyberte kategorii.')
			->setItems($this->categoryRepository->findAllForForm());
		
		$form->addText('material', 'Materiál:')
			->setRequired('Zboží musí mít určený materiál.');
			
		$form->addText('amountAvailable', 'Množství na skladě:')
			->setRequired('Zboží musí mít uvedené množství na skladě.')
			->addRule($form::NUMERIC, 'Množství zboží musí být celé kladné číslo.')
			->addFilter(function($value){
				return intval($value);
			});
			
		$form->addTextArea('description', 'Popis:')
			->setRequired('Zboží musí mít popis.');
			
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'manageProductFormSucceeded'];
		
		return $form;
	}
	
	public function manageProductFormSucceeded(Form $form, Array $data): void
	{
		dump($data);
		exit;
	}
}
