<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Services\Repository\RepositoryInterface\IRoleRepository;
use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use Nette\Application\UI\Form;


final class RolePresenter extends BasePresenter
{
	private const RESOURCE = 'rolesAdmin';
	
	/** @var IRoleRepository */
	private $roleRepository;
	
	public function __construct(IRoleRepository $roleRepository){
		$this->roleRepository = $roleRepository;
	}
	
	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		$this->template->roles = $this->roleRepository->findAll();
	}
}
