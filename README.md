AchievementX

AchievementX is a lightweight and customizable achievement system plugin for PocketMine-MP (API 5). It adds Minecraft-style achievements with progression requirements, automatic detection, and command management.

---

✨ Features

- 🎯 Built-in achievement progression system (with dependencies)
- 📦 Automatic detection:
  - Block breaking (e.g. mining wood)
  - Crafting items
- 📢 Configurable broadcast system
- ⚙️ Simple API to add custom achievements
- 🛠️ Admin commands to manage achievements
- 📊 Per-player achievement tracking (in-memory)

---

📦 Installation

1. Download the plugin ".phar"
2. Place it in your "/plugins/" folder
3. Start or restart your server

---

⚙️ Configuration

"config.yml"

# Whether to announce achievements to all players
# Set to false to only show achievement messages to the player who earned it
announce-player-achievements: true

---

🎮 Commands

"/achievement"

Manage achievements

Usage:

/achievement <give|list> [player] [achievementId]

Subcommands:

- "give <player> <achievementId>"
  - Give an achievement to a player (admin only)
- "list"
  - Show all available achievements
- "list <player>"
  - Show achievements earned by a player

---

🔐 Permissions

Permission| Description| Default
"achievement.command"| Allows use of achievement commands| OP
"achievement.give"| Allows giving achievements to players| OP

---

🏆 Default Achievements

ID| Name| Requires
mineWood| Getting Wood| -
buildWorkBench| Benchmarking| mineWood
buildPickaxe| Time to Mine!| buildWorkBench
buildFurnace| Hot Topic| buildPickaxe
acquireIron| Acquire Hardware| buildFurnace
buildHoe| Time to Farm!| buildWorkBench
makeBread| Bake Bread| buildHoe
bakeCake| The Lie| buildHoe
buildBetterPickaxe| Getting an Upgrade| buildPickaxe
buildSword| Time to Strike!| buildWorkBench
diamonds| DIAMONDS!| acquireIron

---

🔄 How It Works

- Achievements are defined with:
  - "name"
  - "requires" (dependency list)
- Player must complete required achievements before unlocking the next
- Events automatically trigger:
  - Breaking logs → "mineWood"
  - Crafting items → multiple achievements

---

🧠 Developer API

Get Manager

$manager = Main::getInstance()->getAchievementManager();

Add Custom Achievement

$manager->add("myAchievement", "My Custom Achievement", ["mineWood"]);

Award Achievement

$manager->award($player, "myAchievement");

---

📢 Broadcast Behavior

- If enabled → message sent to all players
- If disabled → only sent to the player

Example:

PlayerName has just earned the achievement [Getting Wood]

---

⚠️ Notes

- Achievements are stored in memory (not persistent)
- Restarting the server will reset all player progress
- You can extend this plugin to add database support if needed

---

👤 Author

Nuh

---

📄 License

This plugin is open-source and free to use under GPL-3.0.

---

🚀 Poggit

Ready for publishing on Poggit:

- Includes "plugin.yml"
- Compatible with PocketMine-MP API 5

---

💡 Future Improvements (Optional)

- Save achievements to database / YAML
- Add GUI menu
- Add achievement rewards (money/items)
- Add toast notifications (Bedrock style)

---
