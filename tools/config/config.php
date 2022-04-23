<?php

// RDM DB
define('DB_HOST', getenv("DB_HOST") ?: "db");
define('DB_USER', getenv("DB_USER") ?: "root");
define('DB_PSWD', getenv("DB_PSWD") ?: "madwho?");
define('DB_NAME', getenv("DB_NAME") ?: "rdmdb");
define('DB_PORT', getenv("DB_PORT") ?: 3306);

// ManualDB (for nests)
define('MDB_ACTIVE', getenv("MDB_ACTIVE") ?: false);
define('MDB_HOST', getenv("MDB_HOST") ?: "127.0.0.1");
define('MDB_USER', getenv("MDB_HOST") ?: "mdbuser");
define('MDB_PSWD', getenv("MDB_HOST") ?: "password");
define('MDB_NAME', getenv("MDB_HOST") ?: "manualdb");
define('MDB_PORT', getenv("MDB_HOST") ?: 3306);

// own Tileserver
define('OWN_TS', getenv("OWN_TS") ?: "https://IP:PORT/tile/STYLE/{z}/{x}/{y}/1/png");

?>
