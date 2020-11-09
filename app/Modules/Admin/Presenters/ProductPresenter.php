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
	private const RESOURCE = 'generalAdmin';

	private $productRepository;
	private $categoryRepository;
	private $entityRepository;
	private $productFactory;
	
	public function __construct(IProductRepository $productRepository, ICategoryRepository $categoryRepository, ProductFactory $productFactory){
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->productFactory = $productFactory;
	}
	
	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		$this->template->products = $this->productRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$product = $this->productRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Zboží nebo kategorie nenalezeny.');
				$this->redirect('Product:default');
			}
			$formDefaults = $product->toArray();
		}
		$this['manageProductForm']->setDefaults($formDefaults);
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
			->addRule($form::FLOAT, 'Cena musí být kladné číslo.')
			->addFilter(function($value){
				return floatval($value);
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
		$product = $this->productFactory->createFromArray($data);
		
		if(!$product->getId()){
			try{
				$rowsAffected = $this->productRepository->insert($product);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet kategorií, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Zboží uloženo.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->productRepository->update($product) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		if(!$nameIsOriginal = $this->verifyNameOriginality($product)){
			$this->flashMessage('Upozornění: zboží s tímto názvem již existuje.');
		}
		$this->redirect('Product:default');
	}
	
	public function actionDelete($id): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		if($this->productRepository->delete($id) === 1){
			$this->flashMessage('Zboží vymazáno.');
		} else {
			$this->flashMessage('Zboží nenalezeno.');
		}
		$this->redirect('Product:default');
	}
	
	private function verifyNameOriginality($product): bool
	{
		if(is_null($product->getId())){	
			$usedNames = $this->productRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->productRepository->getArrayOfUsedNames($product->getId());
		}

		return !in_array(trim(mb_strtolower($product->getName())), $usedNames);
	}
}
