<?php
/**
 * This file is part of FakePlayer
 *
 * @author Arie
 * @link   https://github.com/Arie
 * @license https://opensource.org/licenses/MIT MIT License
 *
 * •.,¸,.•*`•.,¸¸,.•*¯ ╭━━━━━━━━━━━━╮
 * •.,¸,.•*¯`•.,¸,.•*¯.|:::::::/\___/\
 * •.,¸,.•*¯`•.,¸,.•* <|:::::::(｡ ●ω●｡)
 * •.,¸,.•¯•.,¸,.•╰ *  し------し---Ｊ
 *
 */
declare(strict_types=1);

namespace arie\myinfo;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use SQLite3;
use SQLite3Stmt;

class Loader extends PluginBase{
    use SingletonTrait;

    protected SQLite3 $database;
    protected SQLite3Stmt|false $prepare;

    public function onLoad() : void{
        self::setInstance($this);
        foreach ($this->getResources() as $resource) {
            $this->saveResource($resource->getFilename());
        }

        $this->saveDefaultConfig();
        try {
            if(file_exists($this->getDataFolder() . "players.db")) {
                $this->database = new SQLite3($this->getDataFolder() . "players.db", SQLITE3_OPEN_READWRITE);
            } else {
                $this->database = new SQLite3($this->getDataFolder() . "players.db", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
            }
            $this->prepare = $this->database->prepare("CREATE TABLE IF NOT EXISTS Player (Id INTEGER AUTO_INCREMENT PRIMARY KEY);");
            $this->prepare->execute();
            $this->getLogger()->info("[SQLite] System working YEEAAAAH");
        } catch (\Throwable $e) {
            $this->getLogger()->info("[SQLite] Oh no! system failed");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onEnable() : void{
        $manager = $this->getServer()->getPluginManager();
        $manager->registerEvent(PlayerPreLoginEvent::class, static function(PlayerPreLoginEvent $ev) : void{
            $info = $ev->getPlayerInfo();
            var_dump($ev->getIp());
            var_dump($ev->getPort());
            var_dump($ev->getPlayerInfo()->getExtraData()["ServerAddress"]);
            //var_dump($info->getLocale());
            //var_dump($info->getUsername());
            //var_dump($info->getUuid());
            //var_dump($info->getExtraData());
            // DeviceId, DeviceModel, DeviceOS, GameVersion, GuiScale, LanguageCode, ServerAddress, ThirdPartyName
        }, EventPriority::MONITOR, $this);
    }

    /**public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if ($command->getName() === "myinfo") {
            if (!$sender instanceof Player) {
                $this->getLogger()->notice("Please run this command in-game.");
                return false;
            }

        }
        return true;
    }*/

    public function onDisable() : void{
        if(isset($this->database)) {
            $this->database->close();
        }
    }
}