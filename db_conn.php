<?php

try {
    $db = new PDO('mysql:host=localhost;dbname=shopping', 'root', '123');
} catch (\PDOException $th) {
    echo $th->getMessage();
}

?>x`