<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require 'vendor/autoload.php';

umask(0);

$twigMaker = function(callable $processor = null) {
	$loader = new FilesystemLoader(__DIR__);
	$twig = new Environment($loader, array(
		'debug' => false,
		'cache' => __DIR__ . '/cache',
		'auto_reload' => true,
	));
	$processor and $processor($twig);
	return $twig;
};

return [$twigMaker];
