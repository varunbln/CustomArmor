<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Togglable;

use pocketmine\entity\EffectInstance;
use pocketmine\Player;

class EffectAbility extends TogglableAbility
{
    /**
     * @var EffectInstance
     */
    private $effect;

    public function __construct(EffectInstance $effectInstance)
    {
        $this->effect = $effectInstance;
    }

    public function on(Player $player)
    {
        $player->addEffect($this->effect);
    }

    public function off(Player $player)
    {
        if ($player->hasEffect($this->effect->getId())) {
            $player->removeEffect($this->effect->getId());
        }
    }
}