<?php
/**
 * This file is part of PROJECT_NAME
 *
 * @author Arisify
 * @link   https://github.com/Arisify
 * @license https://opensource.org/licenses/MIT MIT License
 *
 * •.,¸,.•*`•.,¸¸,.•*¯ ╭━━━━━━━━━━━━╮
 * •.,¸,.•*¯`•.,¸,.•*¯.|:::::::/\___/\
 * •.,¸,.•*¯`•.,¸,.•* <|:::::::(｡ ●ω●｡)
 * •.,¸,.•¯•.,¸,.•╰ *  し------し---Ｊ
 *
 */
declare(strict_types=1);

try {
    $db = new SQLite3("test.db");
    echo "Opened database successfully\n";

    $query = <<<QUERY
        CREATE TABLE IF NOT EXISTS PLAYERS (
            name VARCHAR(64) NOT NULL,
            balance INTEGER DEFAULT 0
        );
        INSERT or REPLACE into PLAYERS VALUES ("naaame", 1);
    QUERY;
    $db->query($query);
    var_dump($db->query("SELECT * FROM PLAYERS;")->fetchArray(SQLITE3_ASSOC));

    // INSERT INTO players (name, balance)
    // VALUES ("cat", 12);

    // INSERT INTO COMPANY VALUES (7, 'James', 24, 'Houston', 10000.00 );

    var_dump (($db->query($query))->fetchArray(SQLITE3_ASSOC));
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
    echo "Operation done successfully\n";
    $db->close();

} catch (Throwable $e) {
    echo $e->getMessage();
}