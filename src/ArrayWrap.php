<?php
/**
 * This file is part of the Sclable Array Functions Package.
 * For the full license information, please read the LICENSE
 * file distributed with this source code.
 */

namespace sclable\arrayFunctions;

/**
 * Class ArrayWrap
 *
 * A normalizing, functional programming and OOP approach to the PHP array functions.
 *
 * **Usage**
 * ```php
 * echo ArrayWrap::range(0, 10)
 *     ->filter(function ($nr) { return $nr < 3; })
 *     ->map(function ($nr) { return "Number: $nr\n"; })
 *     ->reverse();
 * // echoes
 * // Number: 2
 * // Number: 1
 * // Number: 0
 * ```
 *
 * @todo implement Iterator, Countable interfaces
 *
 * @package sclable\arrayFunctions
 * @author Michael Rutz <michael.rutz@sclable.com>
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 */
class ArrayWrap extends AccessibleArray
{
    /**
     * Factory method
     *
     * Usage:
     * ```php
     * $arr = ArrayWrap::create([1, 2, 3]);
     * ```
     *
     * @param array $data
     * @return static
     */
    public static function create($data)
    {
        return new static($data);
    }

    /**
     * Factory method to create a range into ArrayWrap.
     * Shorthand for:
     * `ArrayWrap::create(range(0, 10))`
     * @param $start
     * @param $end
     * @return ArrayWrap
     */
    public static function range($start, $end)
    {
        return static::create(range($start, $end));
    }



    /**
     * return the raw data array
     * @return array
     */
    public function getRaw()
    {
        return $this->data;
    }

    /**
     * reply to a function call with a new instance of the wrapper for immutability
     * @param array $data
     * @internal
     * @return static
     */
    protected function reply(array $data)
    {
        return static::create($data);
    }

    /**
     * Pad the array to the specified length with a value
     * @see array_pad()
     * @param int $padSize
     * @param mixed $padValue
     * @return ArrayWrap
     */
    public function pad($padSize, $padValue)
    {
        return $this->reply(array_pad($this->data, $padSize, $padValue));
    }

    /**
     * Merge this array with one or more arrays.
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([1, 2, 3])->merge([4, 5, 6], [7, 8]);
     * // $res == [1, 2, 3, 4, 5, 6, 7, 8]
     * ```
     *
     * @see array_merge()
     * @link http://php.net/manual/en/function.array-merge.php
     * @param array|ArrayWrap $array1,...
     * @return ArrayWrap
     */
    // @codingStandardsIgnoreLine
    public function merge($array1)
    {
        $args = $this->makeRaw(func_get_args());
        array_unshift($args, $this->data);
        return $this->reply(call_user_func_array('array_merge', $args));
    }

    /**
     * Merge this array with one or more arrays recursively
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([0 => 0, 1 => [2, 3]])->merge([1 => [4]], [5]);
     * // $res == [0 => 0, 1 => [2, 3, 4], 5]
     * ```
     * @param array|ArrayWrap $array1,...
     * @return ArrayWrap
     */
    // @codingStandardsIgnoreLine
    public function mergeRecursive($array1)
    {
        $args = $this->makeRaw(func_get_args());
        array_unshift($args, $this->data);
        return $this->reply(call_user_func_array('array_merge_recursive', $args));
    }

    /**
     * Execute a callback with each element of the array
     * and return the callback results as a new array
     *
     * Usage:
     * ```php
     * $original = ArrayWrap::create([1, 2]);
     * $res = $original->map(function ($nr) { return $nr * 2; });
     * // $res == [2, 4]; $original == [1, 2]
     * // make sure to return a value
     * ```
     * @see array_map()
     * @param callable $callback
     * @return ArrayWrap
     */
    public function map(callable $callback)
    {
        return $this->reply(array_map($callback, $this->data));
    }

    /**
     * Reduce the array to a single value.
     *
     * Usage:
     * ```php
     * $concat = ArrayWrap::create(['a', 'b', 'c'])->reduce(function ($agg, $item) {
     *     return $agg . $item;
     * }, '');
     *
     * // $concat == 'abc';
     * ```
     *
     * @see array_reduce()
     * @param callable $callback the callback to reduce the array. The callback gets two arguments,
     * $aggregate and $currentItem, the return value will be the $aggregate of the next call. Within the first
     * call, $aggregate is null or the value of $seed.
     * @param mixed $seed the initial aggregate value
     * @return mixed the reduced / aggregated value
     */
    public function reduce(callable $callback, $seed = null)
    {
        return array_reduce($this->data, $callback, $seed);
    }

    /**
     * Reduce the array to a single value in reverse order.
     *
     * Usage:
     * ```php
     * $concat = ArrayWrap::create(['a', 'b', 'c'])->reduceRight(function ($agg, $item) {
     *     return $agg . $item;
     * }, '');
     *
     * // $concat == 'cba';
     * ```
     *
     * @see array_reduce()
     * @see array_reverse()
     * @param callable $callback the callback to reduce the array. The callback gets two arguments,
     * $aggregate and $currentItem, the return value will be the $aggregate of the next call. Within the first
     * call, $aggregate is null or the value of $seed.
     * @param mixed $seed the initial aggregate value
     * @return mixed the reduced / aggregated value
     */
    public function reduceRight(callable $callback, $seed = null)
    {
        return array_reduce(array_reverse($this->data), $callback, $seed);
    }

    /**
     * Apply a callback to each element of the array
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([1, 2]);
     * $res->apply(function (&$nr) { $nr * 2; });
     * // $res == [2, 4];
     * // make sure to reference the variable --> &$nr
     * // otherwise changes will not be effective
     * ```
     * @see array_walk()
     * @param callable $callback
     * @return ArrayWrap
     */
    public function apply(callable $callback)
    {
        return $this->walk($callback);
    }

    /**
     * Apply a callback to each element of the array
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([1, 2]);
     * $res->apply(function (&$nr) { $nr * 2; });
     * // $res == [2, 4];
     * // make sure to reference the variable --> &$nr
     * // otherwise changes will not be effective
     * ```
     * @see array_walk()
     * @param callable $callback
     * @return ArrayWrap
     */
    public function walk(callable $callback)
    {
        array_walk($this->data, $callback);
        return $this;
    }

    /**
     * Apply a callback recursive to each element and child element of the array
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([1, 2, [3]]);
     * $res->apply(function (&$nr) { $nr * 2; });
     * // $res == [2, 4, [6]];
     * // make sure to reference the variable --> &$nr
     * // otherwise changes will not be effective
     * ```
     * @see array_walk()
     * @param callable $callback
     * @return ArrayWrap
     */
    public function applyRecursive(callable $callback)
    {
        return $this->walkRecursive($callback);
    }

    /**
     * Apply a callback recursive to each element and child element of the array
     *
     * Usage:
     * ```php
     * $res = ArrayWrap::create([1, 2, [3]]);
     * $res->apply(function (&$nr) { $nr * 2; });
     * // $res == [2, 4, [6]];
     * // make sure to reference the variable --> &$nr
     * // otherwise changes will not be effective
     * ```
     * @see array_walk()
     * @param callable $callback
     * @return ArrayWrap
     */
    public function walkRecursive(callable $callback)
    {
        array_walk_recursive($this->data, $callback);
        return $this;
    }

    /**
     * Shuffle the array. Unlike the original, this method is immutable and returns a new array.
     *
     * Usage:
     * ```php
     * $original = ArrayWrap::create([1, 2, 3]);
     * $shuffled = $original->shuffle();
     * // $shuffled == [2, 3, 1]; $original == [1, 2, 3];
     * ```
     *
     * @see shuffle()
     * @return ArrayWrap
     */
    public function shuffle()
    {
        $data = $this->data;
        shuffle($data);
        return $this->reply($data);
    }

    /**
     * Remove duplicated items from an array. Unlike the original the default sort flag is SORT_REGULAR,
     * to keep the original variable type of the array item.
     *
     * @see array_unique()
     * @param int $sortFlag Default is SORT_REGULAR, any
     * @return ArrayWrap
     */
    public function unique($sortFlag = SORT_REGULAR)
    {
        return $this->reply(array_unique($this->data, $sortFlag));
    }

    /**
     * Return an array with elements in reverse order.
     * @see array_reverse()
     * @param bool $preserveKeys whether to preserve the array keys. Default's false.
     * @return ArrayWrap the reversed array
     */
    public function reverse($preserveKeys = false)
    {
        return $this->reply(array_reverse($this->data, $preserveKeys === true));
    }

    /**
     * Iterates over each value in the array passing them to the callback function.
     * If the callback function returns true, the current value from array is returned into the result array.
     * Array keys are preserved.
     *
     * Usage:
     * ```php
     * $data = ArrayWrap::range(0, 10)->filter(function ($item) { return $item < 2; });
     * // $data == [0, 1];
     * ```
     *
     * @todo implement & normalize flags parameter
     * @see array_filter()
     * @param callable|null $callback
     * @return ArrayWrap
     */
    public function filter(callable $callback = null)
    {
        if ($callback === null) {
            // even though the documentation states to accept null as callback parameter
            // the function throws an error
            return $this->reply(array_filter($this->data));
        }

        return $this->reply(array_filter($this->data, $callback));
    }

    /**
     * Find an element in the array with a callback function.
     *
     * ```php
     * $list = ArrayWrap::create([
     *     (object) [ 'id' => 1, 'name' => 'Peter' ],
     *     (object) [ 'id' => 2, 'name' => 'Paul' ],
     *     (object) [ 'id' => 3, 'name' => 'Mary' ],
     * ]);
     *
     * $selectedNameId = 2;
     *
     * $nameObject = $list->find(function ($item) use ($selectedNameId) {
     *     return $item->id === $selectedNameId;
     * });
     * ```
     *
     * @param callable $callback
     * @return mixed|null returns the array element or null, if element has not been found.
     */
    public function find(callable $callback)
    {
        $idx = $this->findIndex($callback);
        return $idx !== false ? $this->data[$idx] : null;
    }

    /**
     * Find an element in the array with a callback function.
     *
     * ```php
     * $list = ArrayWrap::create([
     *     (object) [ 'id' => 1, 'name' => 'Peter' ],
     *     (object) [ 'id' => 2, 'name' => 'Paul' ],
     *     (object) [ 'id' => 3, 'name' => 'Mary' ],
     * ]);
     *
     * $selectedNameId = 2;
     *
     * $idx = $list->find(function ($item) use ($selectedNameId) {
     *     return $item->id === $selectedNameId;
     * });
     *
     * echo $list[$idx]->name; // echoes 'Paul'
     * ```
     *
     * @param callable $callback
     * @return string|int|false returns the key of the array element or false, if element has not been found.
     */
    public function findIndex(callable $callback)
    {
        $data = &$this->data;
        reset($data);
        while (key($data) !== null && call_user_func($callback, current($data), key($data), $this) === false) {
            next($data);
        }

        return key($data) !== null ? key($data) : false;
    }

    /**
     * Find the first position of an element in the array.
     *
     * Usage:
     * ```php
     * $needle = (object) [ 'id' => 2, 'name' => 'Paul' ];
     * $list = ArrayWrap::create([
     *     (object) [ 'id' => 1, 'name' => 'Peter' ],
     *     $needle,
     *     (object) [ 'id' => 3, 'name' => 'Mary' ],
     * ]);
     *
     * $idx = $list->indexOf($needle);
     * ```
     *
     * @see array_search()
     * @param mixed $needle
     * @return string|int|false returns the key or false, if not found
     */
    public function indexOf($needle)
    {
        return $this->search($needle, true);
    }

    /**
     * Find the last position of an element in the array.
     *
     * ```php
     * $needle = (object) [ 'id' => 2, 'name' => 'Paul' ];
     * $list = ArrayWrap::create([
     *     $needle,
     *     $needle,
     *     (object) [ 'id' => 3, 'name' => 'Mary' ],
     * ]);
     *
     * $idx = $list->lastIndexOf($needle); // $idx == 1
     * ```
     *
     * @see array_search()
     * @see array_reverse()
     * @param mixed $needle
     * @return string|int|false returns the key or false, if not found
     */
    public function lastIndexOf($needle)
    {
        return array_search($needle, array_reverse($this->data, true), true);
    }

    /**
     * Find the first position of an element in the array.
     *
     * Usage:
     * ```php
     * $needle = (object) [ 'id' => 2, 'name' => 'Paul' ];
     * $list = ArrayWrap::create([
     *     (object) [ 'id' => 1, 'name' => 'Peter' ],
     *     $needle,
     *     (object) [ 'id' => 3, 'name' => 'Mary' ],
     * ]);
     *
     * $idx = $list->indexOf($needle);
     * ```
     *
     * @see array_search()
     * @param mixed $needle
     * @param bool $strict whether to use strict comparison,
     *     default's false (like the original function). If you prefer strict mode,
     *     use the {@see ArrayWrap::indexOf()}.
     * @return string|int|false the key of the first position of $needle or false, if not found
     */
    public function search($needle, $strict = false)
    {
        return array_search($needle, $this->data, $strict);
    }

    /**
     * Whether the array contains $item, comparison is made in strict mode (`===`)
     * @see in_array()
     * @see ArrayWrap::inArray()
     * @param mixed $item
     * @return bool returns true if $item is part of the array
     */
    public function includes($item)
    {
        return $this->inArray($item);
    }

    /**
     * Whether the array contains a certain value or not, comparison is made in strict mode (`===`)
     * @see in_array()
     * @param mixed $value the value to search for
     * @return bool returns true if $value is part of the array
     */
    public function inArray($value)
    {
        return in_array($value, $this->data, true);
    }

    /**
     * Whether the array has a key or not. Unlike isset($array[$key]), this method returns true when
     * the value at the key position is NULL.
     * @see array_key_exists()
     * @param string|int $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->data) === true;
    }

    /**
     * Whether the array has a key or not. Unlike isset($array[$key]), this method returns true when
     * the value at the key position is NULL.
     * @see array_key_exists()
     * @param string|int $key
     * @return bool
     */
    public function keyExists($key)
    {
        return $this->hasKey($key);
    }

    /**
     * Check whether every element in the array matches the condition in the callback function. This method has fast
     * fail to avoid unnecessary iterations.
     *
     * Usage:
     * ```
     * $list = ArrayWrap::create([1, 2, 3]);
     * var_dump($list->every(function ($item) { return $item < 4; })); // is true
     * var_dump($list->every(function ($item) { return $item < 2; })); // is false
     * ```
     *
     * @param callable $callback the callback to test the elements.
     * The callback must return true or false and is evaluated to true in strict mode (=== true). Three arguments
     * will be passed to the callback: $currentElement, $currentKey, The array as ArrayWrap ($this).
     *
     * @return bool
     */
    public function every(callable $callback)
    {
        $data = &$this->data;
        reset($data);
        while (key($data) !== null && call_user_func($callback, current($data), key($data), $this) === true) {
            next($data);
        }

        return key($data) === null;
    }

    /**
     * Check whether some element in the array matches the condition in the callback function. This method has fast
     * success to avoid unnecessary iterations.
     *
     * Usage:
     * ```
     * $list = ArrayWrap::create([1, 2, 3]);
     * var_dump($list->every(function ($item) { return $item > 2; })); // is true
     * var_dump($list->every(function ($item) { return $item < 0; })); // is false
     * ```
     *
     * @param callable $callback the callback to test the elements.
     * The callback must return true or false and is evaluated to true in strict mode (=== true). Three arguments
     * will be passed to the callback: $currentElement, $currentKey, The array as ArrayWrap ($this).
     *
     * @return bool
     */
    public function some(callable $callback)
    {
        $data = &$this->data;
        reset($data);
        while (key($data) !== null && call_user_func($callback, current($data), key($data), $this) === false) {
            next($data);
        }

        return key($data) !== null;
    }

    /**
     * get the array values re-indexed with an integer sequence starting from 0
     * @see array_values()
     * @return ArrayWrap
     */
    public function values()
    {
        return $this->reply(array_values($this->data));
    }

    /**
     * Calculate the sum of values in the array
     * @see array_sum()
     * @return mixed
     */
    public function sum()
    {
        return array_sum($this->data);
    }

    /**
     * Calculate the average of values in the array
     * @return float
     */
    public function avg()
    {
        return array_sum($this->data) / count($this->data);
    }

    /**
     * Calculate the product of values in the array
     * @see array_product()
     * @return mixed
     */
    public function product()
    {
        return array_product($this->data);
    }

    /**
     * returns the biggest number in the array
     * @see max()
     * @see array_reduce()
     * @return int
     */
    public function max()
    {
        return $this->reduce(function ($agg, $curr) {
            return $agg !== null ? max($agg, $curr) : $curr;
        });
    }

    /**
     * returns the smallest number in the array.
     * @return int
     */
    public function min()
    {
        return $this->reduce(function ($agg, $curr) {
            return $agg !== null ? min($agg, $curr) : $curr;
        });
    }

    /**
     * get number of elements in the array
     * @see count()
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * get the number of elements in the array
     * @see count()
     * @return int
     */
    public function length()
    {
        return $this->count();
    }

    /**
     * shift the first element off the array.
     * @return mixed the shifted element
     */
    public function shift()
    {
        return array_shift($this->data);
    }

    /**
     * unshift or prepend one or more elements to the array.
     * @param mixed $item,...
     * @return $this
     */
    // @codingStandardsIgnoreLine
    public function unshift($item)
    {
        $args = func_get_args();
        array_unshift($args, []);
        $args[0] = &$this->data;
        call_user_func_array('array_unshift', $args);
        return $this;
    }

    /**
     * push one or more elements to the end of array.
     * @param mixed $item,...
     * @return $this
     */
    // @codingStandardsIgnoreLine
    public function push($item)
    {
        $args = func_get_args();
        array_unshift($args, []);
        $args[0] = &$this->data;
        call_user_func_array('array_push', $args);
        return $this;
    }

    /**
     * flip keys and values of the array.
     * @see array_flip()
     * @return ArrayWrap
     */
    public function flip()
    {
        return $this->reply(array_flip($this->data));
    }

    /**
     * sort the array
     * @see sort()
     * @param int|null $sortFlags
     * @return ArrayWrap
     */
    public function sort($sortFlags = null)
    {
        $data = $this->data;
        sort($data, $sortFlags);
        return $this->reply($data);
    }

    /**
     * @internal
     * @param $arrays
     * @return array
     */
    private function makeRaw($arrays)
    {
        return array_map(function ($item) {
            return $item instanceof ArrayWrap ? $item->getRaw() : (array)$item;
        }, $arrays);
    }
}
