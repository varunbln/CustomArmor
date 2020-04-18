<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive;

use Heisenburger69\BurgerCustomArmor\Abilities\ArmorAbility;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class KnockbackAbility extends ArmorAbility
{
    /**
     * @var float
     */
    private $multiplier;

    public function __construct(float $multiplier)
    {
        $this->multiplier = $multiplier;
    }

    public function activate(EntityDamageByEntityEvent $event)
    {
        $kb = $event->getKnockBack();
        $event->setKnockBack($kb * $this->multiplier);
    }
}