<?php

use Twig\Environment;
use Twig\Lexer;
use Twig\TemplateWrapper;

list($twigMaker) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

$vars = json_decode(file_get_contents('index.json'), true);

$compile = function(Environment $twig, string $template) {
	$_time = microtime(1);
	$template = $twig->load($template);
	echo 'compile: ';
	var_dump((microtime(1) - $_time) * 1000);
	return $template;
};

$render = function(TemplateWrapper $template) use ($vars) {
	$line = str_repeat(str_repeat('=', 40) . "\n", 1);

	$_time = microtime(1);
	$output = $template->render($vars);
	echo 'render: ';
	var_dump((microtime(1) - $_time) * 1000);
	echo "{$line}" . trim($output) . "\n{$line}\n\n";
};

// Normal Twig
$runNormalTwig = function() use ($twigMaker, $compile, $render) {
	$twig = $twigMaker();
	$template = $compile($twig, 'tpl.index.twig');
	$render($template);
};

// PHP style
$runPhpStyle = function() use ($twigMaker, $compile, $render) {
	$twig = $twigMaker(function(Environment $twig) {
		$lexer = new Lexer($twig, array(
		    'tag_comment'   => array('<?#', '#?>'),
		    'tag_block'     => array('<?', '?>'),
		    'tag_variable'  => array('<?=', '?>'),
		    'interpolation' => array('#<?', '?>'),
		));
		$twig->setLexer($lexer);
	});
	$template = $compile($twig, 'tpl.index.php');
	$render($template);
};

$runPhpStyle();
$runNormalTwig();
