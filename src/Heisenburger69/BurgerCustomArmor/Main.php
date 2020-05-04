<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerCustomArmor;

use Heisenburger69\BurgerCustomArmor\Abilities\AbilityUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\ArmorSetUtils;
use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use Heisenburger69\BurgerCustomArmor\Commands\CustomArmorCommand;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Color;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use Heisenburger69\BurgerCustomArmor\Pocketmine\{
    Chain\ChainBoots,
    Chain\ChainChestplate,
    Chain\ChainHelmet,
    Chain\ChainLeggings,
    Diamond\DiamondBoots,
    Diamond\DiamondChestplate,
    Diamond\DiamondHelmet,
    Diamond\DiamondLeggings,
    Gold\GoldBoots,
    Gold\GoldChestplate,
    Gold\GoldHelmet,
    Gold\GoldLeggings,
    Iron\IronBoots,
    Iron\IronChestplate,
    Iron\IronHelmet,
    Iron\IronLeggings,
    Leather\LeatherBoots,
    Leather\LeatherCap,
    Leather\LeatherPants,
    Leather\LeatherTunic
};

class Main extends PluginBase
{
    /** @var string */
    public const PREFIX = C::BOLD . C::AQUA . "Burger" . C::LIGHT_PURPLE . "CustomArmor" . "> " . C::RESET;

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
    /**
     * @var Config
     */
    private $craftingRecipes;

    public function onEnable()
    {
        self::$instance = $this;

        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig();
        $this->saveResource("armorsets.yml");
        $this->saveResource("recipes.yml");
        $this->saveResource("FireCape.png");
        $this->armorSets = new Config($this->getDataFolder() . "armorsets.yml");
        $this->craftingRecipes = new Config($this->getDataFolder() . "recipes.yml");

        $this->registerCustomItems();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->registerArmorSets();
        $this->registerRecipes();
        $this->getServer()->getCommandMap()->register("BurgerCustomArmor", new CustomArmorCommand($this));
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
            isset($properties["durability"]) ? $properties["durability"] : [],
            $properties["name"],
            $properties["lore"],
            $properties["setbonuslore"],
            isset($properties["equipped-commands"]) ? $properties["equipped-commands"] : [],
            isset($properties["unequipped-commands"]) ? $properties["unequipped-commands"] : [],
            isset($properties["equipped-messages"]) ? $properties["equipped-messages"] : [],
            isset($properties["unequipped-messages"]) ? $properties["unequipped-messages"] : [],
        );

        $this->using[$name] = [];
    }

    private function registerCustomItems(): void
    {
        $items = [
            new LeatherCap(),
            new LeatherTunic(),
            new LeatherPants(),
            new LeatherBoots(),

            new ChainHelmet(),
            new ChainChestplate(),
            new ChainLeggings(),
            new ChainBoots(),

            new GoldHelmet(),
            new GoldChestplate(),
            new GoldLeggings(),
            new GoldBoots(),

            new IronHelmet(),
            new IronChestplate(),
            new IronLeggings(),
            new IronBoots(),

            new DiamondHelmet(),
            new DiamondChestplate(),
            new DiamondLeggings(),
            new DiamondBoots(),
        ];
        foreach ($items as $item) {
            ItemFactory::registerItem($item, true);
        }
    }

    /**
     * Definitely not stolen from PiggyBackpacks :)
     * thx pig <3
     */
    private function registerRecipes(): void
    {
        foreach ($this->craftingRecipes->getAll() as $name => $recipeData) {
            $customArmor = explode("-", $name);
            $setName = $customArmor[0];
            $setPiece = $customArmor[1];

            $item = ItemFactory::fromString($setPiece);
            $item->setNamedTagEntry(new StringTag("burgercustomarmor", $setName));
            $item->setCustomName(C::RESET . C::BOLD . $setName . C::RESET . " " .$item->getName());

            $requiredItems = [];
            foreach ($recipeData["materials"] as $materialSymbol => $materialData) {
                $requiredItems[$materialSymbol] = Item::get($materialData["id"], $materialData["meta"], $materialData["count"]);
            }
            $this->getServer()->getCraftingManager()->registerRecipe(new ShapedRecipe($recipeData["shape"], $requiredItems, [$item]));
        }
        $this->getServer()->getCraftingManager()->buildCraftingDataCache();
    }

}
