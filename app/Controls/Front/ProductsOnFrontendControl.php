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
	
	/** @var int */
	public $productsPerPageBaseValue;
	
	/** @var int */
	public $maxProductsPerPage;
	
	/** @var int */
	/** @persistent */
	public $page = 1;
	
	/** @var int */
	/** @persistent */
	public $productsPerPage = 8;
	
	public function __construct(IProductRepository $productRepository, Paginator $paginator){
		$this->productRepository = $productRepository;
		$this->paginator = $paginator;
	}
	
	public function render($category = null): void
	{
		if(is_null($category)){
			$numberOfProducts = $this->productRepository->getProductsCount();
		} else {
			$numberOfProducts = $this->productRepository->getProductsCountByCategory($category);
		}
		
		$this->setUpPaginator($numberOfProducts);
		$this->setUpPageButtons($numberOfProducts);
		$this->setUpProductsPerPageButtons($this->productsPerPageBaseValue, $this->maxProductsPerPage);
		
		if(is_null($category)){
			$this->template->products = $this->productRepository->findAll($this->paginator->getLength(), $this->paginator->getOffset());
		} else {
			$this->template->products = $this->productRepository->findByCategory($category, $this->paginator->getLength(), $this->paginator->getOffset());
		}
		
		$this->template->render(__DIR__ . '\templates\productsOnFrontend.latte');
	}
	
	public function handleChangePage(int $page): void
	{
		$this->page = $page;
		$this->getPresenter()->redirect('this');
	}
	
	public function handleChangeProductsPerPage(int $productsPerPage): void
	{
		$this->productsPerPage = $productsPerPage;
		$this->getPresenter()->redirect('this');
	}
	
	private function setUpPaginator($numberOfProducts): void
	{
		$this->paginator->setItemCount($numberOfProducts);
		$this->paginator->setItemsPerPage($this->productsPerPage);
		$this->paginator->setPage($this->page);
	}
	
	private function setUpPageButtons($numberOfProducts): void
	{
		$this->template->page = $this->page;
		$maxPages = round($numberOfProducts / $this->productsPerPage);
		$this->template->maxPages = $maxPages;
	}
	
	private function setUpProductsPerPageButtons($productsPerPageBaseValue, $maxProductsPerPage): void
	{
		$this->template->productsPerPageBaseValue = $productsPerPageBaseValue;
		$this->template->maxProductsPerPage = $maxProductsPerPage;
		$this->template->itemsPerPage = $this->productsPerPage;
	}
}
