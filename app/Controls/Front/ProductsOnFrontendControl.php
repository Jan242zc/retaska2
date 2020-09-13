<?php

declare(strict_types=1);

namespace App\Controls\Front;

use Nette;
use Nette\Utils\Paginator;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use Nette\Application\UI\Control;


final class ProductsOnFrontendControl extends Control
{
	/** @var IProductRepository */
	private $productRepository;

	/** @var Paginator */
	private $paginator;
	
	public function __construct(IProductRepository $productRepository, Paginator $paginator){
		$this->productRepository = $productRepository;
		$this->paginator = $paginator;
	}
	
	public function render(int $page, int $productsPerPage): void
	{
		$numberOfProducts = $this->productRepository->getProductsCount();
		$this->paginator->setItemCount($numberOfProducts);
		$this->paginator->setItemsPerPage($productsPerPage);
		$this->paginator->setPage($page);
		$this->template->products = $this->productRepository->findAll($this->paginator->getLength(), $this->paginator->getOffset());
		
		$this->template->page = $page;
		$maxPages = round($numberOfProducts / $productsPerPage);
		$this->template->maxPages = $maxPages;
		
		$productsPerPageBaseValue = 8;
		$maxProductsPerPage = 64;
		$this->template->productsPerPageBaseValue = $productsPerPageBaseValue;
		$this->template->maxProductsPerPage = $maxProductsPerPage;
		$this->template->itemsPerPage = $productsPerPage;
		
		$this->template->currentPresenterAndAction = ':' . $this->getPresenter()->getName() .':'. $this->getPresenter()->getAction();
		$this->template->render(__DIR__ . '\templates\productsOnFrontend.latte');
	}
}
