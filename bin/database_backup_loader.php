<?php

declare(strict_types=1);

namespace Bin\Dp;

use App;
use Nette;

require __DIR__ . '\..\vendor\autoload.php';

$container = App\Bootstrap::bootForCli()
	->createContainer();
$db = $container->getByType(Nette\Database\Connection::class);

if(!file_exists('..\backupA.txt') || !file_exists('..\backupB.txt') || !file_exists('..\backupC.txt')){
	print('At least one of the backup files was not found. Download them from Github and save them to the root folder.');
	exit;
}

if(in_array('--reset', $_SERVER['argv'], true)){
	$db->query(file_get_contents('..\backupC.txt'));
} else {
	$db->query(file_get_contents('..\backupA.txt'));		
}

if(in_array('--wdata', $_SERVER['argv'], true)){
	$db->query(file_get_contents('..\backupB.txt'));	
}

if(is_null($error = error_get_last())){
	print('Action performed.');
}
