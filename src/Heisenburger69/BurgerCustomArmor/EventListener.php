<?php

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\DamageNegationAbility;
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
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
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
     * @param EntityDamageByEntityEvent $event
     */
    public function onDefensiveAbility(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if(!$player instanceof Player || !$damager instanceof Player) {
            return;
        }
        if(($nbt = $player->getArmorInventory()->getHelmet()->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if(!EquipmentUtils::canUseSet($player, $setName)) {
            return;
        }
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if(!Utils::checkProtectionLevel($player->getLevel())) {
                return;
            }
            if ($ability instanceof DefensiveAbility && $ability->canActivate($damager)) {
                $ability->activate($event);
            }
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onOffensiveAbility(EntityDamageByEntityEvent $event)
    {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if(!$player instanceof Player || !$damager instanceof Player) {
            return;
        }
        if(($nbt = $damager->getArmorInventory()->getHelmet()->getNamedTagEntry("burgercustomarmor")) === null) {
            return;
        }
        $setName = $nbt->getValue();
        if(!EquipmentUtils::canUseSet($damager, $setName)) {
            return;
        }
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if(!Utils::checkProtectionLevel($player->getLevel())) {
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
        if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) === null){
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
            if(!Utils::checkProtectionLevel($player->getLevel())) {
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
        if (($nbt = $item->getNamedTagEntry("burgercustomarmor")) === null){
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
        if($fullSetWorn) {
            ($event = new CustomSetUnequippedEvent($player, $armorSet))->call();
            foreach ($armorSet->getAbilities() as $ability) {
                if ($ability instanceof TogglableAbility) {
                    $ability->off($player);
                }
            }
        }
    }
}