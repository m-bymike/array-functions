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
$textList = array_map(function () {
    return 'abcdefgh';
}, range(0, 10000));

$foreach = \sclable\arrayFunctions\ArrayWrap::create([]);
$map = \sclable\arrayFunctions\ArrayWrap::create([]);
$wrapMap = \sclable\arrayFunctions\ArrayWrap::create([]);

for ($i = 0; $i < 100; $i++) {
    // foreach
    $fTextList = $textList;
    $start = microtime(true);
    foreach ($fTextList as &$text) {
        $text = htmlentities($text);
    }
    $end = microtime(true);
    $foreach->push($end - $start);

    // map
    $wTextList = $textList;
    $start = microtime(true);
    $wTextList = array_map('htmlentities', $wTextList);
    $end = microtime(true);
    $map->push($end - $start);

    // ArrayWrap map
    $mTextList = $textList;
    $start = microtime(true);
    $mTextList = \sclable\arrayFunctions\ArrayWrap::create($mTextList)->apply('htmlentities');
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