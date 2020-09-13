<?php declare(strict_types=1);

namespace ya3sat\Model;

class SetPAConditions
{
    /** @var int */
    public $varCnt;
    /** @var SetPAConditions[] */
    public $conditions = [];

    public function __construct(int $varCnt)
    {
        $this->varCnt = $varCnt;
    }
}
