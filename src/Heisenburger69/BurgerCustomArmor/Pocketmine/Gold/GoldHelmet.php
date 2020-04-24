<?php

namespace Heisenburger69\BurgerCustomArmor\Pocketmine\Gold;

use Heisenburger69\BurgerCustomArmor\ArmorSets\CustomArmorSet;
use Heisenburger69\BurgerCustomArmor\Main;
use pocketmine\item\GoldHelmet as PmItem;

class GoldHelmet extends PmItem
{
    /** @var float */
    protected $metaFloat = 0.0;

    public function __construct(int $meta = 0)
    {
        parent::__construct($meta);
    }

    public function getMaxDurability(): int
    {
        if (($nbt = $this->getNamedTagEntry("burgercustomarmor")) !== null) {
            $setName = $nbt->getValue();
            $armorSet = Main::$instance->customSets[$setName];
            if ($armorSet instanceof CustomArmorSet) {
                return isset($armorSet->durabilities["chestplate"]) ? $armorSet->durabilities["chestplate"] : parent::getMaxDurability();
            }
        }
        return parent::getMaxDurability();
    }

    public function applyDamage(int $amount): bool
    {
        if ($this->isUnbreakable() or $this->isBroken()) {
            return false;
        }

        $amount -= $this->getUnbreakingDamageReduction($amount);
        $factor = $this->getMaxDurability() / parent::getMaxDurability();
        $this->metaFloat = ($this->metaFloat + ($amount / $factor));
        $this->meta = min((int)round($this->metaFloat), parent::getMaxDurability());
        if ($this->isBroken()) {
            $this->onBroken();
        }

        return true;
    }
}