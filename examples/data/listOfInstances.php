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


return \sclable\arrayFunctions\ArrayWrap::create(range(1, 10000))->map(function ($id) {
    return (object) [
        'id' => $id,
        'name' => 'name - ' . $id,
    ];
})->getRaw();