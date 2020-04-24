<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class KnockbackNegationAbility extends DefensiveAbility
{
    /**
     * @var float
     */
    private $multiplier;

    public function __construct(float $multiplier)
    {
        $this->multiplier = $multiplier;
    }

    public function canActivate(Player $damager): bool
    {
        return true;
    }

    public function activate(EntityDamageByEntityEvent $event)
    {
        $kb = $event->getKnockBack();
        $event->setKnockBack($kb * $this->multiplier);
    }
}