<?php

namespace Heisenburger69\BurgerCustomArmor\Events;

use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use pocketmine\event\Event;
use pocketmine\Player;

class CustomSetUnequippedEvent extends Event
{

    /**
     * @var CustomArmorSet
     */
    private $armorSet;

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player, CustomArmorSet $armorSet)
    {
        $this->player = $player;
        $this->armorSet = $armorSet;
    }

    /**
     * @return CustomArmorSet
     */
    public function getArmorSet(): CustomArmorSet
    {
        return $this->armorSet;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}