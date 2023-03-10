<?php
$sql3 = new \SQLite3('test3.db');

$sql3->close();

rename("test3.db", "test4.db");

