<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Modules\Front\Presenters\BaseFrontPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;
use App\Controls\Front\ProductsOnFrontendControl;
use App\Controls\Front\Factory\IProductsOnFrontendControlFactory;
use Nette\Application\UI\Form;


/**
* @persistent(productsOnFrontendControl)
*/

final class ProductPresenter extends BasePresenter
{
	/** @var IProductRepository */
	private $productRepository;
	
	/** @var ICategoryRepository */
	private $categoryRepository;
	
	/** @var IProductsOnFrontendControlFactory */
	private $productsOnFrontendControlFactory;
	
	public function __construct(IProductRepository $productRepository, ICategoryRepository $categoryRepository, IProductsOnFrontendControlFactory $productsOnFrontendControlFactory){
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->productsOnFrontendControlFactory = $productsOnFrontendControlFactory;
	}
	
	public function renderDefault($category = null): void
	{
		$this->template->categories = $this->categoryRepository->findAll();
		$this->template->currentCategory = $category;
	}
	
	protected function createComponentProductsOnFrontendControl(): ProductsOnFrontendControl
	{
		return $this->productsOnFrontendControlFactory->create();
	}
	
	public function renderDetail($id): void
	{
		$this->template->product = $this->productRepository->find($id);
		$this['addToBasketForm']->setDefaults(['product' => $this->productRepository->find($id)->getId()]);
	}
	
	protected function createComponentAddToBasketForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'add-to-basket');
		
		$form->addHidden('product')
			->setHtmlAttribute('id', 'product-id');
		
		
		$form->addText('quantity', 'Přidat do košíku')
			->setHtmlAttribute('class', 'quantity')
			->setDefaultValue(5)
			->setRequired('Je nutné zadat počet kusů.')
			->addRule($form::NUMERIC, 'Množství zboží musí být celé kladné číslo.')
			->addFilter(function($value){
				return intval($value);
			});
		
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'addToBasketFormSucceeded'];
		
		return $form;
	}
	
	public function addToBasketFormSucceeded(Form $form, Array $data)
	{
		dump($data);
		exit;
	}
}
