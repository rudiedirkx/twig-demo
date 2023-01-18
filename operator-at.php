<?php

use Twig\Compiler;
use Twig\Environment;
use Twig\ExpressionParser;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\Binary\AbstractBinary;
use Twig\Node\Expression\NameExpression;

list($twigMaker, $debugTemplate) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

class Project_Twig_Extension extends AbstractExtension {
	public function getOperators() : array {
		return array(
			array(),
			array(
				'@' => array(
					'precedence' => 30,
					'class' => 'Twig_Node_Expression_Binary_At',
					'associativity' => ExpressionParser::OPERATOR_LEFT,
				),
			),
		);
	}
}

class Twig_Node_Expression_Binary_At extends AbstractBinary {
	public function compile(Compiler $compiler) : void {
		$left = $this->getNode('left');
		$right = $this->getNode('right');

		if (!$left instanceof NameExpression || !$right instanceof NameExpression) {
			throw new Twig_Error_Syntax("Left and Right of @ must be names.");
		}

		$compiler
			->raw("'")
			->raw($left->getAttribute('name'))
			->raw('@')
			->raw($right->getAttribute('name'))
			->raw("'")
		;
	}

	public function operator(Compiler $compiler) : Compiler {
		return $compiler;
	}
}

$twig = $twigMaker(function(Environment $twig) {
	$twig->addExtension(new Project_Twig_Extension());
});
$template = $twig->load("operator-at.twig");
$output = $template->render([
	'items' => ['a', 'b', 'c'],
]);

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
