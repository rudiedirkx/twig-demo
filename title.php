<?php

use Twig\Compiler;
use Twig\Environment;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TwigFunction;

list($twigMaker) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

class TitleTokenParser extends AbstractTokenParser {
	public function parse(Token $token) {
// var_dump($token);

		$parser = $this->parser;
		$stream = $parser->getStream();

		$stream->expect(Token::OPERATOR_TYPE, '=');
// var_dump($stream->getCurrent());

		$node = $parser->getExpressionParser()->parseExpression();
// var_dump($node);

		$stream->expect(Token::BLOCK_END_TYPE);

		if ($node instanceof NameExpression) {
			$title = $node->getAttribute('name');
			if (strtoupper($title) === $title) {
				return new TranslatedTitleNode($node, $token->getLine(), $this->getTag());
			}
		}

		return new CompositeTitleNode($node, $token->getLine(), $this->getTag());
	}

	public function getTag() {
		return 'title';
	}
}

class TranslatedTitleNode extends Node {
	public function __construct($title, $line, $tag) {
		parent::__construct(compact('title'), [], $line, $tag);
	}

	public function compile(Compiler $compiler) {
		$node = $this->getNode('title');
		$title = $node->getAttribute('name');

		$compiler
			->addDebugInfo($this)
			->write("\$context['title'] = trans('$title');\n")
		;
	}
}

class CompositeTitleNode extends Node {
	public function __construct($title, $line, $tag) {
		parent::__construct(compact('title'), [], $line, $tag);
	}

	public function compile(Compiler $compiler) {
		$node = $this->getNode('title');

		$compiler
			->addDebugInfo($this)
			->write("\$context['title'] = ")
			->subcompile($node)
			->raw(";\n")
		;
	}
}

function trans($key) {
	return ucfirst(strtolower(str_replace('_', ' ', $key)));
}

$vars = [
	'pageTitle' => 'Given title?',
	'composite' => time() % 2,
];

$twig = $twigMaker(function(Environment $twig) {
	// $twig->setCache(false);
	$twig->addFunction(new TwigFunction('t', function($key) {
		return trans($key);
	}));
	$twig->addTokenParser(new TitleTokenParser());
});
$template = $twig->load("title-page.twig");
$output = $template->render($vars);

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
