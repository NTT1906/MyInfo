<?php
/**
 * This file is part of MyInfo
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

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use SQLite3;

final class Loader extends PluginBase{
    protected const SQL_PLAYERS_NAME = 'Name';
    protected const SQL_PLAYERS_IP = 'Ip';
    protected const SQL_PLAYERS_PORT = 'Port';
    protected const SQL_PLAYERS_LOCALE = 'Locale';
    protected const SQL_PLAYERS_UUID = 'Uuid';
    protected const SQL_PLAYERS_DEVICE_ID = 'DeviceId';
    protected const SQL_PLAYERS_DEVICE_MODEL = 'DeviceModel';
    protected const SQL_PLAYERS_DEVICE_OS = 'DeviceOS';
    protected const SQL_PLAYERS_GAME_VERSION = 'GameVersion';
    protected const SQL_PLAYERS_LANGUAGE_CODE = 'LanguageCode';

    use SingletonTrait;

    private array $data = [];
    protected string $dbpath;
    private bool $update = false;
    private array $cache = [];

    public function onLoad() : void{
        self::setInstance($this);
        foreach ($this->getResources() as $resource) {
            $this->saveResource($resource->getFilename());
        }
        $this->saveDefaultConfig();

        $this->dbpath = $this->getDataFolder() . "players.db";
        $this->loadDb($this->data);
        //var_dump($this->data);
    }

    private function loadDb(&$array = []) : array{
        $db = new SQLite3($this->dbpath);
        $query1 = <<<QUERY1
            CREATE TABLE IF NOT EXISTS PLAYERS (
                Name VARCHAR(64) PRIMARY KEY NOT NULL,
                Ip VARCHAR(64),
                Port INT,
                Locale TEXT,
                Uuid VARCHAR(32),
                DeviceId VARCHAR(32),
                DeviceModel TEXT,
                DeviceOS INT,
                GameVersion VARCHAR(64),
                LanguageCode TEXT
            );
        QUERY1;
        $db->query($query1);

        $query2 = <<< QUERY2
            SELECT * FROM PLAYERS;
        QUERY2;
        $ret = $db->query($query2);

        while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            $name = $row[self::SQL_PLAYERS_NAME];
            //unset($row[self::SQL_PLAYERS_NAME]);
            $array[$name] = $row;
        }
        return $array;
    }

    private function updateDb(string $playerName, &$db = null) : bool{
        if (empty($playerName) || !isset($this->data[$playerName])) {
            return false;
        }
        $db ??= new SQLite3($this->dbpath);
        $data = $this->data[$playerName];
        //$query = "INSERT OR REPLACE INTO PLAYERS " . $this->createSQLValues($data); It worked! :)
        $query = "INSERT OR REPLACE INTO PLAYERS VALUES (" .
            "'" . $data[self::SQL_PLAYERS_NAME] . "'," .
            "'" . $data[self::SQL_PLAYERS_IP] . "'," .
            $data[self::SQL_PLAYERS_PORT] . "," .
            "'" . $data[self::SQL_PLAYERS_LOCALE] . "'," .
            "'" . $data[self::SQL_PLAYERS_UUID] . "'," .
            "'" . $data[self::SQL_PLAYERS_DEVICE_ID] . "'," .
            "'" . $data[self::SQL_PLAYERS_DEVICE_MODEL] . "'," .
            $data[self::SQL_PLAYERS_DEVICE_OS] . "," .
            "'" . $data[self::SQL_PLAYERS_GAME_VERSION] . "'," .
            "'" . $data[self::SQL_PLAYERS_LANGUAGE_CODE] . "');";
        $db->query($query);
        $db->close();
        return true;
    }

    private function saveDb() : void{
        $db = new SQLite3($this->dbpath);
        foreach (array_keys($this->data) as $playerName) {
            $this->updateDb($playerName, $db);
        }
        $db->close();
    }

    private function deleteDb(string $playerName) : bool{ //TODO: More work, not done yet
        if (empty($playerName) || !isset($this->data[$playerName])) {
            return false;
        }
        unset($this->cache[$playerName]);
        $db = new SQLite3($this->dbpath);
        $query = "DELETE FROM PLAYERS WHERE name=" . "'" . $playerName . "'";
        $db->close();
        return true;
    }

    /**
     * Create a string like this, made for not manually spamming "."
     * VALUES ("data1", "data2", 1, 131);
     *
     * @param $values
     *
     * @return string|null
     */
    private function createSQLValues($values) : ?string{
        try {
            return "VALUES (" . str_replace(["[", "]"], "", json_encode(array_values($values), JSON_THROW_ON_ERROR)) . ");";
        } catch (JsonException $e) {
            $this->getLogger()->error($e->getMessage());
            return null;
        }
        /* $s = "";
        foreach ($values as $v) {
            $s .= match (gettype($v)) {
                'integer' => $v,
                default => "'" . $v . "'"
            } . ", ";
        }
        return $s;
        */
    }

    public function onEnable() : void{
        $manager = $this->getServer()->getPluginManager();

        $manager->registerEvent(PlayerPreLoginEvent::class, function(PlayerPreLoginEvent $ev) : void{
            $info = $ev->getPlayerInfo();
            $name = $info->getUsername();
            $xdata = $info->getExtraData();
            //var_dump($xdata);

            $data = [
                self::SQL_PLAYERS_NAME => $name,
                self::SQL_PLAYERS_IP => $ev->getIp(),
                self::SQL_PLAYERS_PORT => $ev->getPort(),
                self::SQL_PLAYERS_LOCALE => $info->getLocale(),
                self::SQL_PLAYERS_UUID => $info->getUuid(),
                self::SQL_PLAYERS_DEVICE_ID => $xdata[self::SQL_PLAYERS_DEVICE_ID],
                self::SQL_PLAYERS_DEVICE_MODEL => $xdata[self::SQL_PLAYERS_DEVICE_MODEL],
                self::SQL_PLAYERS_DEVICE_OS => $xdata[self::SQL_PLAYERS_DEVICE_OS],
                self::SQL_PLAYERS_GAME_VERSION => $xdata[self::SQL_PLAYERS_GAME_VERSION],
                self::SQL_PLAYERS_LANGUAGE_CODE => $xdata[self::SQL_PLAYERS_LANGUAGE_CODE]
            ];
            $this->data[$name] = $data;
        }, EventPriority::MONITOR, $this);
    }
    
    public function sendForm(Player $player, string $target) : void{
        $player->sendForm(new CustomForm(
            "MyInfo",
            [
                new Label("test", $this->genInfo($target))
            ],
            function(Player $submitter, CustomFormResponse $response) : void{
                $this->getServer()->broadcastMessage(TextFormat::GREEN . $submitter->getName() . " submitted custom form with values: " . print_r($response, true));
            },
            function(Player $submitter) : void{
                $this->getServer()->broadcastMessage(TextFormat::YELLOW . $submitter->getName() . " closed the form :(");
            }
        ));
    }

    private function genInfo(string $playerName) : ?string{
        if (empty($playerName) || !isset($this->data[$playerName])) {
            return null;
        }
        $s = "";
        foreach ($this->data[$playerName] as $i => $v) {
            if ($i === self::SQL_PLAYERS_DEVICE_OS) {
                $v = match($v) {
                    DeviceOS::UNKNOWN => "N/A",
                    DeviceOS::ANDROID => "Android",
                    DeviceOS::IOS => "IOS",
                    DeviceOS::OSX => "OSX",
                    DeviceOS::AMAZON => "AMAZON",
                    DeviceOS::GEAR_VR => "Gear VR",
                    DeviceOS::HOLOLENS => "Hololens",
                    DeviceOS::WINDOWS_10 => "Windows 10",
                    DeviceOS::WIN32 => "Win 32",
                    DeviceOS::DEDICATED => "Dedicated",
                    DeviceOS::TVOS => "TVOS",
                    DeviceOS::PLAYSTATION => "PlayStation",
                    DeviceOS::NINTENDO => "Nintendo",
                    DeviceOS::XBOX => "Xbox",
                    DeviceOS::WINDOWS_PHONE => "Window Phone",
                };
            }
            $s .= $i . ":  " . $v . TextFormat::EOL;
        }
        return $s;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if ($command->getName() === "myinfo") {
            if (!$sender instanceof Player) {
                if (!empty($args) && isset($args[0])) {
                    $r = $this->genInfo($args[0]);
                    if ($r === null) {
                        $this->getLogger()->notice("Player not found!");
                    } else {
                        foreach (explode(TextFormat::EOL, $r) as $l) {
                            $this->getLogger()->info($l);
                        }
                    }
                    return true;
                }
                $this->getLogger()->notice("Missing player name, try: /myinfo <playerName>");
                return false;
            }
            if (empty($args) || !isset($args[0])) {
                $this->sendForm($sender, $sender->getName());
                return true;
            }
            if (isset($this->data[$args[0]])) {
                $this->sendForm($sender, $args[0]);
                return true;
            }
            $sender->sendMessage("Player not found!");
        }
        return true;
    }

    /**
     * @return string
     */
    public function getDbpath() : string{
        return $this->dbpath;
    }

    public function onDisable() : void{
        $this->saveDb();
    }
}