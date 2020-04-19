<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive;

use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\ReactiveAbility;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class OffensiveAbility extends ReactiveAbility
{
    public function canActivate(Player $damager): bool
    {
        return true;
    }

    public function activate(EntityDamageByEntityEvent $event)
    {

    }
}