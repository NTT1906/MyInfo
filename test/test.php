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

    $query = <<<QUERY
        CREATE TABLE IF NOT EXISTS PLAYERS (
            name VARCHAR(64) PRIMARY KEY NOT NULL ,
            balance INTEGER DEFAULT 0
        );
        INSERT or REPLACE into PLAYERS VALUES ("nasaame", 144);
    QUERY;
    $db->query($query);

    $query = <<<QUERYR
        REPLACE or INSERT into PLAYERS VALUES ("nasaasáame", 14423);
    QUERYR;
    var_dump($db->query($query)->fetchArray());

    $data = array();

    $result = $db->query('SELECT * FROM PLAYERS');
    var_dump($result->fetchArray());

    while ($row = $db->query("SELECT * FROM PLAYERS;")->fetchArray()) {
        $data[] = $row;
        echo json_encode($row) . PHP_EOL;
        sleep(5);
   }
    //echo json_encode($data, JSON_THROW_ON_ERROR);

    echo "Operation done successfully\n";
    $db->close();
    //var_dump($db->query("")->fetchArray(SQLITE3_ASSOC));

    // INSERT INTO players (name, balance)
    // VALUES ("cat", 12);

    // INSERT INTO COMPANY VALUES (7, 'James', 24, 'Houston', 10000.00 );
    //
    //$sql = <<<UPDA TE
      //UPDATE players set name = Hi;
    //UPD ATE  ;
    //$db->exec($sql);


    /*$sql = <<<EOF
      UPDATE COMPANY set SALARY = 25000.00 where ID=1;
EOF;
    $ret = $db->exec($sql);
    if (!$ret) {
        echo $db->lastErrorMsg();
    } else {
        echo $db->changes(), " Record updated successfully\n";
    }

    $sql = <<<EOF
      SELECT * from COMPANY;
EOF;
    $ret = $db->query($sql);
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
        echo "ID = " . $row['ID'] . "\n";
        echo "NAME = " . $row['NAME'] . "\n";
        echo "ADDRESS = " . $row['ADDRESS'] . "\n";
        echo "SALARY =  " . $row['SALARY'] . "\n\n";
    }*/

} catch (Throwable $e) {
    echo $e->getMessage();
}