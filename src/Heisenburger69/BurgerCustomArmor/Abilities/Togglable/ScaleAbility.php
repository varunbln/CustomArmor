<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Togglable;

use pocketmine\Player;

class ScaleAbility extends TogglableAbility
{
    /**
     * @var float
     */
    private $scale;

    public function __construct(float $scale)
    {
        $this->scale = $scale;
    }

    public function on(Player $player)
    {
        $player->setScale($this->scale);
    }

    public function off(Player $player)
    {
        $player->setScale(1);
    }
}