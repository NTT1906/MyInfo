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


class MyDB extends SQLite3{
    function __construct(){
        $this->open('test.db');
    }
}

$db = new MyDB();
if (!$db) {
    echo $db->lastErrorMsg();
} else {
    echo "Opened database successfully\n";
}
