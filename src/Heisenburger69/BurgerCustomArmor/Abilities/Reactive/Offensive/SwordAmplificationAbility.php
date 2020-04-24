<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive;

use pocketmine\item\Sword;
use pocketmine\Player;

class SwordAmplificationAbility extends DamageAmplificationAbility
{
    public function canActivate(Player $damager): bool
    {
        if ($damager->getInventory()->getItemInHand() instanceof Sword) {
            return true;
        }
        return false;
    }
}