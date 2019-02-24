<?php

namespace redstone\blocks;

use pocketmine\level\Level;

use pocketmine\math\Vector3;


use redstone\utils\Facing;
use redstone\utils\RedstoneUtils;

trait RedstoneTrait {

    public $level;
    
    public function updateAroundRedstone(Vector3 $pos, ?int $face = null) : void {
        $direction = Facing::ALL;
        for ($i = 0; $i < count($direction); ++$i) {
            if ($face != null && $face == $direction[$i]) {
                continue;
            }

            $block = $this->level->getBlock($pos->getSide($direction[$i]));
            if ($block instanceof IRedstone) {
                $block->onRedstoneUpdate();
            }
        }
    }

    public function getRedstonePower(Vector3 $pos, int $face) : int {
        $block = $this->level->getBlock($pos);
        return RedstoneUtils::isNormalBlock($block) ? $this->getStrongPowered($pos) : RedstoneUtils::getWeakPower($block, $face);
    }

    public function isBlockPowered(Vector3 $pos) : bool {
        $direction = Facing::ALL;
        for ($i = 0; $i < count($direction); ++$i) {
            $face = $direction[$i];
            if ($this->getRedstonePower($pos->getSide($face), $face) > 0) {
                return true;
            }
        }
        return false;
    }

    public function isSidePowered(Vector3 $pos, int $face) : bool {
        return $this->getRedstonePower($pos->getSide($face), $face) > 0;
    }

    public function getStrongPowered(Vector3 $pos) : int {
        $power = 0;
        $direction = Facing::ALL;
        for ($i = 0; $i < count($direction); ++$i) {
            $face = $direction[$i];
            $power = max($power, $this->getSideStrongPowered($pos->getSide($face), $face));

            if ($power >= 15) {
                return $power;
            }
        }
        return $power;
    }

    public function getSideStrongPowered(Vector3 $pos, int $face) : int {
        $block = $this->level->getBlock($pos);
        return RedstoneUtils::getStrongPower($block, $face);
    }
}