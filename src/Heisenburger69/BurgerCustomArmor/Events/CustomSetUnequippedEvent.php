<?php

namespace Heisenburger69\BurgerCustomArmor\Events;

use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Event;
use pocketmine\Player;
use pocketmine\Server;

class CustomSetUnequippedEvent extends Event
{

    /**
     * @var CustomArmorSet
     */
    private $armorSet;

    /**
     * @var Player
     */
    private $player;

    public function __construct(Player $player, CustomArmorSet $armorSet)
    {
        $this->player = $player;
        $this->armorSet = $armorSet;
    }

    /**
     * @return CustomArmorSet
     */
    public function getArmorSet(): CustomArmorSet
    {
        return $this->armorSet;
    }

    public function call(): void
    {
        foreach ($this->armorSet->getUnequippedCommands() as $command) {
            $command = str_replace("{PLAYER}", $this->player->getName(), $command);
            Server::getInstance()->getCommandMap()->dispatch(new ConsoleCommandSender(), $command);
        }
        foreach ($this->armorSet->getUnequippedMessages() as $msg) {
            $msg = str_replace("{PLAYER}", $this->player->getName(), $msg);
            $this->player->sendMessage($msg);
        }
        parent::call();
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}