<?php

use Twig\Environment;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

list($twigMaker) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

$twig = $twigMaker();
$template = $twig->load("whitespace.twig");
$output = $template->render([
	'last_run' => rand(0, 1) ? date('Y-m-d') : null,
]);

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
