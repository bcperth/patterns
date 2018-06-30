<?php

$a = '1';
echo $a.PHP_EOL;
$b = &$a;
echo $b.PHP_EOL;
$b = "2$b";
echo $b.PHP_EOL;
echo $a.PHP_EOL;

