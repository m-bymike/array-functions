<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of the Sclable Business Application Development Platform
 * and is subject to the provisions of your License Agreement with
 * Sclable Business Solutions GmbH.
 *
 * @copyright (c) 2016 Sclable Business Solutions GmbH
 * ----------------------------------------------------------------------------
 */

use \sclable\arrayFunctions\ArrayWrap;

require_once dirname(__DIR__) . '/vendor/autoload.php';
$numbers = ArrayWrap::range(-10000, -2)->shuffle()->getRaw();



// old
$numbers = array_pad([], 10, 0);
foreach ($numbers as &$number) {
    $number = rand();
}

$max = null;
foreach ($numbers as $number) {
    $max = $max === null ? $number : max($max, $number);
}

echo $max . PHP_EOL;

// fp
echo ArrayWrap::create([])
    ->pad(10, 0)
    ->map(function () { return rand(); })
    ->reduce('max');