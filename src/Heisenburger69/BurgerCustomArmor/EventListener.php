<?php

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\DefensiveAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive\OffensiveAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\TogglableAbility;
use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use Heisenburger69\BurgerCustomArmor\Events\CustomSetEquippedEvent;
use Heisenburger69\BurgerCustomArmor\Events\CustomSetUnequippedEvent;
use Heisenburger69\BurgerCustomArmor\Utils\EquipmentUtils;
use Heisenburger69\BurgerCustomArmor\Utils\Utils;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class EventListener implements Listener
{
    /**
     * @var Main
     */
    private $plugin;

    /**
     * EventListener constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerJoinEvent $event
     * @priority HIGH
     */
    public function onJoin(PlayerJoinEvent $event): void
    {
        EquipmentUtils::updateSetUsage($event->getPlayer());
    }

    /**
     * Removes the user from the array of players using CustomSets to prevent wasting memory
     *
     * @param PlayerQuitEvent $event
     * @priority HIGH
     */
    public function onQuit(PlayerQuitEvent $event): void
    {
        foreach (Main::$instance->using as $setName => $players) {
            if (!is_array($players)) {
                continue;
            }
            foreach ($players as $playerName => $using) {
                if ($playerName !== $event->getPlayer()->getName()) continue;
                unset(Main::$instance->using[$setName][$playerName]);
            }
        }
        //TODO: Do I call CustomSetUnequippedEvent here? Seems kinda redundant to do so once the player has quit.
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onDefensiveAbility(EntityDamageByEntityEvent $event): void
    {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if (!$player instanceof Player || !$damager instanceof Player) {
            return;
        }
        if (($nbt = $player->getArmorInventory()->getHelmet()->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if (!EquipmentUtils::canUseSet($player, $setName)) {
            return;
        }
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if (!Utils::checkProtectionLevel($player->getLevel())) {
                return;
            }
            if ($ability instanceof DefensiveAbility && $ability->canActivate($damager)) {
                $ability->activate($event);
            }
        }
    }

    /**
     * Overwriting the defense points of each armor piece if they're part of a Custom Armor Set
     *
     * @param EntityDamageEvent $event
     */
    public function onModifier(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        $items = $player->getArmorInventory()->getContents();
        $totalP = 0;
        foreach ($items as $item) {
            $itemP = $item->getDefensePoints();
            if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) !== null) {
                $armorSet = $this->plugin->customSets[$nbt->getValue()];
                if (Utils::isHelmet($item)) {
                    $itemP = $armorSet->getHelmetDefensePoints();
                } elseif (Utils::isChestplate($item)) {
                    $itemP = $armorSet->getChestplateDefensePoints();
                } elseif (Utils::isLeggings($item)) {
                    $itemP = $armorSet->getLeggingsDefensePoints();
                } elseif (Utils::isBoots($item)) {
                    $itemP = $armorSet->getBootsDefensePoints();
                }
            }
            $totalP += $itemP;
        }
        $event->setModifier(-$event->getFinalDamage() * $totalP * 0.04, EntityDamageEvent::MODIFIER_ARMOR);
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onOffensiveAbility(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if (!$player instanceof Player || !$damager instanceof Player) {
            return;
        }
        if (($nbt = $damager->getArmorInventory()->getHelmet()->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if (!EquipmentUtils::canUseSet($damager, $setName)) {
            return;
        }
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if (!Utils::checkProtectionLevel($player->getLevel())) {
                return;
            }
            if ($ability instanceof OffensiveAbility && $ability->canActivate($damager)) {
                $ability->activate($event);
            }
        }
    }

    /**
     * @param EntityArmorChangeEvent $event
     */
    public function onEquip(EntityArmorChangeEvent $event)
    {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        $item = $event->getNewItem();
        if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if (!isset($this->plugin->using[$setName])) {
            return;
        }
        EquipmentUtils::addUsingSet($player, $item, $setName);
        if (!EquipmentUtils::canUseSet($player, $setName)) {
            return;
        }
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        ($event = new CustomSetEquippedEvent($player, $armorSet))->call();
        foreach ($armorSet->getAbilities() as $ability) {
            if (!Utils::checkProtectionLevel($player->getLevel())) {
                return;
            }
            if ($ability instanceof TogglableAbility) {
                $ability->on($player);
            }
        }
    }

    /**
     * @param EntityArmorChangeEvent $event
     */
    public function onUnequip(EntityArmorChangeEvent $event)
    {
        $player = $event->getEntity();
        if (!$player instanceof Player) {
            return;
        }
        $item = $event->getOldItem();
        if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if (!isset($this->plugin->using[$setName])) {
            return;
        }
        $fullSetWorn = false;
        if (EquipmentUtils::canUseSet($player, $setName)) {
            $fullSetWorn = true;
        }
        EquipmentUtils::removeUsingSet($player, $item, $setName);
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        if ($fullSetWorn) {
            ($event = new CustomSetUnequippedEvent($player, $armorSet))->call();
            foreach ($armorSet->getAbilities() as $ability) {
                if ($ability instanceof TogglableAbility) {
                    $ability->off($player);
                }
            }
        }
    }
}