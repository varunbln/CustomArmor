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
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\CapeAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\EffectAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\PermissionAbility;
use Heisenburger69\BurgerCustomArmor\Abilities\Togglable\ScaleAbility;
use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Server;

class AbilityUtils
{

    /**
     * @param string $ability
     * @param mixed $value
     * @return ArmorAbility|null
     */
    public static function getAbility(string $ability, $value): ?ArmorAbility
    {
        switch ($ability) {
            case "Scale":
                return new ScaleAbility($value);
            case "Permission":
                return new PermissionAbility($value);
            case "Knockback":
                return new KnockbackNegationAbility($value);
            case "DamageNegation":
                return new DamageNegationAbility($value);
            case "SwordNegation":
                return new SwordNegationAbility($value);
            case "AxeNegation":
                return new AxeNegationAbility($value);
            case "BowNegation":
                return new BowNegationAbility($value);
            case "DamageAmplification":
                return new DamageAmplificationAbility($value);
            case "SwordAmplification":
                return new SwordAmplificationAbility($value);
            case "AxeAmplification":
                return new AxeAmplificationAbility($value);
            case "BowAmplification":
                return new BowAmplificationAbility($value);
            case "Cape":
                if (!extension_loaded("gd")) {
                    Server::getInstance()->getLogger()->info(Main::PREFIX . ": gd extension missing! Cape Ability Disabled.");
                    return null;
                }
                return new CapeAbility($value);
            default:
                return null;
        }
    }

    /**
     * @param string $ability
     * @param array $value
     * @return array
     */
    public static function getEffectAbilities(string $ability, array $value): array
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