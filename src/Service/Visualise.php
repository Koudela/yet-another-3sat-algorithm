<?php declare(strict_types=1);

namespace ya3sat\Service;

use ya3sat\Model\Condition2SA;
use ya3sat\Model\ConditionPA;
use ya3sat\Model\Formula3SAT;
use ya3sat\Model\SetPAConditions;

class Visualise
{
    public static function booleanRepresentation(Formula3SAT $formula): void
    {
        foreach ($formula->clauses as $clause) {
            foreach (range(0, $formula->varCnt - 1) as $number) {
                echo isset($clause->vars[$number]) ? ($clause->vars[$number] ? 1 : 0) . ' ' : '  ';
            }
            echo "\n\r";
        }
    }

    public static function orRepresentation(Formula3SAT $formula): void
    {
        foreach ($formula->clauses as $clause)
        {
            $out = [];
            foreach ($clause->vars as $key => $value) {
                $out[] = self::getAlpha($key) . '=' . ($value ? 1 : 0);
            }
            echo "(" . implode('|', $out) . ")\r\n";
        }
    }

    public static function getAlpha(int $key): string
    {
        static $alphabet = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        static $alpha = [];

        if (!isset($alpha[$key])) {
            foreach (range(0, $key * 10) as $number) {
                foreach ($alphabet as $char) {
                    $alpha[] = $number === 0 ? $char : $char.$number;
                }
            }
        }

        return $alpha[$key];
    }

    public static function setRepresentation(SetPAConditions $set)
    {
        self::setRepresentationSub($set, false);
        self::setRepresentationSub($set, true);
    }

    protected static function setRepresentationSub(SetPAConditions $set, bool $printFailed): void
    {
//        usort($this->result, function ($a, $b) {
//            return count($a['values']) <=> count($b['values']);
//        });
        $cnt = 0;

        /** @var ConditionPA $condition */
        foreach ($set->conditions as $nr => $condition) {
            if ($condition->failed !== $printFailed) {
                continue;
            }
            $cnt++;
            echo str_pad((string) $nr, 3, ' ', STR_PAD_LEFT).' ';

            self::conditionPARepresentation($condition, $set->varCnt, $printFailed);
        }

        /** @var ConditionPA $condition */
        foreach ($set->conditions as $nr => $condition) {
            if ($condition->failed !== $printFailed) {
                continue;
            }
            echo str_pad((string) $nr, 3, ' ', STR_PAD_LEFT).' ';
            self::conditionPARepresentation($condition, $set->varCnt, $printFailed);
            if ($nr === 0 && $condition instanceof Condition2SA) {
                foreach ($condition->stack as $key => $term) {
                    echo str_pad((string) $key, 7, '\'', STR_PAD_LEFT).' ';
                    self::conditionPARepresentation($term, $set->varCnt, $printFailed);
                }
            }
            return;
        }
        echo "$cnt lines\r\n";
    }

    public static function conditionPARepresentation(ConditionPA $condition, int $varCnt, bool $printFailed): void
    {
        $out = [];
        foreach (range(0, $varCnt - 1) as $number) {
            if (isset($condition->values[$number])) {
                $isSufficientCondition = in_array($number, $condition->sKeys);
                if (!$printFailed || $isSufficientCondition) {
                    $out[] = self::getAlpha($number) . ($isSufficientCondition ? '#' : '=') . $condition->values[$number];

                    continue;
                }
            }

            $out[] = '   ';
        }

        echo ($condition->failed ? 'boom' : ' ok ') . ' ' . implode(' && ', $out) . "\r\n";
    }
}
