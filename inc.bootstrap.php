<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

require 'vendor/autoload.php';

umask(0);

// @todo Better inline/string templates
// @todo HtmlString-ish

class MyTwigStringLoader implements LoaderInterface {
	public function getSourceContext($source) {
		return new Source($source, $source, '');
	}

	public function getCacheKey($source) {
		return sha1(trim($source));
	}

	public function isFresh($source, $time) {
		return false;
	}

	public function exists($source) {
		return true;
	}
}

$twigMaker = function(callable $processor = null) {
	$loader = new FilesystemLoader(__DIR__ . '/tpl');
	$twig = new Environment($loader, array(
		'debug' => false,
		'cache' => __DIR__ . '/cache',
		'auto_reload' => true,
	));
	$processor and $processor($twig);
	return $twig;
};

return [$twigMaker];
