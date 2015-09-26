<?php

namespace ChaosTangent\FansubEbooks\Extension\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Parser,
    Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\SqlWalker;

/**
 * Random DQL function
 *
 * RANDOM()
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Random extends FunctionNode
{
    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return mt_rand();
    }
}
