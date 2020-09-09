<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Modules\Front\Presenters\BaseFrontPresenter as BasePresenter;
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
	
	public function renderDefault(int $page = 1): void
	{
		$this->template->categories = $this->categoryRepository->findAll();

		$paginator = new Nette\Utils\Paginator;
		$paginator->setItemCount($this->productRepository->getProductsCount());
		$paginator->setItemsPerPage(8);
		$paginator->setPage($page);
		$this->template->products = $this->productRepository->findAll($paginator->getLength(), $paginator->getOffset());
	}
}
