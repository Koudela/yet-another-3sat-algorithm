<?php declare(strict_types=1);

namespace ya3sat\Factory;

use ya3sat\Model\Clause3SAT;
use ya3sat\Model\Formula3SAT;

class Random3SATFormula
{
    public static function build(int $varCnt, int $clauseCnt): Formula3SAT
    {
        $formula = new Formula3SAT($varCnt);

        foreach (range(0, $clauseCnt - 1) as $number) {
            $vars = [];

            while (count($vars) < 3) {
                $vars[rand(0, $varCnt - 1)] = rand(0, 1) === 1;
            }

            $formula->clauses[$number] = new Clause3SAT();
            $formula->clauses[$number]->vars = $vars;
        }

        return $formula;
    }
}
