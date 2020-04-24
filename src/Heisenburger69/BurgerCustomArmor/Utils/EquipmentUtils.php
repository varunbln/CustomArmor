<?php

namespace Heisenburger69\BurgerCustomArmor\Utils;

use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\TogglableAbility;
use Heisenburger69\BurgerCustomArmor\Events\CustomSetEquippedEvent;
use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\item\Item;
use pocketmine\Player;

class EquipmentUtils
{

    /**
     * @param Player $player
     * @param Item $item
     * @param string $setName
     */
    public static function removeUsingSet(Player $player, Item $item, string $setName): void
    {
        $playerName = $player->getName();
        if (!isset(Main::$instance->using[$setName][$playerName])) {
            self::initPlayer($playerName, $setName);
        }

        if (Utils::isHelmet($item)) {
            Main::$instance->using[$setName][$playerName]["helmet"] = false;
        } elseif (Utils::isChestplate($item)) {
            Main::$instance->using[$setName][$playerName]["chestplate"] = false;
        } elseif (Utils::isLeggings($item)) {
            Main::$instance->using[$setName][$playerName]["leggings"] = false;
        } elseif (Utils::isBoots($item)) {
            Main::$instance->using[$setName][$playerName]["boots"] = false;
        }
    }

    /**
     * @param Player $player
     * @param Item $item
     * @param string $setName
     */
    public static function addUsingSet(Player $player, Item $item, string $setName): void
    {
        $playerName = $player->getName();
        if (!isset(Main::$instance->using[$setName][$playerName])) {
            self::initPlayer($playerName, $setName);
        }

        if (Utils::isHelmet($item)) {
            Main::$instance->using[$setName][$playerName]["helmet"] = true;
        } elseif (Utils::isChestplate($item)) {
            Main::$instance->using[$setName][$playerName]["chestplate"] = true;
        } elseif (Utils::isLeggings($item)) {
            Main::$instance->using[$setName][$playerName]["leggings"] = true;
        } elseif (Utils::isBoots($item)) {
            Main::$instance->using[$setName][$playerName]["boots"] = true;
        }
    }

    /**
     * @param string $playerName
     * @param string $setName
     */
    public static function initPlayer(string $playerName, string $setName): void
    {
        Main::$instance->using[$setName][$playerName] =
            [
                "helmet" => false,
                "chestplate" => false,
                "leggings" => false,
                "boots" => false,
            ];
    }

    /**
     * @param Player $player
     * @param string $setName
     * @return bool
     */
    public static function canUseSet(Player $player, string $setName): bool
    {
        $playerName = $player->getName();
        if (!isset(Main::$instance->using[$setName][$playerName])) return false;
        if (
            Main::$instance->using[$setName][$playerName]["helmet"] === true &&
            Main::$instance->using[$setName][$playerName]["chestplate"] === true &&
            Main::$instance->using[$setName][$playerName]["leggings"] === true &&
            Main::$instance->using[$setName][$playerName]["boots"] === true
        ) {
            return true;
        }
        return false;
    }

    /**
     * Checks the players armor and adds the player back to the array of players using CustomSets
     *
     * @param Player $player
     */
    public static function updateSetUsage(Player $player)
    {
        $setName = null;
        $armorSet = null;
        foreach ($player->getArmorInventory()->getContents() as $item) {
            if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) === null) {
                continue;
            }
            $setName = $nbt->getValue();
            if (!isset(Main::$instance->customSets[$setName])) {
                continue;
            }
            $armorSet = Main::$instance->customSets[$setName];
            self::addUsingSet($player, $item, $setName);
        }
        if ($setName === null || $armorSet === null) return;
        if (!self::canUseSet($player, $setName)) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if (!Utils::checkProtectionLevel($player->getLevel())) {
                return;
            }
            if ($ability instanceof TogglableAbility) {
                $ability->on($player);
            }
        }
        ($event = new CustomSetEquippedEvent($player, $armorSet))->call();
    }
}