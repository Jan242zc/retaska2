<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Modules\Front\Presenters\BaseFrontPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IProductRepository;


final class HomepagePresenter extends BasePresenter
{
	private $productRepository;
	
	public function __construct(IProductRepository $productRepository){
		$this->productRepository = $productRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->topProducts = $this->productRepository->findTopProducts(4);
	}
}
