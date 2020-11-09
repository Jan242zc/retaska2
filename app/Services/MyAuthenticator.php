<?php

namespace App\Services;

use Nette\Security\IAuthenticator;
use Nette\Security\Passwords;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use App\Services\Repository\RepositoryInterface\IUserDataRepository;


final class MyAuthenticator implements IAuthenticator
{
	/** @var IUserDataRepository */
	private $userDataRepository;
	
	/** @var Passwords */
	private $passwords;
	
	public function __construct(IUserDataRepository $userDataRepository, Passwords $passwords){
		$this->userDataRepository = $userDataRepository;
		$this->passwords = $passwords;
	}

	public function authenticate(Array $signFormData): IIdentity
	{
		$name = $signFormData['name'];
		$password = $signFormData['password'];
		
		try{			
			$user = $this->userDataRepository->findByName($name);
		} catch (\Exception $ex){
			throw new AuthenticationException('User not found.');
		}
		
		if(!$this->passwords->verify($password, $user->getPassword())){
			throw new AuthenticationException('Invalid password.');
		}
		
		return new Identity($user->getId(), $user->getRole()->getName(), ['username' => $user->getName()]);
	}
}