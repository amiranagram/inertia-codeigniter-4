<?php

use Inertia\Factory;
use Inertia\Services;

if (! function_exists('inertia')) {
    /**
     * @param null $component
     * @param array $props
     * @return Factory|string
     */
    function inertia($component = null, $props = [])
    {
        $inertia = Services::inertia();

        if ($component) {
            return $inertia->render($component, $props);
        }

        return $inertia;
    }
}

if (! function_exists('array_only')) {
    /**
     * @param $array
     * @param $keys
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}

if (! function_exists('array_get')) {
    /**
     * @param $array
     * @param $key
     * @param null $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (! is_array($array)) {
            return closure_call($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? closure_call($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return closure_call($default);
            }
        }

        return $array;
    }
}

if (! function_exists('array_set')) {
    /**
     * @param $array
     * @param $key
     * @param $value
     * @return array|mixed
     */
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}

if (! function_exists('closure_call')) {
    /**
     * @param $closure
     * @return mixed
     */
    function closure_call($closure)
    {
        return $closure instanceof Closure ? $closure() : $closure;
    }
}
