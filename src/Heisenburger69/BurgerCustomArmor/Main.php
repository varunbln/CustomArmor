<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\AbilityUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\ArmorSetUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use pocketmine\item\ChainBoots;
use pocketmine\item\ChainChestplate;
use pocketmine\item\ChainHelmet;
use pocketmine\item\ChainLeggings;
use pocketmine\item\DiamondBoots;
use pocketmine\item\DiamondChestplate;
use pocketmine\item\DiamondHelmet;
use pocketmine\item\DiamondLeggings;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldChestplate;
use pocketmine\item\GoldHelmet;
use pocketmine\item\GoldLeggings;
use pocketmine\item\IronBoots;
use pocketmine\item\IronChestplate;
use pocketmine\item\IronHelmet;
use pocketmine\item\IronLeggings;
use pocketmine\item\Item;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\LeatherPants;
use pocketmine\item\LeatherTunic;
use pocketmine\level\Level;
use pocketmine\Player;
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

    /**
     * @param Level $level
     * @return bool
     */
    public function checkProtectionLevel(Level $level): bool
    {
        $blacklist = $this->getConfig()->get("enable-world-blacklist");
        $whitelist = $this->getConfig()->get("enable-world-whitelist");
        $levelName = $level->getName();

        if ($blacklist === $whitelist) return true;

        if ($blacklist) {
            $disallowedWorlds = $this->getConfig()->get("blacklisted-worlds");
            if (in_array($levelName, $disallowedWorlds)) return false;
            return true;
        }

        if ($whitelist) {
            $allowedWorlds = $this->getConfig()->get("whitelisted-worlds");
            if (in_array($levelName, $allowedWorlds)) return true;
            return false;
        }

        return false;
    }

    /**
     * @param Player $player
     * @param Item $item
     * @param string $setName
     */
    public function addUsingSet(Player $player, Item $item, string $setName): void
    {
        $playerName = $player->getName();
        if (!isset($this->using[$setName][$playerName])) {
            $this->initPlayer($playerName, $setName);
        }

        if ($this->isHelmet($item)) {
            $this->using[$setName][$playerName]["helmet"] = true;
        } elseif ($this->isChestplate($item)) {
            $this->using[$setName][$playerName]["chestplate"] = true;
        } elseif ($this->isLeggings($item)) {
            $this->using[$setName][$playerName]["leggings"] = true;
        } elseif ($this->isBoots($item)) {
            $this->using[$setName][$playerName]["boots"] = true;
        }
    }

    private function initPlayer(string $playerName, string $setName): void
    {
        $this->using[$setName][$playerName] =
            [
                "helmet" => false,
                "chestplate" => false,
                "leggings" => false,
                "boots" => false,
            ];
    }

    public function isHelmet(Item $item): bool
    {
        if (
            $item instanceof DiamondHelmet ||
            $item instanceof GoldHelmet ||
            $item instanceof IronHelmet ||
            $item instanceof ChainHelmet ||
            $item instanceof LeatherCap) {
            return true;
        }
        return false;
    }

    public function isChestplate(Item $item): bool
    {
        if (
            $item instanceof DiamondChestplate ||
            $item instanceof IronChestplate ||
            $item instanceof GoldChestplate ||
            $item instanceof ChainChestplate ||
            $item instanceof LeatherTunic) {
            return true;
        }
        return false;
    }

    public function isLeggings(Item $item): bool
    {
        if ($item instanceof DiamondLeggings ||
            $item instanceof GoldLeggings ||
            $item instanceof IronLeggings ||
            $item instanceof ChainLeggings ||
            $item instanceof LeatherPants) {
            return true;
        }
        return false;
    }

    public function isBoots(Item $item): bool
    {
        if ($item instanceof DiamondBoots ||
            $item instanceof GoldBoots ||
            $item instanceof IronBoots ||
            $item instanceof ChainBoots ||
            $item instanceof LeatherBoots) {
            return true;
        }
        return false;
    }

    public function canUseSet(Player $player, $setName): bool
    {
        $playerName = $player->getName();
        if(
            $this->using[$setName][$playerName]["helmet"] === true &&
            $this->using[$setName][$playerName]["chestplate"] === true &&
            $this->using[$setName][$playerName]["leggings"] === true &&
            $this->using[$setName][$playerName]["boots"] === true
        ) {
            return true;
        }
        return false;
    }


}
