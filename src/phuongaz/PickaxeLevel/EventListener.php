<?php

namespace phuongaz\PickaxeLevel;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\block\BlockIds;
use phuongaz\PickaxeLevel\event\PickaxeLevelUpEvent;
use onebone\economyapi\EconomyAPI;

Class EventListener implements Listener{

    public array $blockids = [
        BlockIds::COAL_ORE => 4,
        BlockIds::DIAMOND_ORE => 5,
        BlockIds::EMERALD_ORE => 4,
        BlockIds::GOLD_ORE => 3,
        BlockIds::IRON_ORE => 3,
        BlockIds::LAPIS_ORE => 5,
        BlockIds::NETHER_QUARTZ_ORE => 7
    ];

    public function onBreack(BlockBreakEvent $event) :void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $loader = Loader::getInstance();
        if(!$loader->isPickaxeLevel($item)) return;
        if(!$loader->isOwner($player, $item)) return;
        if(in_array($block->getId(), array_keys($this->blockids))){
            $player->getInventory()->setItemInHand(Loader::getInstance()->addExp($item, $this->blockids[$block->getId()]));
            $player->sendPopup($block->getName() . " +".$this->blockids[$block->getId()]);
        }
    }

    public function onJoin(PlayerJoinEvent $event) :void
    {
        $player = $event->getPlayer();
        if(!$player->hasPlayedBefore()){
            $item = Loader::getInstance()->getPickaxe($player);
            $player->getInventory()->setItemInHand($item);
        }
    }

    public function onLevelUp(PickaxeLevelUpEvent $event) :void
    {
        $player = $event->getPlayer();
        $level = $event->getLevel();
        $player->sendTitle("+1 Level", "You received ". 10000*$level);
        EconomyAPI::getInstance()->addMoney($player, 10000*$level);
    }
}