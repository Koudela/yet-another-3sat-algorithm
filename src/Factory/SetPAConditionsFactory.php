<?php declare(strict_types=1);

namespace ya3sat\Factory;

use ya3sat\Model\Condition2SA;
use ya3sat\Model\Formula3SAT;
use ya3sat\Model\SetPAConditions;
use ya3sat\Service\Service2SA;

class SetPAConditionsFactory
{
    public static function build(Formula3SAT $formula): SetPAConditions
    {
        $set = new SetPAConditions($formula->varCnt);

        foreach ($formula->clauses as $clause) {
            for ($i = 0; $i < 3; $i++) {
                $set->conditions[] = new Condition2SA($clause, $i);
            }
        }

        /** @var Condition2SA $condition */
        foreach ($set->conditions as $key => $condition) {
            /** @var Condition2SA $term */
            foreach ($set->conditions as $term) {
                if (Service2SA::isApplicable2SATerm($condition, $term)) {
                    $condition->stack[] = clone $term;
                }
            }
        }

        return $set;
    }
}
