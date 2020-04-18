<?php

namespace Heisenburger69\BurgerCustomArmor\ArmorSets;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat as C;

class ArmorSetUtils
{

    /**
     * @param string $name
     * @return int|null
     */
    public static function getTierFromName(string $name): ?int
    {
        switch ($name) {
            case "leather":
                return CustomArmorSet::TIER_LEATHER;
            case "chainmail":
                return CustomArmorSet::TIER_CHAIN;
            case "gold":
                return CustomArmorSet::TIER_GOLD;
            case "iron":
                return CustomArmorSet::TIER_IRON;
            case "diamond":
                return CustomArmorSet::TIER_DIAMOND;
            default:
                return null;
        }
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getHelmetFromTier(int $tier): Item
    {
        switch ($tier) {
            case CustomArmorSet::TIER_DIAMOND:
                return Item::get(Item::DIAMOND_HELMET);
            case CustomArmorSet::TIER_IRON:
                return Item::get(Item::IRON_HELMET);
            case CustomArmorSet::TIER_GOLD:
                return Item::get(Item::GOLD_HELMET);
            case CustomArmorSet::TIER_CHAIN:
                return Item::get(Item::CHAINMAIL_HELMET);
            case CustomArmorSet::TIER_LEATHER:
                return Item::get(Item::LEATHER_HELMET);
            default:
                return Item::get(Item::AIR);
        }
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getChestplateFromTier(int $tier): Item
    {
        switch ($tier) {
            case CustomArmorSet::TIER_DIAMOND:
                return Item::get(Item::DIAMOND_CHESTPLATE);
            case CustomArmorSet::TIER_IRON:
                return Item::get(Item::IRON_CHESTPLATE);
            case CustomArmorSet::TIER_GOLD:
                return Item::get(Item::GOLD_CHESTPLATE);
            case CustomArmorSet::TIER_CHAIN:
                return Item::get(Item::CHAIN_CHESTPLATE);
            case CustomArmorSet::TIER_LEATHER:
                return Item::get(Item::LEATHER_CHESTPLATE);
            default:
                return Item::get(Item::AIR);
        }
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getLeggingsFromTier(int $tier): Item
    {
        switch ($tier) {
            case CustomArmorSet::TIER_DIAMOND:
                return Item::get(Item::DIAMOND_LEGGINGS);
            case CustomArmorSet::TIER_IRON:
                return Item::get(Item::IRON_LEGGINGS);
            case CustomArmorSet::TIER_GOLD:
                return Item::get(Item::GOLD_LEGGINGS);
            case CustomArmorSet::TIER_CHAIN:
                return Item::get(Item::CHAIN_LEGGINGS);
            case CustomArmorSet::TIER_LEATHER:
                return Item::get(Item::LEATHER_LEGGINGS);
            default:
                return Item::get(Item::AIR);
        }
    }

    /**
     * @param int $tier
     * @return Item
     */
    public static function getBootsFromTier(int $tier): Item
    {
        switch ($tier) {
            case CustomArmorSet::TIER_DIAMOND:
                return Item::get(Item::DIAMOND_BOOTS);
            case CustomArmorSet::TIER_IRON:
                return Item::get(Item::IRON_BOOTS);
            case CustomArmorSet::TIER_GOLD:
                return Item::get(Item::GOLD_BOOTS);
            case CustomArmorSet::TIER_CHAIN:
                return Item::get(Item::CHAIN_BOOTS);
            case CustomArmorSet::TIER_LEATHER:
                return Item::get(Item::LEATHER_BOOTS);
            default:
                return Item::get(Item::AIR);
        }
    }

    public static function getHelmetLore(array $lores, array $setBonusLore)
    {
        $lore = [];
        $itemLore = [];
        $setBonus = implode("\n", $setBonusLore);

        if(isset($lores["helmet"])) {
            $itemLore = $lores["helmet"];
        }
        foreach ($itemLore as $line) {
            $lore[] = C::RESET . C::colorize(str_replace("{FULLSETBONUS}", $setBonus, $line));
        }

        return $lore;
    }

    public static function getChestplateLore(array $lores, array $setBonusLore)
    {
        $lore = [];
        $itemLore = [];
        $setBonus = implode("\n", $setBonusLore);

        if(isset($lores["chestplate"])) {
            $itemLore = $lores["chestplate"];
        }
        foreach ($itemLore as $line) {
            $lore[] = C::RESET . C::colorize(str_replace("{FULLSETBONUS}", $setBonus, $line));
        }

        return $lore;
    }

    public static function getLeggingsLore(array $lores, array $setBonusLore)
    {
        $lore = [];
        $itemLore = [];
        $setBonus = implode("\n", $setBonusLore);

        if(isset($lores["leggings"])) {
            $itemLore = $lores["leggings"];
        }
        foreach ($itemLore as $line) {
            $lore[] = C::RESET . C::colorize(str_replace("{FULLSETBONUS}", $setBonus, $line));
        }

        return $lore;
    }

    public static function getBootsLore(array $lores, array $setBonusLore)
    {
        $lore = [];
        $itemLore = [];
        $setBonus = implode("\n", $setBonusLore);

        if(isset($lores["boots"])) {
            $itemLore = $lores["boots"];
        }
        foreach ($itemLore as $line) {
            $lore[] = C::RESET . C::colorize(str_replace("{FULLSETBONUS}", $setBonus, $line));
        }

        return $lore;
    }

    public static function getTotalStrengthPoints(int $tier, array $strength): int
    {
        $helmetStrength = self::getHelmetFromTier($tier)->getDefensePoints();
        if(isset($strength["helmet"])) {
            $helmetStrength = $strength["helmet"];
        }

        $chestplateStrength = self::getChestplateFromTier($tier)->getDefensePoints();
        if(isset($strength["chestplate"])) {
            $chestplateStrength = $strength["chestplate"];
        }

        $leggingsStrength = self::getLeggingsFromTier($tier)->getDefensePoints();
        if(isset($strength["leggings"])) {
            $leggingsStrength = $strength["leggings"];
        }

        $bootsStrength = self::getBootsFromTier($tier)->getDefensePoints();
        if(isset($strength["boots"])) {
            $helmetStrength = $strength["boots"];
        }

        return $helmetStrength + $chestplateStrength + $leggingsStrength + $bootsStrength;

    }
}