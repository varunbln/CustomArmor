<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\AbilityUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\ArmorSetUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Color;
use pocketmine\utils\Config;

class Main extends PluginBase
{
    /**
     * @var Main
     */
    public static $instance;
    /**
     * @var Config
     */
    private $armorSets;
    /**
     * @var Config
     */
    private $cfg;
    /**
     * @var CustomArmorSet[]
     */
    public $customSets;

    /**
     * @var array
     */
    public $using;

    public function onEnable()
    {
        self::$instance = $this;

        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig();
        $this->saveResource("armorsets.yml");
        $this->armorSets = new Config($this->getDataFolder() . "armorsets.yml");

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->registerArmorSets();
    }

    public function registerArmorSets(): void
    {
        foreach ($this->armorSets->getAll() as $name => $properties) {
            $this->registerArmorSet($name, $properties);
        }
    }

    /**
     * @param string $name
     * @param array $properties
     */
    public function registerArmorSet(string $name, array $properties): void
    {
        $tier = ArmorSetUtils::getTierFromName($properties["tier"]);

        $color = new Color(0, 0, 0);
        if (isset($properties["color"])) {
            $color = new Color($properties["color"]["r"], $properties["color"]["g"], $properties["color"]["b"]);
        }

        $abilities = [];
        if (is_array($properties["abilities"]) && count($properties["abilities"]) > 0) {
            foreach ($properties["abilities"] as $ability => $value) {
                if ($ability === "Effect") {
                    $abilities = array_merge($abilities, AbilityUtils::getEffectAbilities($ability, $value));
                    continue;
                }
                if (($armorAbility = AbilityUtils::getAbility($ability, $value)) !== null) {
                    $abilities[] = $armorAbility;
                }
            }
        }

        $this->customSets[$name] = new CustomArmorSet(
            $name,
            $tier,
            $properties["glint"],
            $abilities,
            $color,
            $properties["strength"],
            $properties["name"],
            $properties["lore"],
            $properties["setbonuslore"]
        );

        $this->using[$name] = [];
    }
}
