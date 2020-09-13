<?php declare(strict_types=1);

namespace ya3sat\Service;

use ya3sat\Model\Condition1SA;
use ya3sat\Model\Condition2SA;

class Service1SA
{
    public static function isApplicable1SATerm(Condition2SA $condition, Condition1SA $term): bool
    {
        return (!isset($condition->values[$term->sKeys[0]]) || $condition->values[$term->sKeys[0]] === $term->values[$term->sKeys[0]])
            && (!isset($condition->values[$term->nKeys[0]]) || $condition->values[$term->nKeys[0]] !== $term->values[$term->nKeys[0]]);
    }

    public static function isChainable1SATerm(Condition2SA $condition, Condition1SA $term): bool
    {
        return (isset($condition->values[$term->sKeys[0]]) && $condition->values[$term->sKeys[0]] === $term->values[$term->sKeys[0]]);
    }


    public static function isChainableInverse1SATerm(Condition2SA $condition, Condition1SA $term): bool
    {
        return self::isInvertible2SATerm($condition, $term);
    }

    public static function isInvertible2SATerm(Condition2SA $condition, Condition1SA $term): bool
    {
        return (isset($condition->values[$term->nKeys[0]]) && $condition->values[$term->nKeys[0]] !== $term->values[$term->nKeys[0]]);
    }
}
