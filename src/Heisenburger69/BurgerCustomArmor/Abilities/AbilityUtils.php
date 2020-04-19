<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities;

use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\AxeNegationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\BowNegationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\DamageNegationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\KnockbackNegationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Defensive\SwordNegationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive\AxeAmplificationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive\BowAmplificationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive\DamageAmplificationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Reactive\Offensive\SwordAmplificationAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\EffectAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\PermissionAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\ScaleAbility;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

class AbilityUtils
{

    /**
     * @param string $ability
     * @param mixed $values
     * @return ArmorAbility|null
     */
    public static function getAbility(string $ability, $values): ?ArmorAbility
    {
        switch ($ability) {
            case "Scale":
                return new ScaleAbility($values);
            case "Permission":
                return new PermissionAbility($values);
            case "Knockback":
                return new KnockbackNegationAbility($values);
            case "DamageNegation":
                return new DamageNegationAbility($values);
            case "SwordNegation":
                return new SwordNegationAbility($values);
            case "AxeNegation":
                return new AxeNegationAbility($values);
            case "BowNegation":
                return new BowNegationAbility($values);
            case "DamageAmplification":
                return new DamageAmplificationAbility($values);
            case "SwordAmplification":
                return new SwordAmplificationAbility($values);
            case "AxeAmplification":
                return new AxeAmplificationAbility($values);
            case "BowAmplification":
                return new BowAmplificationAbility($values);
            default:
                return null;
        }
    }

    /**
     * @param string $ability
     * @param $value
     * @return array
     */
    public static function getEffectAbilities(string $ability, $value): array
    {
        $abilities = [];
        if ($ability !== "Effect") {
            return $abilities;
        }
        foreach ($value as $effect) {
            foreach ($effect as $effectName => $level) {
                $effectInstance = new EffectInstance(Effect::getEffectByName($effectName), 999999, $level - 1);
                $abilities[] = new EffectAbility($effectInstance);
            }
        }
        return $abilities;
    }
}