<?php

namespace phuongaz\PickaxeLevel\event;

use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class PickaxeLevelUpEvent extends PlayerEvent
{
    private int $level;

    public function __construct(Player $player, int $level)
    {
        $this->player = $player;
        $this->level = $level;
    }

    public function getLevel() :int
    {
        return $this->level;
    }
}