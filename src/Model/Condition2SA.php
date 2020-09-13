<?php declare(strict_types=1);

namespace ya3sat\Model;

use ya3sat\Service\Service1SA;
use ya3sat\Service\Service2SA;

class Condition2SA extends ConditionPA
{
    /** @var ConditionPA[] */
    public $stack;
    /** @var ConditionPA[] */
    public $conditions;
    /** @var array */
    public $reverences;

    public function __construct(Clause3SAT $clause, int $nr)
    {
        $cnt = 0;

        foreach ($clause->vars as $key => $value) {
            if ($cnt !== $nr) {
                $this->sKeys[] = $key;
                $this->values[$key] = $value ? 0 : 1;
            } else {
                $this->nKeys[] = $key;
                $this->values[$key] = $value ? 1 : 0;
            }

            $cnt++;
        }
    }

    public function processStack()
    {
        while (count($this->stack) > 0) {
            $this->processNext();
        }
    }

    protected function processNext()
    {
        $colKey = $this->processNextStackItem();
        if (is_null($colKey) || isset($this->values[$colKey])) {
            return;
        }
        $colKeys = [$colKey => $colKey];

        while (true) {
            $colKey = array_pop($colKeys);
            if (is_null($colKey)) {
                return;
            }
            if (isset($this->reverences[$colKey])) {
                /** @var ConditionPA $condition */
                foreach ($this->reverences[$colKey] as $condition) {
                    [$colKey, $remove] = $this->processCondition($condition);
                    if ($remove) {
                        unset($this->conditions[spl_object_id($condition)]);
                        unset($this->reverences[$condition->sKeys[0]][spl_object_id($condition)]);
                        if ($condition instanceof Condition2SA) {
                            unset($this->reverences[$condition->sKeys[1]][spl_object_id($condition)]);
                        }
                    }

                    if (!is_null($colKey) && !isset($this->values[$colKey])) {
                        $colKeys[$colKey] = $colKey;
                    }
                }
            }
        }
    }

    protected function processNextStackItem(): ?int
    {
        if ($condition = array_pop($this->stack)) {
            [$colKey, $remove] = $this->processCondition($condition);

            if (!$remove) {
                $this->addToConditions($condition);
            }
        }

        return $colKey ?? null;
    }

    protected function processCondition(ConditionPA $condition): array
    {
        if ($condition instanceof Condition2SA) {
            if (!Service2SA::isApplicable2SATerm($this, $condition)) {
                return [null, true];
            }
            if (Service2SA::isChainable2SATerm($this, $condition)) {
                return [$this->chainPACondition($condition), true];
            }
            $colKey = Service2SA::isChainableInverse2SATerm($this, $condition);
            if (!is_null($colKey)) {
                return [$this->chainInversePACondition($condition, $colKey), true];
            }

            return [null, false];
        }

        if ($condition instanceof Condition1SA) {
            if (!Service1SA::isApplicable1SATerm($this, $condition)) {
                return [null, true];
            }
            if (Service1SA::isChainable1SATerm($this, $condition)) {
                return [$this->chainPACondition($condition), true];
            }
            if (Service1SA::isChainableInverse1SATerm($this, $condition)) {
                return [$this->chainInversePACondition($condition, $condition->sKeys[0]), true];
            }

            return [null, false];
        }

        return [null, false];
    }

    protected function addToConditions(ConditionPA $condition)
    {
        $this->conditions[spl_object_id($condition)] = $condition;

        if (!$this->reverences[$condition->sKeys[0]]) {
            $this->reverences[$condition->sKeys[0]] = [];
        }
        $this->reverences[$condition->sKeys[0]][spl_object_id($condition)] = $condition;

        if ($condition instanceof Condition2SA) {
            if (!$this->reverences[$condition->sKeys[1]]) {
                $this->reverences[$condition->sKeys[1]] = [];
            }
            $this->reverences[$condition->sKeys[1]][spl_object_id($condition)] = $condition;
        }
    }

    protected function chainPACondition(ConditionPA $condition): ?int
    {
        if (!isset($this->values[$condition->nKeys[0]])) {
            $this->values[$condition->nKeys[0]] = $condition->values[$condition->nKeys[0]];
            $this->nKeys[] = $condition->nKeys[0];

            return $condition->nKeys[0];
        } else if ($this->values[$condition->nKeys[0]] !== $condition->values[$condition->nKeys[0]]) {
            $this->failed = true;
            $this->stack = [];
            $this->conditions = [];
            $this->reverences = [];

            $this->set->addBoomTermsToAllSubConditions($condition);
        }

        return null;
    }

    protected function chainInversePACondition(ConditionPA $condition, int $colKey): ?int
    {
        if (!isset($this->values[$colKey])) {
            $this->values[$colKey] = $condition->values[$colKey] ? 0 : 1;
            $this->nKeys[] = $colKey;

            return $colKey;
        } else if ($this->values[$colKey] === $condition->values[$colKey]) {
            $this->failed = true;
            $this->stack = [];
            $this->conditions = [];
            $this->reverences = [];

            $this->set->addBoomTermsToAllSubConditions($condition);
        }

        return null;
    }
}
