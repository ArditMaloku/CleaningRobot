<?php

namespace CleaningRobot;

class CleaningRobot implements CleaningRobotInterface
{
    public $blocks = [];
    public $going = 'right';
    public $blockedBlocks;
    public $activeBlocks;
    public $cleaned;

    private $xKeysCount;
    private $yKeysCount;
    private $activeXKey = 0;
    private $activeYKey = 0;
    private $visitedBlocks = [];
    private $robotStarted = false;

    private const RIGHT = 'right';
    private const BOTTOM = 'bottom';
    private const LEFT = 'left';
    private const TOP = 'top';
    private const ENDED = 'ended';

    function __construct($blocks)
    {
        if (!isset($blocks[0][0])) {
            print_r("Where do you live bro?");
            exit;
        }

        $this->blocks = $blocks;
        $this->xKeysCount = count($blocks[0]);
        $this->yKeysCount = count($blocks);

        $keys = array_keys($blocks);
        $activeBlocks = 0;
        $blockedBlocks = 0;

        for ($i = 0; $i < count($blocks); $i++) {
            foreach ($blocks[$keys[$i]] as $value) {
                if ($value == '.') {
                    $activeBlocks++;
                } else if ($value == 'X') {
                    $blockedBlocks++;
                }
            }
        }

        $this->activeBlocks = $activeBlocks;
        $this->blockedBlocks = $blockedBlocks;
    }

    public function getBlockedBlocksNumber()
    {
        if ($this->robotStarted) {
            print_r('You got ' . $this->blockedBlocks . ' blocked blocks in your house!' . PHP_EOL);
        } else {
            print_r('Turn on the robot!' . PHP_EOL);
        }
    }

    public function getActiveBlocksNumber()
    {
        if ($this->robotStarted) {
            print_r('You got ' . $this->activeBlocks . ' active blocks in your house, but ' . $this->cleaned . ' are reachable!' . PHP_EOL);
        } else {
            print_r('Turn on the robot!' . PHP_EOL);
        }
    }

    public function clean()
    {
        $this->robotStarted = true;

        print_r('ROBOT STARTED CLEANING RAISE YOUR FEETS!' . PHP_EOL . PHP_EOL);

        runAgain:

        switch ($this->going) {
            case static::RIGHT:
                for ($i = $this->activeXKey; $i < $this->xKeysCount; $i++) {
                    if ($this->blocks[$this->activeYKey][$i] == '.') {
                        $blockKey = $this->activeYKey . $i;

                        if ($this->visitBlock($blockKey) == static::ENDED) {
                            goto end;
                        }

                        $this->activeXKey = $i;
                    } else {
                        $this->going = $this->switchGoingTo();

                        if (isset($this->blocks[$this->activeYKey + 1][$this->activeXKey]) && $this->blocks[$this->activeYKey + 1][$this->activeXKey] == '.') {
                            $this->activeYKey++;
                        } else {
                            $this->going = $this->switchGoingTo();
                        }

                        goto runAgain;
                    }
                }
                if (!isset($this->blocks[$this->activeYKey][$this->activeXKey + 1])) {
                    $this->going = $this->switchGoingTo();

                    goto runAgain;
                }
                break;
            case static::BOTTOM:
                for ($i = $this->activeYKey; $i < $this->yKeysCount; $i++) {
                    if ($this->blocks[$i][$this->activeXKey] == '.') {
                        $blockKey = $i . $this->activeXKey;

                        if ($this->visitBlock($blockKey) == static::ENDED) {
                            goto end;
                        }

                        $this->activeYKey = $i;
                    } else {
                        $this->going = $this->switchGoingTo();

                        if (isset($this->blocks[$this->activeYKey][$this->activeXKey - 1]) && $this->blocks[$this->activeYKey][$this->activeXKey - 1] == '.') {
                            $this->activeXKey--;
                        } else {
                            $this->going = $this->switchGoingTo();
                        }

                        goto runAgain;
                    }
                }
                if (!isset($this->blocks[$this->activeYKey + 1][$this->activeXKey])) {
                    $this->going = $this->switchGoingTo();

                    goto runAgain;
                }
                break;
            case static::LEFT:
                for ($i = $this->activeXKey; $i >= 0; $i--) {
                    if ($this->blocks[$this->activeYKey][$i] == '.') {
                        $blockKey = $this->activeYKey . $i;

                        if ($this->visitBlock($blockKey) == static::ENDED) {
                            goto end;
                        }

                        $this->activeXKey = $i;
                    } else {
                        $this->going = $this->switchGoingTo();

                        if (isset($this->blocks[$this->activeYKey - 1][$this->activeXKey]) && $this->blocks[$this->activeYKey - 1][$this->activeXKey] == '.') {
                            $this->activeYKey--;
                        } else {
                            $this->going = $this->switchGoingTo($this->going);
                        }

                        goto runAgain;
                    }
                }
                if (!isset($this->blocks[$this->activeYKey][$this->activeXKey + 1])) {
                    $this->going = $this->switchGoingTo();
                    goto runAgain;
                }
                break;
            case static::TOP:
                for ($i = $this->activeYKey; $i >= 0; $i--) {
                    if ($this->blocks[$i][$this->activeXKey] == '.') {
                        $blockKey = $i . $this->activeXKey;

                        if ($this->visitBlock($blockKey) == static::ENDED) {
                            goto end;
                        }

                        $this->activeYKey = $i;
                    } else {
                        $this->going = $this->switchGoingTo();

                        if (isset($this->blocks[$this->activeYKey][$this->activeXKey + 1]) && $this->blocks[$this->activeYKey][$this->activeXKey + 1] == '.') {
                            $this->activeXKey++;
                        } else {
                            $this->going = $this->switchGoingTo();
                        }
                        goto runAgain;
                    }
                }

                if (!isset($this->blocks[$this->activeYKey + 1][$this->activeXKey])) {
                    $this->going = $this->switchGoingTo();
                    goto runAgain;
                }
                break;
        }

        end:

        print_r('===' . PHP_EOL);
        print_r('==' . PHP_EOL);
        print_r('=' . PHP_EOL);

        $this->cleaned = count($this->visitedBlocks);

        print_r("You're home was messy!" . PHP_EOL . "The robot managed to clean " . $this->cleaned . " blocks!" . PHP_EOL . PHP_EOL);
    }

    private function switchGoingTo()
    {
        switch ($this->going) {
            case static::RIGHT:
                return static::BOTTOM;
                break;
            case static::BOTTOM:
                return static::LEFT;
                break;
            case static::LEFT:
                return static::TOP;
                break;
            case static::TOP:
                return static::RIGHT;
                break;
        }
    }

    private function visitBlock($blockKey)
    {
        if (isset($this->visitedBlocks[$blockKey]) && strlen($this->visitedBlocks[$blockKey]) == 3) {
            return static::ENDED;
        }

        print_r('Robot is going ' . $this->going . '. On block: ' . $blockKey . PHP_EOL);

        if (isset($this->visitedBlocks[$blockKey])) {
            $this->visitedBlocks[$blockKey] .= '+';
        } else {
            $this->visitedBlocks[$blockKey] = '+';
        }
    }
}
