<?php

namespace WPMyProductWebspark\Utils;

class ArrayUtils
{
    public static function insertAfter(array $array, $insert_key, array $element): array
    {

        $new_array = array();

        foreach ($array as $key => $value) {

            $new_array[$key] = $value;

            if ($insert_key == $key) {

                foreach ($element as $k => $v) {
                    $new_array[$k] = $v;
                }
            }
        }

        return $new_array;
    }
}