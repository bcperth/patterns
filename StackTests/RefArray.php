<?php
$array = [
    'foo' => [
        'a' => [1, 2],
        'b' => [3, 4]
    ]
];
$pointer =& $array['foo']['b']; //Save a pointer to 'b'.
$sum = array_sum($pointer);
$pointer = $sum;
print_r($array);
