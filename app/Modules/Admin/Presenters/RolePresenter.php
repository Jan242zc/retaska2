<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Services\Repository\RepositoryInterface\IRoleRepository;
use App\Entity\Factory\RoleFactory;
use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use Nette\Application\UI\Form;


final class RolePresenter extends BasePresenter
{
	private const RESOURCE = 'rolesAdmin';
	
	/** @var IRoleRepository */
	private $roleRepository;
	
	/** @var RoleFactory */
	private $roleFactory;
	
	public function __construct(IRoleRepository $roleRepository, RoleFactory $roleFactory){
		$this->roleRepository = $roleRepository;
		$this->roleFactory = $roleFactory;
	}
	
	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		$this->template->roles = $this->roleRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$role = $this->roleRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Uživatelská role nenalezena.');
				$this->redirect('Role:default');
			}
			$formDefaults = $role->toArray();
		}
		$this['manageRoleForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManageRoleForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');

		$form->addHidden('id');

		$form->addText('name', 'Název:')
			->setRequired('Role musí mít název.');

		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'manageRoleFormSucceeded'];

		return $form;
	}
	
	public function manageRoleFormSucceeded(Form $form, Array $data): void
	{
		$role = $this->roleFactory->createFromArray($data);
		
		if(!$nameIsOriginal = $this->verifyNameOriginality($role)){
			$this->flashMessage('Role s tímto názvem již existuje. Zvolte jiný.');
			$this->redirect('this');
		}
		
		if(!$role->getId()){
			try{
				$rowsAffected = $this->roleRepository->insert($role);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet rolí, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Role uložena.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			try{
				$affectedRows = $this->roleRepository->update($role);
			} catch(\Exception $ex){
				$this->flashMessage('Došlo k chybě.');
				if($ex->getMessage() === 'Superadmin cannot be renamed.'){
					$this->flashMessage('Role superadmina nesmí být přejmenována.');
				}
				$this->redirect('Role:default');
			}
			if($affectedRows === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		$this->redirect('Role:default');
	}

	public function actionDelete($id): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		
		try{
			$affectedRows = $this->roleRepository->delete($id);
		} catch(Nette\Database\ForeignKeyConstraintViolationException $ex){
			$this->flashMessage('Tento roli smazat nesmíte, protože je přiřazena nějakému uživateli.');
		} catch (\Exception $ex){
			$this->flashMessage('Došlo k chybě.');
		} finally {
			$this->redirect('Role:default');
		}
		
		if($affectedRows === 1){
			$this->flashMessage('Uživatelská role smazána.');
		} else {
			$this->flashMessage('Uživatelská role nenalezena.');
		}
		$this->redirect('Role:default');
	}

	private function verifyNameOriginality($role): bool
	{
		if(is_null($role->getId())){	
			$usedNames = $this->roleRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->roleRepository->getArrayOfUsedNames($role->getId());
		}

		return !in_array(trim(mb_strtolower($role->getName())), $usedNames);
	}
}
