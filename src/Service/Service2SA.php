<?php declare(strict_types=1);

namespace ya3sat\Service;

use ya3sat\Model\Condition2SA;

class Service2SA
{
    public static function isApplicable2SATerm(Condition2SA $condition, Condition2SA $term): bool
    {
        return (!isset($condition->values[$term->sKeys[0]]) || $condition->values[$term->sKeys[0]] === $term->values[$term->sKeys[0]])
            && (!isset($condition->values[$term->sKeys[1]]) || $condition->values[$term->sKeys[1]] === $term->values[$term->sKeys[1]])
            && (!isset($condition->values[$term->nKeys[0]]) || $condition->values[$term->nKeys[0]] !== $term->values[$term->nKeys[0]]);
    }

    public static function isChainable2SATerm(Condition2SA $condition, Condition2SA $term): bool
    {
        return (isset($condition->values[$term->sKeys[0]]) && $condition->values[$term->sKeys[0]] === $term->values[$term->sKeys[0]])
            && (isset($condition->values[$term->sKeys[1]]) && $condition->values[$term->sKeys[1]] === $term->values[$term->sKeys[1]]);
    }

    public static function isChainableInverse2SATerm(Condition2SA $condition, Condition2SA $term): ?int
    {
        if (!isset($condition->values[$term->nKeys[0]]) || $condition->values[$term->nKeys[0]] === $term->values[$term->nKeys[0]]) {
            return null;
        }

        if (isset($condition->values[$term->sKeys[0]]) && $condition->values[$term->sKeys[0]] === $term->values[$term->sKeys[0]]) {
            return $term->sKeys[1];
        }

        if (isset($condition->values[$term->sKeys[1]]) && $condition->values[$term->sKeys[1]] === $term->values[$term->sKeys[1]]) {
            return $term->nKeys[0];
        }

        return null;
    }

    public static function isInvertible2SATerm(Condition2SA $condition, Condition2SA $term): bool
    {
        return (isset($condition->values[$term->nKeys[0]]) && $condition->values[$term->nKeys[0]] !== $term->values[$term->nKeys[0]]);
    }
}
