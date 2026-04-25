<?php

declare(strict_types=1);

namespace Nuh\Achievement;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AchievementManager {

    private Main $plugin;

    /**
     * @var array<string, array{name: string, requires: list<string>}>
     */
    private array $list = [
        "mineWood" => [
            "name" => "Getting Wood",
            "requires" => []
        ],
        "buildWorkBench" => [
            "name" => "Benchmarking",
            "requires" => ["mineWood"]
        ],
        "buildPickaxe" => [
            "name" => "Time to Mine!",
            "requires" => ["buildWorkBench"]
        ],
        "buildFurnace" => [
            "name" => "Hot Topic",
            "requires" => ["buildPickaxe"]
        ],
        "acquireIron" => [
            "name" => "Acquire hardware",
            "requires" => ["buildFurnace"]
        ],
        "buildHoe" => [
            "name" => "Time to Farm!",
            "requires" => ["buildWorkBench"]
        ],
        "makeBread" => [
            "name" => "Bake Bread",
            "requires" => ["buildHoe"]
        ],
        "bakeCake" => [
            "name" => "The Lie",
            "requires" => ["buildHoe"]
        ],
        "buildBetterPickaxe" => [
            "name" => "Getting an Upgrade",
            "requires" => ["buildPickaxe"]
        ],
        "buildSword" => [
            "name" => "Time to Strike!",
            "requires" => ["buildWorkBench"]
        ],
        "diamonds" => [
            "name" => "DIAMONDS!",
            "requires" => ["acquireIron"]
        ]
    ];

    /**
     * Tracks which achievements each player has earned
     * @var array<string, list<string>>
     */
    private array $playerAchievements = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Get all achievements
     * @return array<string, array{name: string, requires: list<string>}>
     */
    public function getList(): array {
        return $this->list;
    }

    /**
     * Check if achievement ID exists
     */
    public function has(string $achievementId): bool {
        return isset($this->list[$achievementId]);
    }

    /**
     * Get achievement data by ID
     * @return array{name: string, requires: list<string>}|null
     */
    public function get(string $achievementId): ?array {
        return $this->list[$achievementId] ?? null;
    }

    /**
     * Check if a player has a specific achievement
     */
    public function playerHas(Player $player, string $achievementId): bool {
        $name = $player->getName();
        return isset($this->playerAchievements[$name]) &&
               in_array($achievementId, $this->playerAchievements[$name], true);
    }

    /**
     * Check if player meets requirements for an achievement
     */
    public function meetsRequirements(Player $player, string $achievementId): bool {
        if (!$this->has($achievementId)) {
            return false;
        }

        $requires = $this->list[$achievementId]["requires"];
        foreach ($requires as $req) {
            if (!$this->playerHas($player, $req)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Award an achievement to a player and broadcast it
     */
    public function award(Player $player, string $achievementId): bool {
        if (!$this->has($achievementId)) {
            return false;
        }

        if ($this->playerHas($player, $achievementId)) {
            return false; // Already has it
        }

        if (!$this->meetsRequirements($player, $achievementId)) {
            return false; // Missing requirements
        }

        // Grant the achievement
        $name = $player->getName();
        $this->playerAchievements[$name][] = $achievementId;

        // Broadcast
        $this->broadcast($player, $achievementId);

        return true;
    }

    /**
     * Broadcast achievement unlock message
     */
    public function broadcast(Player $player, string $achievementId): bool {
        if (!$this->has($achievementId)) {
            return false;
        }

        $achievementName = TextFormat::GREEN . $this->list[$achievementId]["name"] . TextFormat::RESET;
        $message = TextFormat::YELLOW . $player->getDisplayName() .
                   TextFormat::WHITE . " has just earned the achievement " .
                   "[" . $achievementName . TextFormat::WHITE . "]";

        $announceAchievements = $this->plugin->getConfig()->get("announce-player-achievements", true);

        if ($announceAchievements) {
            $this->plugin->getServer()->broadcastMessage($message);
        } else {
            $player->sendMessage($message);
        }

        return true;
    }

    /**
     * Add a custom achievement
     * @param string[] $requires
     */
    public function add(string $achievementId, string $achievementName, array $requires = []): bool {
        if (isset($this->list[$achievementId])) {
            return false; // Already exists
        }

        $this->list[$achievementId] = [
            "name" => $achievementName,
            "requires" => $requires
        ];

        return true;
    }

    /**
     * Get all achievements a player has earned
     * @return list<string>
     */
    public function getPlayerAchievements(Player $player): array {
        return $this->playerAchievements[$player->getName()] ?? [];
    }
}

