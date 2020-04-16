<?php
    require_once("../database.php");

    //var_dump($_POST);

    echo(Team::register($_POST));
?>