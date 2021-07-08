<?php

namespace phuongaz\PickaxeLevel;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\item\Item;
use phuongaz\PickaxeLevel\event\PickaxeLevelUpEvent;

Class Loader extends PluginBase
{
    use SingletonTrait;

    public function onLoad(): void
    {
        self::setInstance($this);
        $this->getLogger()->info("PiCkAxElEvEl V0");
    }

    public function onEnable(): void
    {
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function getPickaxe(Player $player): Item
    {
        $item = Item::get(Item::DIAMOND_PICKAXE);
        $item->setCustomName("PiCkAxElEvEl (".$player->getName().")");
        $nbt = $item->getNamedTag();
        $nbt->setString("Powner", $player->getName());
        $nbt->setInt("Plvl", 0);
        $nbt->setInt("Pexp", 0);
        $nbt->setInt("Pnext", 2000);
        $item->setNamedTag($nbt);
        return $item;
    }

    public function addExp(Item $item, int $exp) :Item
    {
        $nbt = $item->getNamedTag();
        if($this->isPickaxeLevel($item)){
            $exp += $nbt->getInt("Pexp");
            if($exp >= $nbt->getInt("Pnext")){
                $nbt->setInt("Plvl", $nbt->getInt("Plvl")+1);
                $nbt->setInt("Pnext", $nbt->getInt("Plvl")*2000);
                $nbt->setInt("Pexp", 0);
                $player = Server::getInstance()->getPlayer($nbt->getString("Powner"));
                $event = new PickaxeLevelUpEvent($player, $nbt->getInt("Plvl"));
                $event->call();
            }
            $nbt->setInt("Pexp", $exp);
        }
        $item->setLore(["Level: ".$nbt->getInt("Plvl"), "Exp: ".$nbt->getInt("Pexp")."/".$nbt->getInt("Pnext")]);
        return $item->setNamedTag($nbt);
    }

    public function isOwner(Player $player, Item $item) :bool
    {
        if($this->isPickaxeLevel($item)){
            $nbt = $item->getNamedTag();
            return ($player->getName() == $nbt->getString("Powner"));
        }
    }

    public function isPickaxeLevel(Item $item) :bool
    {
        $nbt = $item->getNamedTag();
        return $nbt->hasTag('Plvl', IntTag::class);
    }
}