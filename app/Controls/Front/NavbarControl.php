<?php

declare(strict_types=1);

namespace App\Controls\Front;

use Nette;
use Nette\Application\UI\Control;

final class NavbarControl extends Control
{
	public function __construct(){

	}
	
	public function render(): void
	{
		$this->template->render(__DIR__ . '/templates/navbar.latte');
	}
}
