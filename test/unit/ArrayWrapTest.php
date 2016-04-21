<?php
/**
 * This file is part of the Sclable Array Functions Package.
 * For the full license information, please read the LICENSE
 * file distributed with this source code.
 */

namespace sclable\arrayFunctions\test\unit;

use sclable\arrayFunctions\ArrayWrap;

/**
 * Class ArrayWrapTest
 *
 *
 * @package sclable\arrayFunctions\test\unit
 * @author Michael Rutz <michael.rutz@sclable.com>
 */
class ArrayWrapTest extends \PHPUnit_Framework_TestCase
{
    public function testMergeWrapperWithAnArray()
    {
        $subject = ArrayWrap::create(['foo' => 'bar']);
        $test = $subject->merge(['another' => 'value']);
        $this->assertInstanceOf(ArrayWrap::class, $test);
        $this->assertEquals([
            'foo' => 'bar',
            'another' => 'value',
        ], $test->getRaw());
    }

    public function testMergeWrapperWithAWrap()
    {
        $subject = ArrayWrap::create(['foo' => 'bar']);
        $test = $subject->merge(ArrayWrap::create(['another' => 'value']));
        $this->assertInstanceOf(ArrayWrap::class, $test);
        $this->assertEquals([
            'foo' => 'bar',
            'another' => 'value',
        ], $test->getRaw());
    }

    public function testImmutableMerge()
    {
        $subject = ArrayWrap::create(['foo' => 'bar']); // Immutable is default
        $test = $subject->merge(['another' => 'value']);
        $this->assertNotEquals($test, $subject);
    }

    public function testRecursiveMerge()
    {
        $this->assertEquals(
            ['a' => ['b' => 1, 'c' => 2], 'd' => 3],
            ArrayWrap::create(['a' => ['c' => 2]])->mergeRecursive(['a' => ['b' => 1]], ['d' => 3])->getRaw()
        );
    }

    public function testArrayMapWithCallback()
    {
        $subject = ArrayWrap::create([1, 2, 3]);
        $test = $subject->map(function ($val) {
            return $val * 2;
        });
        $this->assertInstanceOf(ArrayWrap::class, $test);
        $this->assertEquals([2, 4, 6], $test->getRaw());
    }

    public function testArrayMapImmutability()
    {
        $subject = new ArrayWrap([1, 2, 3]);
        $subject->map(function ($val) {
            return $val * 2;
        });
        $this->assertNotEquals([2, 4, 6], $subject->getRaw());
    }

    public function testArrayMapWithBuiltInFunction()
    {
        $this->assertEquals(
            ['MUST', 'BE', 'UPPERCASE'],
            ArrayWrap::create(['must', 'be', 'uppercase'])->map('strtoupper')->getRaw()
        );
    }

    public function testArrayApplyWithCallback()
    {
        $this->assertEquals([2, 3], ArrayWrap::create([4, 9])->apply(function (&$val) {
            $val = sqrt($val);
        })->getRaw());
    }

    public function testArrayApplyRecursive()
    {
        $this->assertEquals(
            ['a', ['b', ['c']]],
            ArrayWrap::create(['A', ['B', ['C']]])->applyRecursive(function (&$item) {
                $item = strtolower($item);
            })->getRaw()
        );
    }

    public function testArrayReduceRight()
    {
        $this->assertEquals('test', ArrayWrap::create(str_split('tset'))->reduceRight(function ($agg, $char) {
            return $agg . $char;
        }, ''));
    }

    // filtering
    public function testArrayFilterWithCallback()
    {
        $number3 = (object)['id' => 3];
        $subject = ArrayWrap::create([
            (object)['id' => 1],
            (object)['id' => 2],
            $number3,
        ]);

        $this->assertEquals(
            [2 => $number3],
            $subject->filter(function ($item) {
                return $item->id > 2;
            })->getRaw()
        );
    }

    public function testArrayFilterWithBuiltInFunction()
    {
        $number2 = (object)[];
        $subject = ArrayWrap::create([[], $number2, 'string']);
        $this->assertEquals([1 => $number2], $subject->filter('is_object')->getRaw());
    }

    public function testArrayFilterWithNullRemovesEmptyElements()
    {
        $subject = ArrayWrap::create([0, 1, 2, '', null, [], 'but_me']);
        $this->assertEquals([1 => 1, 2 => 2, 6 => 'but_me'], $subject->filter()->getRaw());
    }

    // find elements
    public function testArrayFindReturnsElement()
    {
        $number2 = (object)['id' => 2];
        $subject = ArrayWrap::create([
            (object)['id' => 1],
            $number2,
            (object)['id' => 3],
        ]);

        $this->assertEquals(
            $number2,
            $subject->find(function ($item) {
                return $item->id == 2;
            })
        );
    }

    public function testArrayFindReturnsNullIfNotFound()
    {
        $subject = ArrayWrap::create([42]);
        $this->assertNull(
            $subject->find(function ($item) {
                return $item !== 42;
            })
        );
    }

    public function testArrayFindIndex()
    {
        $subject = ArrayWrap::create([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $this->assertEquals(
            1,
            $subject->findIndex(function ($item) {
                return $item->id == 2;
            })
        );
    }

    public function testArrayFindIndexPassesAllParams()
    {
        $subject = ArrayWrap::create([42]);
        $actualArgs = false;
        $subject->findIndex(function () use (&$actualArgs) {
            $actualArgs = func_get_args();
            return true;
        });

        $this->assertEquals([42, 0, $subject], $actualArgs);
    }

    public function testArrayFindIndexReturnsFalse()
    {
        $subject = ArrayWrap::create([
            (object)['id' => 1],
            (object)['id' => 2],
            (object)['id' => 3],
        ]);

        $this->assertFalse($subject->findIndex(function ($item) {
            return $item->id == 4;
        }));
    }

    public function testArrayFindIndexWorksWithEdgeCase()
    {
        $this->assertEquals(2, ArrayWrap::create([0, '', false])->findIndex(function ($item) {
            return $item === false;
        }));
    }

    public function testIndexOfMethodWithObject()
    {
        $needle = (object)['id' => 2];
        $subject = ArrayWrap::create([
            (object)['id' => 1],
            $needle,
            (object)['id' => 3],
        ]);

        $this->assertEquals(1, $subject->indexOf($needle));
    }

    public function testIndexOfReturnsFalse()
    {
        $subject = ArrayWrap::create([1, 2, 3]);
        $this->assertFalse($subject->indexOf(4));
    }

    public function testIndexOfComparesStrict()
    {
        $subject = ArrayWrap::create([0, false, '']);
        $this->assertEquals(1, $subject->indexOf(false));
    }

    public function testLastIndexOfWithObjects()
    {
        $needle = (object) ['id' => 2];
        $subject = ArrayWrap::create([
            $needle,
            $needle,
            (object)['id' => 3],
        ]);

        $this->assertEquals(1, $subject->lastIndexOf($needle));
    }

    // boolean
    public function testEveryConditionReturnsTrue()
    {
        $this->assertTrue(ArrayWrap::create([0, 1])->every(function ($item) {
            return $item < 2;
        }));
    }

    public function testEveryConditionReturnsTrueOnEdgeCase()
    {
        $this->assertTrue(ArrayWrap::create([0, 1, false])->every(function ($item) {
            return $item < 2 || $item === false;
        }));
    }

    public function testEveryConditionReturnsFalse()
    {
        // todo rename to every
        $subject = ArrayWrap::create([0, 1, 2, 3]);
        $this->assertFalse($subject->every(function ($item) {
            return $item < 2;
        }));
        $this->assertEquals(2, key($subject->getRaw()), 'all should fail fast.');
    }

    public function testEveryPassesAllParams()
    {
        $subject = ArrayWrap::create([42]);
        $actualArgs = false;
        $subject->findIndex(function () use (&$actualArgs) {
            $actualArgs = func_get_args();
            return true;
        });

        $this->assertEquals([42, 0, $subject], $actualArgs);
    }

    public function testSomePassesAllParams()
    {
        $subject = ArrayWrap::create([42]);
        $actualArgs = false;
        $subject->findIndex(function () use (&$actualArgs) {
            $actualArgs = func_get_args();
            return true;
        });

        $this->assertEquals([42, 0, $subject], $actualArgs);
    }

    public function testSomeConditionReturnsTrue()
    {
        $subject = ArrayWrap::create([3, 2, 1]);
        $this->assertTrue($subject->some(function ($item) {
            return $item <= 2;
        }));
        $this->assertEquals(1, key($subject->getRaw()), 'some should success fast.');
    }

    public function testSomeConditionReturnsTrueOnEdgeCase()
    {
        $this->assertTrue(ArrayWrap::create([false, 1, 10])->some(function ($item) {
            return $item === false;
        }));
    }

    public function testSomeConditionReturnsFalse()
    {
        $subject = ArrayWrap::create([3, 2, 1]);
        $this->assertFalse($subject->some(function ($item) {
            return $item > 4;
        }));
    }

    // math
    public function testSum()
    {
        $this->assertEquals(6, ArrayWrap::create([1, 2, 3])->sum());
    }

    public function testAvg()
    {
        $this->assertEquals(2, ArrayWrap::create([1, 2, 3])->avg());
    }

    // stack
    public function testShiftElements()
    {
        $subject = ArrayWrap::create([1, 2, 3]);
        $shifted = $subject->shift();
        $this->assertEquals(1, $shifted);
        $this->assertEquals([2, 3], $subject->getRaw());
    }

    public function testPushElement()
    {
        $subject = ArrayWrap::create([1]);
        $subject->push(2);
        $this->assertAttributeEquals([1, 2], 'data', $subject);
    }

    public function testUnshiftElement()
    {
        $subject = ArrayWrap::create([1]);
        $subject->unshift(2);
        $this->assertAttributeEquals([2, 1], 'data', $subject);
    }

    // ordering
    public function testReverse()
    {
        $this->assertEquals([3, 2, 1], ArrayWrap::create([1, 2, 3])->reverse()->getRaw());
    }

    public function testReverseWithKeyPreservation()
    {
        $this->assertEquals(
            [2 => 2, 1 => 1, 0 => 0],
            ArrayWrap::create([0 => 0, 1 => 1, 2 => 2])->reverse(true)->getRaw()
        );
    }

    // Exceptions
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException()
    {
        /** @noinspection PhpParamsInspection */
        new ArrayWrap(1);
    }


}
