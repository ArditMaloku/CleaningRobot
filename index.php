<?php

use CleaningRobot\CleaningRobot;

require_once realpath("vendor/autoload.php");

$housesJson = file_get_contents("data/houses.json");

$houses = json_decode($housesJson, true);

foreach ($houses as $key => $house) {
    print_r('==========================================' . PHP_EOL);
    print_r($key + 1 . 'nth house' . PHP_EOL);
    print_r('==========================================' . PHP_EOL);
    $cleaningRobot = new CleaningRobot($house);
    $cleaningRobot->clean();
    $cleaningRobot->getBlockedBlocksNumber();
    $cleaningRobot->getActiveBlocksNumber();
}
