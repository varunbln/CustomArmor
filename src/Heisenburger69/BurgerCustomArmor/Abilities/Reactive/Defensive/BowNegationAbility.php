<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive;

use pocketmine\item\Bow;
use pocketmine\Player;

class BowNegationAbility extends DamageNegationAbility
{
    public function canActivate(Player $damager): bool
    {
        if ($damager->getInventory()->getItemInHand() instanceof Bow) {
            return true;
        }
        return false;
    }
}