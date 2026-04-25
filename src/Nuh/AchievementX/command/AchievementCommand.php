<?php

declare(strict_types=1);

namespace Nuh\AchievementX\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Nuh\AchievementX\Main;

class AchievementCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct(
            "achievement",
            "Manage player achievements",
            "/achievement <give|list> [player] [achievementId]",
            ["ach"]
        );
        $this->plugin = $plugin;
        $this->setPermission("achievement.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (empty($args)) {
            $sender->sendMessage(TextFormat::RED . "Usage: " . $this->getUsage());
            return false;
        }

        $subCommand = strtolower($args[0]);

        switch ($subCommand) {
            case "give":
                return $this->handleGive($sender, $args);
            case "list":
                return $this->handleList($sender, $args);
            default:
                $sender->sendMessage(TextFormat::RED . "Unknown sub-command. Usage: " . $this->getUsage());
                return false;
        }
    }

    private function handleGive(CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission("achievement.give")) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to give achievements.");
            return false;
        }

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::RED . "Usage: /achievement give <player> <achievementId>");
            return false;
        }

        $targetName = $args[1];
        $achievementId = $args[2];

        $target = $this->plugin->getServer()->getPlayerByPrefix($targetName);
        if ($target === null) {
            $sender->sendMessage(TextFormat::RED . "Player '$targetName' not found.");
            return false;
        }

        $manager = $this->plugin->getAchievementManager();

        if (!$manager->has($achievementId)) {
            $sender->sendMessage(TextFormat::RED . "Achievement '$achievementId' does not exist.");
            $sender->sendMessage(TextFormat::YELLOW . "Available achievements: " .
                implode(", ", array_keys($manager->getList())));
            return false;
        }

        if ($manager->playerHas($target, $achievementId)) {
            $sender->sendMessage(TextFormat::YELLOW . $target->getName() .
                " already has achievement '$achievementId'.");
            return false;
        }

        // Force award (bypass requirements check for admin command)
        $manager->broadcast($target, $achievementId);
        $sender->sendMessage(TextFormat::GREEN . "Achievement '$achievementId' given to " .
            $target->getName() . ".");

        return true;
    }

    private function handleList(CommandSender $sender, array $args): bool {
        $manager = $this->plugin->getAchievementManager();

        // List all achievements
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::GOLD . "=== All Achievements ===");
            foreach ($manager->getList() as $id => $data) {
                $reqs = empty($data["requires"]) ? "none" : implode(", ", $data["requires"]);
                $sender->sendMessage(TextFormat::YELLOW . $id . TextFormat::WHITE .
                    " - " . $data["name"] . TextFormat::GRAY . " (requires: " . $reqs . ")");
            }
            return true;
        }

        // List a specific player's achievements
        $targetName = $args[1];
        $target = $this->plugin->getServer()->getPlayerByPrefix($targetName);

        if ($target === null) {
            $sender->sendMessage(TextFormat::RED . "Player '$targetName' not found.");
            return false;
        }

        $earned = $manager->getPlayerAchievements($target);
        if (empty($earned)) {
            $sender->sendMessage(TextFormat::YELLOW . $target->getName() .
                " has not earned any achievements yet.");
            return true;
        }

        $sender->sendMessage(TextFormat::GOLD . "=== " . $target->getName() . "'s Achievements ===");
        foreach ($earned as $id) {
            $data = $manager->get($id);
            if ($data !== null) {
                $sender->sendMessage(TextFormat::GREEN . "✔ " . TextFormat::WHITE . $data["name"] .
                    TextFormat::GRAY . " (" . $id . ")");
            }
        }

        return true;
    }
}

