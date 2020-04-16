<?php
    require_once("../database.php");

    $mysql = get_mysql();

    if ($_POST["operation"] == "openended") {
        $result = $mysql->query("update teams set openended=" . $_POST["value"] . " where id = " . $mysql->escape_string($_POST["team"]));
    }

    else if ($_POST["operation"] == "correct") {
        $result = $mysql->query("update submissions set result='" . $_POST["value"] . "' where id = " . $mysql->escape_string($_POST["id"]));
    }

    else if ($_POST["operation"] == "remove") {
        $result = $mysql->query("delete from submissions where id = " . $mysql->escape_string($_POST["id"]));
    }

    else if ($_POST["operation"] == "add") {
        $result = Submission::create($_POST["value"], $_POST["team"], -1, "manual", "100");
        echo($result == -1 ? "0" : "1");
        return;
    }
    
    echo($mysql->errno == 0 ? "1" : "0");
?>