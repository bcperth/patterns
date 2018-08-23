<?php
$filename = "C:\\ProgramData\\MySQL\\MySQL Server 5.7\\Data\\transperth";
$handle = fopen($filename, "rb");
$contents = fread($handle, filesize($filename));
fclose($handle);