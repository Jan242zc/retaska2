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
use App\Services\GeneralServiceInterface\IBasketService;


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
	
	/** @var IBasketService */
	private $basketService;	
	
	public function __construct(IProductRepository $productRepository, ICategoryRepository $categoryRepository, IProductsOnFrontendControlFactory $productsOnFrontendControlFactory, IBasketService $basketService){
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->productsOnFrontendControlFactory = $productsOnFrontendControlFactory;
		$this->basketService = $basketService;
	}
	
	public function renderDefault($category = null): void
	{
		$this->template->categories = $this->categoryRepository->findAll();
		if(!is_null($category)){
			$this['productsOnFrontendControl']->setCategory(intval($category));
		}
		$this->template->currentCategory = $category;
	}
	
	protected function createComponentProductsOnFrontendControl(): ProductsOnFrontendControl
	{
		return $this->productsOnFrontendControlFactory->create();
	}
	
	public function renderDetail($id): void
	{
		if(!isset($id)){
			$this->redirect('Product:default');
		}
		$product = $this->productRepository->find($id);
		$this->template->product = $product;
		$formDefaults = [
			'product_id' => $product->getId(),
			'product_name' => $product->getName()
		];
		
		if($basketItem = $this->basketService->verifyThatThisItemInBasket(intval($id))){
			$formDefaults['quantity'] = $this->basketService->getBasketItemById($id)->getQuantity();
			$this->template->alreadyInBasket = true;
		} else {
			$this->template->alreadyInBasket = false;
		}
		
		$this['addToBasketForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentAddToBasketForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'add-to-basket border-left');
		
		$form->addHidden('product_id')
			->setHtmlAttribute('id', 'product-id');
			
		$form->addHidden('product_name');

		$form->addText('quantity', 'Přidat do košíku')
			->setHtmlAttribute('class', 'quantity')
			->setDefaultValue(1)
			->setRequired('Je nutné zadat počet kusů.')
			->addRule($form::NUMERIC, 'Množství zboží musí být celé kladné číslo.')
			->addRule($form::MIN, 'Do košíku můžete přidat minimálně jeden kus.', 1)
			->addFilter(function($value){
				return intval($value);
			});
		
		$form->addSubmit('submit', 'Přidat')
			->setHtmlAttribute('class', 'submit')
			->setHtmlAttribute('id', 'submit-button');
			
		$form->onSuccess[] = [$this, 'addToBasketFormSucceeded'];

		return $form;
	}
	
	public function addToBasketFormSucceeded(Form $form, Array $data)
	{
		$product = $this->productRepository->find($data['product_id'] . ' ' . $data['product_name']);
		if($product->getAmountAvailable() < $data['quantity']){
			$this->flashMessage('Tolik zboží na skladě nemáme.');
			$this->redirect('this');
		}
		$this->basketService->addProductToBasket($product, $data['quantity']);
		$this->redirect('Basket:default');
	}
}
