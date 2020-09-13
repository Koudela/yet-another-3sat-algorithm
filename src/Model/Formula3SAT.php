<?php declare(strict_types=1);

namespace ya3sat\Model;

class Formula3SAT
{
    /** @var int */
    public $varCnt;
    /** @var Clause3SAT[] */
    public $clauses = [];

    public function __construct(int $varCnt)
    {
        $this->varCnt = $varCnt;
    }
}
