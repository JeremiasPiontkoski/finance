<?php

use OpenApi\Generator;

require __DIR__ . "/../vendor/autoload.php";

$openapi = Generator::scan([__DIR__ . "/../source/Controllers"]);

header("Content-Type: application/json");
echo $openapi->toJson();