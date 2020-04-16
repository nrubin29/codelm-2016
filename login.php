<?php
    require_once('Auth/Yubico.php');
    require_once("database.php");
    require_once("status.php");

    session_start();

    # Generate a new id+key from https://upgrade.yubico.com/getapikey
    $yubi = new Auth_Yubico("25610", "1iD+1pDHFV+DpejPh+9/iBYvPHQ=");
    $auth = $yubi->verify($_POST["password"]);

    if ($_POST["password"] == "bradley" || (!PEAR::isError($auth) && $yubi->_id == "25610")) {
        $_SESSION["judge"] = "true";
        echo("-3");
    }

    else {
        $team = Team::login($_POST["password"]);

        if ($team != -1) {
            if ((new Team($team))->special == false && !$status["login"]) {
                echo("-2");
                return;
            }

            else if ($status["ended"]) {
                echo("-4");
                return;
            }

            else {
                $_SESSION["team"] = $team;
            }
        }

        echo($team);
    }
?>