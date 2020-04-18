<?php

namespace Heisenburger69\BurgerCustomArmor\Utils;

use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\item\ChainBoots;
use pocketmine\item\ChainChestplate;
use pocketmine\item\ChainHelmet;
use pocketmine\item\ChainLeggings;
use pocketmine\item\DiamondBoots;
use pocketmine\item\DiamondChestplate;
use pocketmine\item\DiamondHelmet;
use pocketmine\item\DiamondLeggings;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldChestplate;
use pocketmine\item\GoldHelmet;
use pocketmine\item\GoldLeggings;
use pocketmine\item\IronBoots;
use pocketmine\item\IronChestplate;
use pocketmine\item\IronHelmet;
use pocketmine\item\IronLeggings;
use pocketmine\item\Item;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\LeatherPants;
use pocketmine\item\LeatherTunic;
use pocketmine\level\Level;

class Utils
{
    /**
     * @param Level $level
     * @return bool
     */
    public static function checkProtectionLevel(Level $level): bool
    {
        $blacklist = Main::$instance->getConfig()->get("enable-world-blacklist");
        $whitelist = Main::$instance->getConfig()->get("enable-world-whitelist");
        $levelName = $level->getName();

        if ($blacklist === $whitelist) return true;

        if ($blacklist) {
            $disallowedWorlds = Main::$instance->getConfig()->get("blacklisted-worlds");
            if (in_array($levelName, $disallowedWorlds)) return false;
            return true;
        }

        if ($whitelist) {
            $allowedWorlds = Main::$instance->getConfig()->get("whitelisted-worlds");
            if (in_array($levelName, $allowedWorlds)) return true;
            return false;
        }

        return false;
    }
    
    public static function isHelmet(Item $item): bool
    {
        if (
            $item instanceof DiamondHelmet ||
            $item instanceof GoldHelmet ||
            $item instanceof IronHelmet ||
            $item instanceof ChainHelmet ||
            $item instanceof LeatherCap) {
            return true;
        }
        return false;
    }

    public static function isChestplate(Item $item): bool
    {
        if (
            $item instanceof DiamondChestplate ||
            $item instanceof IronChestplate ||
            $item instanceof GoldChestplate ||
            $item instanceof ChainChestplate ||
            $item instanceof LeatherTunic) {
            return true;
        }
        return false;
    }

    public static function isLeggings(Item $item): bool
    {
        if ($item instanceof DiamondLeggings ||
            $item instanceof GoldLeggings ||
            $item instanceof IronLeggings ||
            $item instanceof ChainLeggings ||
            $item instanceof LeatherPants) {
            return true;
        }
        return false;
    }

    public static function isBoots(Item $item): bool
    {
        if ($item instanceof DiamondBoots ||
            $item instanceof GoldBoots ||
            $item instanceof IronBoots ||
            $item instanceof ChainBoots ||
            $item instanceof LeatherBoots) {
            return true;
        }
        return false;
    }
}