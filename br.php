<?php

use Twig\Environment;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

list($twigMaker) = require 'inc.bootstrap.php';

header('Content-type: text/plain; charset=utf-8');

class TitleTokenParser extends AbstractTokenParser {
	public function parse(Token $token) {
		$parser = $this->parser;
		$stream = $parser->getStream();

		if ( $stream->test('key') ) {
			$stream->expect('key');
			$stream->expect(Token::OPERATOR_TYPE, '=');
			$key = $parser->getExpressionParser()->parseExpression();
			$stream->expect(Twig_Token::BLOCK_END_TYPE);

			return new TranslatedTitleNode($key, $token->getLine(), $this->getTag());
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		return new StreamTitleNode($token->getLine(), $this->getTag());
	}

	public function getTag() {
		return 'title';
	}
}

class EndTitleTokenParser extends AbstractTokenParser {
	public function parse(Token $token) {
		$parser = $this->parser;
		$stream = $parser->getStream();
		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		return new EndStreamTitleNode($token->getLine(), $this->getTag());
	}

	public function getTag() {
		return 'endtitle';
	}
}

class TranslatedTitleNode extends Node {
	public function __construct($key, $line, $tag) {
		parent::__construct(['key' => $key], [], $line, $tag);
	}

	public function compile(Twig_Compiler $compiler) {
		$compiler
			->addDebugInfo($this)
			->write('$context[\'title\'] = t(')
			->subcompile($this->getNode('key'))
			->raw(");\n");
	}
}

class StreamTitleNode extends Node {
	public function __construct($line, $tag) {
		parent::__construct([], [], $line, $tag);
	}

	public function compile(Twig_Compiler $compiler) {
		$compiler
			->addDebugInfo($this)
			->write("ob_start();\n");
	}
}

class EndStreamTitleNode extends Node {
	public function __construct($line, $tag) {
		parent::__construct([], [], $line, $tag);
	}

	public function compile(Twig_Compiler $compiler) {
		$compiler
			->addDebugInfo($this)
			->write('$this->env->addGlobal(\'title\', $context[\'title\'] = ob_get_clean());' . "\n");
	}
}

function t($key) {
	return ucfirst(strtolower(str_replace('_', ' ', $key)));
}

$vars = json_decode(file_get_contents('data/br.json'), true);
$version = $_GET['version'] ?? '1';

$twig = $twigMaker(function(Environment $twig) {
	$twig->addGlobal('title', '');
	// $twig->setCache(false);
	$twig->addTokenParser(new TitleTokenParser());
	$twig->addTokenParser(new EndTitleTokenParser());
});
$template = $twig->load("br.page{$version}.twig");
$output = $template->render($vars);

header('Content-type: text/html; charset=utf-8');
echo $output;

echo '<pre>';
print_r($debugTemplate($twig, $template));
echo '</pre>';
