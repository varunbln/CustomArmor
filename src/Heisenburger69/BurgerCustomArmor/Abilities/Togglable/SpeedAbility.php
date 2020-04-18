<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Togglable;

use Heisenburger69\BurgerCustomArmor\Abilities\ArmorAbility;
use pocketmine\entity\Attribute;
use pocketmine\Player;

class SpeedAbility extends TogglableAbility
{
    /**
     * @var float
     */
    private $speed;

    public function __construct(float $speed)
    {
        $this->speed = $speed;
    }

    public function on(Player $player)
    {
        $attr = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
        $attr->setValue($attr->getValue() * $this->speed);
    }

    public function off(Player $player)
    {
        $attr = $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
        $attr->setValue($attr->getValue() / $this->speed);
    }
}