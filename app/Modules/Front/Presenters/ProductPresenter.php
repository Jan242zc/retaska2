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
	
	public function renderDefault(): void
	{
		$this->template->categories = $this->categoryRepository->findAll();
		$this->template->products = $this->productRepository->findAll();
	}
}
