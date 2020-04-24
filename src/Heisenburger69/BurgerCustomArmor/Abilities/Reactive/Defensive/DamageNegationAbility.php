<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class DamageNegationAbility extends DefensiveAbility
{

    /**
     * @var float
     */
    private $negation;

    public function __construct(float $negation)
    {
        $this->negation = $negation;
    }

    public function canActivate(Player $damager): bool
    {
        return true;
    }

    public function activate(EntityDamageByEntityEvent $event)
    {
        $baseDmg = $event->getBaseDamage() - ($event->getBaseDamage() * $this->negation);
        if ($baseDmg < 0) $baseDmg = 0;
        $event->setBaseDamage($baseDmg);
    }

}