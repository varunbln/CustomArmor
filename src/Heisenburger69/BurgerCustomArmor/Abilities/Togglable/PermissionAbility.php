<?php

namespace Heisenburger69\BurgerCustomArmor\Abilities\Togglable;

use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\Player;

class PermissionAbility extends TogglableAbility
{
    /**
     * @var string
     */
    private $permission;

    public function __construct(string $permission)
    {
        $this->permission = $permission;
    }

    public function on(Player $player)
    {
        $player->addAttachment(Main::$instance, $this->permission, true);
    }

    public function off(Player $player)
    {
        $player->addAttachment(Main::$instance, $this->permission, false);
    }
}