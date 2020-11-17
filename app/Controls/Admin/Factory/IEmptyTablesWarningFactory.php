<?php

declare(strict_types=1);

namespace App\Controls\Admin\Factory;

use App\Controls\Admin\EmptyTablesWarning;


interface IEmptyTablesWarningFactory //Nette automatically generates a class that implements this interface
{
	public function create(): EmptyTablesWarning;
}
