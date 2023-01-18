<?php

use Twig\Compiler;
use Twig\Environment;
use Twig\ExpressionParser;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\Binary\AbstractBinary;

list($twigMaker, $debugTemplate) = require 'inc.bootstrap.php';

// header('Content-type: text/plain; charset=utf-8');

class Project_Twig_Extension extends AbstractExtension {
	public function getOperators() : array {
		return array(
			array(),
			array(
				'===' => array(
					'precedence' => 100,
					'class' => 'Twig_Node_Expression_Binary_Identical',
					'associativity' => ExpressionParser::OPERATOR_LEFT,
				),
				'!==' => array(
					'precedence' => 100,
					'class' => 'Twig_Node_Expression_Binary_NotIdentical',
					'associativity' => ExpressionParser::OPERATOR_LEFT,
				),
			),
		);
	}
}

class Twig_Node_Expression_Binary_Identical extends AbstractBinary {
	public function operator(Compiler $compiler) : Compiler {
		return $compiler->raw(' === ');
	}
}

class Twig_Node_Expression_Binary_NotIdentical extends AbstractBinary {
	public function operator(Compiler $compiler) : Compiler {
		return $compiler->raw(' !== ');
	}
}

$twig = $twigMaker(function(Environment $twig) {
	$twig->addExtension(new Project_Twig_Extension());
});
$template = $twig->load("operator-identical.twig");
$output = $template->render([]);

// var_dump(get_class($template));
// var_dump($twig->getCache(false)->generateKey($template->getSourceContext()->getName(), $twig->getTemplateClass($template->getSourceContext()->getName())));

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
