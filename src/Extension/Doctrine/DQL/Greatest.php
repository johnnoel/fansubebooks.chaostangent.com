<?php

namespace ChaosTangent\FansubEbooks\Extension\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Parser,
    Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\SqlWalker;

/**
 * Greatest DQL function
 *
 * GREATEST(x, y [, z ...])
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Greatest extends FunctionNode
{
    /** @var array */
    protected $values = [];

    public function parse(Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->values[] = $parser->ArithmeticPrimary();

        while(Lexer::T_COMMA === $lexer->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->values[] = $parser->ArithmeticPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        $values = [];

        foreach ($this->values as $value) {
            $values[] = $value->dispatch($sqlWalker);
        }

        return 'GREATEST('.implode(', ', $values).')';
    }
}
