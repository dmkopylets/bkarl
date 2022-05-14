<?php

namespace App\Infrastructure\Services\Doctrine\SqlCommands;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;


class JsonbAtGreater extends FunctionNode
{
    public $leftHandSide = null;
    public $rightHandSide = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->leftHandSide = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->rightHandSide = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return '(' .
            $this->leftHandSide->dispatch($sqlWalker).'::jsonb' . ' @> ' .
            $this->rightHandSide->dispatch($sqlWalker) .
            ')';
    }
}