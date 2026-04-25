<?php

declare(strict_types=1);

namespace Nuh\AchievementX\listener;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use Nuh\AchievementX\Main;

class AchievementListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Detect wood mining for "Getting Wood" achievement
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        // Check for wood logs
        $woodBlocks = [
            BlockTypeIds::OAK_LOG,
            BlockTypeIds::SPRUCE_LOG,
            BlockTypeIds::BIRCH_LOG,
            BlockTypeIds::JUNGLE_LOG,
            BlockTypeIds::ACACIA_LOG,
            BlockTypeIds::DARK_OAK_LOG,
        ];

        if (in_array($block->getTypeId(), $woodBlocks, true)) {
            $this->tryAward($player, "mineWood");
        }
    }

    /**
     * Detect crafting events for crafting-based achievements
     */
    public function onCraft(CraftItemEvent $event): void {
        $player = $event->getPlayer();
        $outputs = $event->getOutputs();

        foreach ($outputs as $item) {
            match ($item->getTypeId()) {
                ItemTypeIds::CRAFTING_TABLE   => $this->tryAward($player, "buildWorkBench"),
                ItemTypeIds::WOODEN_PICKAXE,
                ItemTypeIds::STONE_PICKAXE    => $this->tryAward($player, "buildPickaxe"),
                ItemTypeIds::FURNACE          => $this->tryAward($player, "buildFurnace"),
                ItemTypeIds::STONE_PICKAXE,
                ItemTypeIds::IRON_PICKAXE     => $this->tryAward($player, "buildBetterPickaxe"),
                ItemTypeIds::WOODEN_HOE,
                ItemTypeIds::STONE_HOE,
                ItemTypeIds::IRON_HOE         => $this->tryAward($player, "buildHoe"),
                ItemTypeIds::WOODEN_SWORD,
                ItemTypeIds::STONE_SWORD,
                ItemTypeIds::IRON_SWORD       => $this->tryAward($player, "buildSword"),
                ItemTypeIds::BREAD            => $this->tryAward($player, "makeBread"),
                ItemTypeIds::CAKE             => $this->tryAward($player, "bakeCake"),
                default                       => null,
            };
        }
    }

    private function tryAward(Player $player, string $achievementId): void {
        $this->plugin->getAchievementManager()->award($player, $achievementId);
    }
}

