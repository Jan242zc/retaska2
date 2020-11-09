<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\UserData;
use App\Entity\Role;
use App\Services\Repository\RepositoryInterface\IRoleRepository;


final class UserDataFactory
{
	/** @var IRoleRepository */
	private $roleRepository;
	
	public function __construct(IRoleRepository $roleRepository){
		$this->roleRepository = $roleRepository;
	}
	
	public function createFromArray(Array $data): UserData
	{
		if(!$data['id']){
			$data['id'] = null;
		}
		
		if(!isset($data['password']) || !$data['password']){
			$data['password'] = null;
		}
		
		try{
			$role = $this->findRoleForUserData($data['role']);			
		} catch(\Exception $ex){
			throw $ex;
		}
		
		return new UserData($data['id'], $data['name'], $role, $data['password']);
	}
	
	public function createFromObject($object): UserData
	{
		if(!$object->id){
			$object->id = null;
		}
		
		if(!$object->password){
			$object->password = null;
		}
		
		try{
			$role = $this->findRoleForUserData($object->role);
		} catch(\Exception $ex){
			throw $ex;
		}
		
		return new UserData($object->id, $object->name, $role, $object->password);
	}
	
	private function findRoleForUserData($id): Role
	{
		try{
			return $this->roleRepository->findById(intval($id));			
		} catch(\Exception $ex){
			throw $ex;
		}
	}
}
