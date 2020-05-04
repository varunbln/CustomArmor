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
                return new ScaleAbility((float)$value);
            case "Permission":
                return new PermissionAbility((string)$value);
            case "Knockback":
                return new KnockbackNegationAbility((float)$value);
            case "DamageNegation":
                return new DamageNegationAbility((float)$value);
            case "SwordNegation":
                return new SwordNegationAbility((float)$value);
            case "AxeNegation":
                return new AxeNegationAbility((float)$value);
            case "BowNegation":
                return new BowNegationAbility((float)$value);
            case "DamageAmplification":
                return new DamageAmplificationAbility((float)$value);
            case "SwordAmplification":
                return new SwordAmplificationAbility((float)$value);
            case "AxeAmplification":
                return new AxeAmplificationAbility((float)$value);
            case "BowAmplification":
                return new BowAmplificationAbility((float)$value);
            case "Cape":
                if (!extension_loaded("gd")) {
                    Server::getInstance()->getLogger()->info(Main::PREFIX . ": gd extension missing! Cape Ability Disabled.");
                    return null;
                }
                return new CapeAbility((string)$value);
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
                $effectInstance = new EffectInstance(Effect::getEffectByName($effectName), 999999, $level - 1, Main::$instance->getConfig()->get("show-effect-particles"));
                $abilities[] = new EffectAbility($effectInstance);
            }
        }
        return $abilities;
    }
}