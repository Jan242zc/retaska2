<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Modules\Front\Presenters\BaseFrontPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;
use App\Controls\Front\ProductsOnFrontendControl;
use App\Controls\Front\Factory\IProductsOnFrontendControlFactory;


/**
* @persistent(productsOnFrontendControl)
*/

final class ProductPresenter extends BasePresenter
{
	private $productRepository;
	private $categoryRepository;
	private $productsOnFrontendControlFactory;
	
	public function __construct(IProductRepository $productRepository, ICategoryRepository $categoryRepository, IProductsOnFrontendControlFactory $productsOnFrontendControlFactory){
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->productsOnFrontendControlFactory = $productsOnFrontendControlFactory;
	}
	
	public function renderDefault(int $page = 1, int $productsPerPage = 8): void
	{
		$this->template->categories = $this->categoryRepository->findAll();
		
		$this->template->page = $page;
		$this->template->productsPerPage = $productsPerPage;
	}
	
	protected function createComponentProductsOnFrontendControl(): ProductsOnFrontendControl
	{
		return $this->productsOnFrontendControlFactory->create();
	}
}
