<?php

list($twigMaker, $debugTemplate) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

class Project_Twig_Extension extends Twig_Extension {
	public function getOperators() {
		return array(
			array(),
			array(
				'@' => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_At', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
			),
		);
	}
}

class Twig_Node_Expression_Binary_At extends Twig_Node_Expression_Binary {
	public function compile(Twig_Compiler $compiler) {
		// return $compiler->raw("''");

		$left = $this->getNode('left');
		$right = $this->getNode('right');

		if (!$left instanceof Twig_Node_Expression_Name || !$right instanceof Twig_Node_Expression_Name) {
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

	public function operator(Twig_Compiler $compiler) {}
}

$twig = $twigMaker(function(Twig_Environment $twig) {
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
