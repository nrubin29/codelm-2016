<?php
    require_once("status.php");

    if (!$status["register"]) {
        echo("-2");
        return;
    }

    require_once("database.php");
    session_start();

    $team = Team::register_practice($_POST);

    if ($team != -1) {
        $_SESSION["team"] = $team;
    }

    echo($team);
?>