<?php

$a = '1';
echo $a.PHP_EOL;
$b = &$a;         // both $b and $a are 1
echo $b.PHP_EOL;
$b = "2$b";       // we changed $b to 21, so $a is alco changed (being refs to the same variable value)
echo $b.PHP_EOL;
echo $a.PHP_EOL;

