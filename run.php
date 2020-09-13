<?php

use ya3sat\Factory\Random3SATFormula;
use ya3sat\Factory\SetPAConditionsFactory;
use ya3sat\Service\Visualise;

include_once __DIR__.'/vendor/autoload.php';

$formula = Random3SATFormula::build(20, 40);

Visualise::booleanRepresentation($formula);
Visualise::orRepresentation($formula);

$set = SetPAConditionsFactory::build($formula);

Visualise::setRepresentation($set);
