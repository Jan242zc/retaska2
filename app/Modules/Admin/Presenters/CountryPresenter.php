<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use Nette\Application\UI\Form;
use App\Entity\Factory\CountryFactory;


final class CountryPresenter extends BasePresenter
{
	/** @var ICountryRepository */
	private $countryRepository;
	
	public function __construct(ICountryRepository $countryRepository){
		$this->countryRepository = $countryRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->countries = $this->countryRepository->findAll();
	}
	
	public function actionManage($id = null): void
	{
		if(!$id){
			$formDefaults = [
				'id' => null
			];
		} else {
			try{
				$country = $this->countryRepository->find($id);
			} catch (\Exception $ex){
				$this->flashMessage('Zboží nebo kategorie nenalezeny.');
				$this->redirect('Country:default');
			}
			$formDefaults = $product->toArray();
		}
		$this['manageCountryForm']->setDefaults($formDefaults);
	}
	
	protected function createComponentManageCountryForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id')
			->addFilter(function($value){
					return intval($value);
				});
		
		$form->addText('name', 'Název:')
			->setRequired('Stát musí mít název.');
		
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
			
		$form->onSuccess[] = [$this, 'manageCountryFormSucceeded'];
		
		return $form;
	}
	
	public function manageCountryFormSucceeded(Form $form, Array $data): void
	{
		$country = CountryFactory::createFromArray($data);
		
		if(!$country->getId()){
			try{
				$rowsAffected = $this->countryRepository->insert($country);
			} catch (\Exception $ex) {
				$this->flashMessage('Počet možných ID je nižší než počet států, zvyšte jej.');
				$this->redirect('Entity:default');
			}
			if($rowsAffected === 1){
				$this->flashMessage('Zboží uloženo.');
			} else {
				$this->flashMessage('Něco se pokazilo.');
			}
		} else {
			if($this->countryRepository->update($country) === 1){
				$this->flashMessage('Změny uloženy.');
			} else {
				$this->flashMessage('Něco se pokazilo nebo nebyly provedeny žádné změny.');
			}
		}
		if(!$nameIsOriginal = $this->verifyNameOriginality($country)){
			$this->flashMessage('Upozornění: stát s tímto názvem již existuje.');
		}
		$this->redirect('Country:default');
	}
	
	private function verifyNameOriginality($country): bool
	{
		if(is_null($country->getId())){	
			$usedNames = $this->countryRepository->getArrayOfUsedNames();
		} else {
			$usedNames = $this->countryRepository->getArrayOfUsedNames($country->getId());
		}

		return !in_array(trim(mb_strtolower($country->getName())), $usedNames);
	}
}
