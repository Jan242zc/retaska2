<?php

declare(strict_types=1);

namespace App\Services;

use Nette\Security\Permission;
use App\Services\Repository\RepositoryInterface\IUserDataRepository;
use App\Services\Repository\RepositoryInterface\IRoleRepository;
use Nette\Security\IAuthorizator;


final class MyAuthorizator
{
	public static function create(IUserDataRepository $userDataRepository, IRoleRepository $roleRepository): Permission
	{
		$acl = new Permission();
		
		$acl->addRole('guest');
		
		for($roles = $roleRepository->findAll(), $i = 0; $i < count($roles); $i++){
			if($i === 0){
				$acl->addRole($roles[$i]->getName(), 'guest');
			} else {				
				$acl->addRole($roles[$i]->getName(), $roles[$i - 1]->getName());
			}
		}
		
		$acl->addResource('generalAdmin');
		$acl->addResource('rolesAdmin');
		$acl->addResource('usersAdmin');
		
		$acl->deny('guest', 'generalAdmin', 'read');
		$acl->allow('admin', 'generalAdmin', 'read');
		$acl->allow('superadmin', 'usersAdmin', 'read');
		$acl->allow('superadmin', 'usersAdmin', 'create');
		$acl->allow('superadmin', 'usersAdmin', 'update');
		$acl->allow('superadmin', 'usersAdmin', 'delete');
		
		return $acl;
	}
}
