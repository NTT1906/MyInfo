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

use pocketmine\world\format\io\leveldb\LevelDB;

try {
    $db = new SQLite3("test.db");
    echo "Opened database successfully\n";

    $i = 0;

    $query1 = <<<QUERY1
        CREATE TABLE IF NOT EXISTS PLAYERS (
            name VARCHAR(64) PRIMARY KEY NOT NULL,
            balance INTEGER DEFAULT 0
        );
    QUERY1;
    $db->query($query1);

    $query2 = <<<QUERY2
        INSERT OR REPLACE INTO PLAYERS VALUES ('Arie1906', 1);
        INSERT OR REPLACE INTO PLAYERS VALUES ('Eira6091', 12);
        INSERT OR REPLACE INTO PLAYERS VALUES ('Raei0619', 123);
    QUERY2;
    $db->query($query2);

    $query3 = <<< QUERY3
        SELECT * FROM PLAYERS;
    QUERY3;

    $query4 = <<<QUERY4
        UPDATE PLAYERS SET balance = 100 WHERE name='Arie1906';
    QUERY4;

    $query5 = <<<QUERY5
        DELETE FROM PLAYERS WHERE name='Raei0619';
    QUERY5;

    $db->query($query4);
    //$db->query($query5);


    $ret = $db->query($query3);

    $array = [1];
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        $name = $row['name'];
        unset($row['name']);
        var_dump($row);
    }
    var_dump($array);

    /*while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        echo "NAME = " . $row['name'] . "\n";
        echo "BALANCE =  " . $row['balance'] . "\n\n";
    }*/
    echo "Operation done successfully\n";
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}