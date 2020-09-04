<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;


final class EntityPresenter extends BasePresenter
{
	private $entityRepository;
	
	public function __construct(IEntityRepository $entityRepository){
		$this->entityRepository = $entityRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->entities = $this->entityRepository->findAll();
	}
}
