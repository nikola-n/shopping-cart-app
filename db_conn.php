<?php

try {
    $db = new PDO('mysql:host=localhost;dbname=shopping', 'root', 'root');
} catch (\PDOException $th) {
    echo $th->getMessage();
}
