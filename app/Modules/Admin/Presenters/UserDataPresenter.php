<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette\Application\UI\Form;
use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Services\Repository\RepositoryInterface\IUserDataRepository;
use App\Services\Repository\RepositoryInterface\IRoleRepository;
use App\Entity\Factory\UserDataFactory;


final class UserDataPresenter extends BasePresenter
{
	private const RESOURCE = 'usersAdmin';
	
	/** @var IUserDataRepository */
	private $userDataRepository;
	
	/** @var UserDataFactory */
	private $userDataFactory;
	
	/** @var IRoleRepository */
	private $roleRepository;
	
	public function __construct(IUserDataRepository $userDataRepository, IRoleRepository $roleRepository, UserDataFactory $userDataFactory){
		$this->userDataRepository = $userDataRepository;
		$this->roleRepository = $roleRepository;
		$this->userDataFactory = $userDataFactory;
	}

	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE, 'read');
		$this->template->userData = $this->userDataRepository->findAll();
	}

	public function actionCreate(): void
	{
		$this->allowOrRedirect(self::RESOURCE, 'create');
		$this['createUserDataForm']->setDefaults(['id' => null]);
	}

	protected function createComponentCreateUserDataForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id');
		
		$form->addText('name', 'Název:')
			->setRequired('Uživatel musí mít jméno.');

		$form->addSelect('role', 'Role:')
			->setPrompt('Zvolte roli.')
			->setItems($this->roleRepository->findAllForForm());

		$form->addPassword('password', 'Heslo')
			->addRule($form::MIN_LENGTH, 'Heslo musí mít nejměně %d znaků.', 4);

		$form->addPassword('passwordVerif', 'Ověření hesla:')
			->addRule($form::EQUAL, 'Hesla se neshodují.', $form['password']);

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'createUserDataFormSucceeded'];

		return $form;
	}

	public function createUserDataFormSucceeded(Form $form, Array $data): void
	{
		$userData = $this->userDataFactory->createFromArray($data);

		if(!$nameIsOriginal = $this->verifyNameOriginality($userData)){
			$this->flashMessage('Uživatel s tímto jménem již existuje. Zvolte jiné.');
			$this->redirect('this');
		}

		try{
			$rowsAffected = $this->userDataRepository->insert($userData);
		} catch (\Exception $ex) {
			$this->flashMessage('Počet možných ID je nižší než počet rolí, zvyšte jej.');
			$this->redirect('Entity:default');
		}
		if($rowsAffected === 1){
			$this->flashMessage('Uživatel uložen.');
		} else {
			$this->flashMessage('Něco se pokazilo.');
		}

		$this->redirect('UserData:default');
	}

	public function actionEdit($id): void
	{
		$this->allowOrRedirect(self::RESOURCE, 'update');
		if(!$id){
			$this->redirect('UserData:default');
		} else {
			try{
				$userData = $this->userDataRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Uživatel nenalezen.');
				$this->redirect('UserData:default');
			}
			$formDefaults = $userData->toArray();
		}
		$this['editUserDataForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentEditUserDataForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id');
		
		$form->addText('name', 'Název:')
			->setRequired('Uživatel musí mít jméno.');

		$form->addSelect('role', 'Role:')
			->setPrompt('Zvolte roli.')
			->setItems($this->roleRepository->findAllForForm());

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'editUserDataFormSucceeded'];

		return $form;
	}
	
	public function editUserDataFormSucceeded(Form $form, Array $data): void
	{
		$userData = $this->userDataFactory->createFromArray($data);
		
		try{
			$affectedRows = $this->userDataRepository->update($userData);			
		} catch(\Exception $ex){
			$this->flashMessage('Došlo k chybě.');
			if($ex->getMessage() === 'At least one superadmin must exist.'){
				$this->flashMessage('Musí zůstat alespoň jeden superadmin.');
			}
			$this->redirect('UserData:default');
		}
		
		if($affectedRows === 1){
			$this->flashMessage('Změny uloženy.');
		} else {
			$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
		}
	}

	public function actionDelete($id): void
	{
		$this->allowOrRedirect(self::RESOURCE, 'delete');
		
		try{
			$affectedRows = $this->userDataRepository->delete($id);			
		} catch(\Exception $ex){
			$this->flashMessage('Došlo k chybě.');
			if($ex->getMessage() === 'At least one superadmin must exist.'){
				$this->flashMessage('Musí zůstat alespoň jeden superadmin.');
			}
			$this->redirect('UserData:default');
		}
		
		if($affectedRows === 1){
			$this->flashMessage('Uživatel smazán.');
		} else {
			$this->flashMessage('Uživatel nenalezen.');
		}
		$this->redirect('UserData:default');
	}

	private function verifyNameOriginality($userData): bool
	{
		if(is_null($userData->getId())){	
			$usedNames = $this->userDataRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->userDataRepository->getArrayOfUsedNames($userData->getId());
		}

		return !in_array(trim(mb_strtolower($userData->getName())), $usedNames);
	}
}
