<?php

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\TogglableAbility;
use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use Heisenburger69\BurgerCustomArmor\Utils\EquipmentUtils;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

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

    public function onDamage(EntityDamageByEntityEvent $event)
    {
        if ($event->getModifier(EntityDamageEvent::MODIFIER_ARMOR)) ;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        foreach ($this->plugin->customSets as $armorSet) {
            if ($armorSet instanceof CustomArmorSet) {
                $items = $armorSet->getSetPieces();
                foreach ($items as $item) {
                    $player->getInventory()->addItem($item);
                }
            }
        }
    }

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
        foreach ($armorSet->getAbilities() as $ability) {
            if ($ability instanceof TogglableAbility) {
                $ability->on($player);
            }
        }
    }

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
        EquipmentUtils::removeUsingSet($player, $item, $setName);
        $armorSet = $this->plugin->customSets[$setName];
        if (!$armorSet instanceof CustomArmorSet) {
            return;
        }
        foreach ($armorSet->getAbilities() as $ability) {
            if ($ability instanceof TogglableAbility) {
                $ability->off($player);
            }
        }
    }
}