<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Entity\Product;
use App\Entity\Factory\ProductFactory;
use App\Services\Repository\RepositoryInterface\IProductRepository;


final class ProductPresenter extends BasePresenter
{
	private $productRepository;
	
	public function __construct(IProductRepository $productRepository){
		$this->productRepository = $productRepository;
	}
	
	public function renderDefault(): void
	{
		
	}
}
