<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive;

use pocketmine\item\Axe;
use pocketmine\Player;

class AxeAmplificationAbility extends DamageAmplificationAbility
{
    public function canActivate(Player $damager): bool
    {
        if ($damager->getInventory()->getItemInHand() instanceof Axe) {
            return true;
        }
        return false;
    }
}