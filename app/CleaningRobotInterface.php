<?php

namespace CleaningRobot;

interface CleaningRobotInterface
{
    public function getBlockedBlocksNumber();
    public function getActiveBlocksNumber();
    public function clean();
}
