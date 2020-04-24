<?php

namespace Heisenburger69\BurgerCustomArmor\Commands;

use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat as C;

class CustomArmorCommand extends PluginCommand
{
    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("customarmor", $plugin);
        $this->setUsage("/customarmor <SetName> <piece> <player>");
        $this->setAliases(["burgercustomarmor", "bca"]);
        $this->setDescription("BurgerCustomArmor Base Command");
        $this->setPermission("burgercustomarmor.command");
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("burgercustomarmor.command")) {
            $sender->sendMessage(Main::PREFIX . C::DARK_RED . "Insufficient Permission.");
            return;
        }
        if (count($args) !== 3) {
            $sender->sendMessage(Main::PREFIX . C::RED . $this->getUsage());
            return;
        }
        if (!isset($this->plugin->customSets[$args[0]])) {
            $sender->sendMessage(Main::PREFIX . C::RED . "The given Armor Set does not exist.");
            return;
        }
        $armorSet = $this->plugin->customSets[$args[0]];
        if (!$armorSet instanceof CustomArmorSet) {
            $sender->sendMessage(Main::PREFIX . C::RED . "The given Armor Set does not exist.");
            return;
        }
        $playerName = $args[2];
        if (($player = $this->plugin->getServer()->getPlayerExact($playerName)) === null) {
            $sender->sendMessage(Main::PREFIX . C::RED . "The given player is offline!");
            return;
        }
        switch ($args[1]) {
            case "full":
                $items = $armorSet->getSetPieces();
                foreach ($items as $item) {
                    $player->getInventory()->addItem($item);
                }
                $sender->sendMessage(Main::PREFIX . C::GREEN . "Successfully gave all pieces of Custom Armor Set " . C::AQUA . $armorSet->getName() . C::GREEN . " to player " . C::AQUA . $playerName);
                return;
            case "helmet":
                $item = $armorSet->getHelmet();
                break;
            case "chestplate":
                $item = $armorSet->getChestplate();
                break;
            case "leggings":
                $item = $armorSet->getLeggings();
                break;
            case "boots":
                $item = $armorSet->getBoots();
                break;
            default:
                $sender->sendMessage(C::RED . "Do either helmet/chestplate/leggings/boots or full to give the full set");
                return;
        }
        $player->getInventory()->addItem($item);
        $sender->sendMessage(Main::PREFIX . C::GREEN . "Successfully gave " . C::AQUA . $args[1] . C::GREEN . " of Custom Armor Set " . C::AQUA . $armorSet->getName() . C::GREEN . " to player " . C::AQUA . $playerName);
    }
}