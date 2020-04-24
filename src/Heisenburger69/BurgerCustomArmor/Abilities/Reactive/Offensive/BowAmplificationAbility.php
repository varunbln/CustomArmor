<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive;

use pocketmine\item\Bow;
use pocketmine\Player;

class BowAmplificationAbility extends DamageAmplificationAbility
{
    public function canActivate(Player $damager): bool
    {
        if ($damager->getInventory()->getItemInHand() instanceof Bow) {
            return true;
        }
        return false;
    }
}