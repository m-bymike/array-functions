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


$foreach = ArrayWrap::create([]);
$map = ArrayWrap::create([]);
$wrapSum = ArrayWrap::create([]);

for ($i = 0; $i < 100; $i++) {
    // foreach
    $start = microtime(true);
    $max = null;
    foreach ($numbers as $number) {
        if ($max === null) {
            $max = $number;
            continue;
        }

        $max = max($max, $number);
    }
    $end = microtime(true);
    $foreach->push($end - $start);

    // map
    $start = microtime(true);
    $max = array_reduce($numbers, 'max', null);
    $end = microtime(true);
    $map->push($end - $start);

    // ArrayWrap map
    $start = microtime(true);
    $max = ArrayWrap::create($numbers)->max();
    $end = microtime(true);
    $wrapSum->push($end - $start);
}

echo 'foreach: ' . printResult($foreach) . PHP_EOL;
echo 'map: ' . printResult($map) . PHP_EOL;
echo 'wrapMap: ' . printResult($wrapSum) . PHP_EOL;

function printResult(ArrayWrap $results)
{
    return "min: {$results->min()} / max: {$results->max()} / avg: {$results->avg()} || sum: {$results->sum()}";
}