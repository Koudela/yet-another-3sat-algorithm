<?php declare(strict_types=1);

namespace ya3sat\Model;

use ya3sat\service\Visualise;

class ConditionPA
{
    public $failed = false;
    public $sKeys = [];
    public $nKeys = [];
    public $values = [];

    public function getCurrentId(): string
    {
        ksort($this->values);

        $id = [];

        foreach ($this->values as $key => $value) {
            $isSufficientCondition = in_array($key, $this->sKeys);
            $id[] = Visualise::getAlpha($key) . ($isSufficientCondition ? '#' : '=') . $value;
        }

        return implode(' ', $id);
    }
}
