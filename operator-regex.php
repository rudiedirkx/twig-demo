<?php

list($twigMaker, $debugTemplate) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

class Project_Twig_Extension extends Twig_Extension {
	public function getOperators() {
		return array(
			array(),
			array(
				're' => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Regex', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
			),
		);
	}
}

class Twig_Node_Expression_Binary_Regex extends Twig_Node_Expression_Binary {
	public function compile(Twig_Compiler $compiler) {
		$compiler
			->raw('(preg_match(')
			->subcompile($this->getNode('right'))
			->raw(', ')
			->subcompile($this->getNode('left'))
			->raw(') > 0)')
		;
	}

	public function operator(Twig_Compiler $compiler) {
	}
}

$twig = $twigMaker(function(Twig_Environment $twig) {
	$twig->addExtension(new Project_Twig_Extension());
});
$template = $twig->load("operator-regex.twig");
$output = $template->render([
	'subject' => rand(0, 1) ? date('Y-m-d') : date('H:i:s'),
	'regex' => '/^\d+-\d+-\d+$/i',
]);

// var_dump(get_class($template));
// var_dump($twig->getCache(false)->generateKey($template->getSourceContext()->getName(), $twig->getTemplateClass($template->getSourceContext()->getName())));

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
