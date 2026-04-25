<?php

declare(strict_types=1);

namespace Nuh\AchievementX;

use pocketmine\plugin\PluginBase;
use Nuh\AchievementX\command\AchievementCommand;
use Nuh\AchievementX\listener\AchievementListener;

class Main extends PluginBase {

    private static Main $instance;
    private AchievementManager $achievementManager;

    public function onLoad(): void {
        self::$instance = $this;
    }

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->achievementManager = new AchievementManager($this);

        $this->getServer()->getPluginManager()->registerEvents(
            new AchievementListener($this),
            $this
        );

        $this->getServer()->getCommandMap()->register(
            "achievement",
            new AchievementCommand($this)
        );

    }

    public static function getInstance(): Main {
        return self::$instance;
    }

    public function getAchievementManager(): AchievementManager {
        return $this->achievementManager;
    }
}
