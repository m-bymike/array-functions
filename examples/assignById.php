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

require_once dirname(__DIR__) . '/vendor/autoload.php';
$instances = include __DIR__ . '/data/listOfInstances.php';

$foreach = \sclable\arrayFunctions\ArrayWrap::create([]);
$map = \sclable\arrayFunctions\ArrayWrap::create([]);
$wrapMap = \sclable\arrayFunctions\ArrayWrap::create([]);

for ($i = 0; $i < 100; $i++) {
    // foreach
    $start = microtime(true);
    $assigned = [];
    foreach ($instances as $instance) {
        $assigned[$instance->id] = $instance;
    }
    $end = microtime(true);
    $foreach->push($end - $start);

    // map
    $start = microtime(true);
    $assigned2 = array_combine(array_map(function ($instance) {
        return $instance->id;
    }, $instances), $instances);
    $end = microtime(true);
    $map->push($end - $start);

    // ArrayWrap map
    $start = microtime(true);
    $assigned3 = array_combine(\sclable\arrayFunctions\ArrayWrap::create($instances)->map(function ($instance) {
        return $instance->id;
    })->getRaw(), $instances);
    $end = microtime(true);
    $wrapMap->push($end - $start);
}

echo 'foreach: ' . printResult($foreach) . PHP_EOL;
echo 'map: ' . printResult($map) . PHP_EOL;
echo 'wrapMap: ' . printResult($wrapMap) . PHP_EOL;

function printResult(\sclable\arrayFunctions\ArrayWrap $results)
{
    return "min: {$results->min()} / max: {$results->max()} / avg: {$results->avg()} || sum: {$results->sum()}";
}