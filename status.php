<?php
    $status = array(
        "login" => false,        // Teams can log in.
        "register" => false,     // Teams can register for practice.
        "submit" => false,       // Teams can submit.
        "ended" => true,       // The competition has ended.
        "debug" => true,       // Debugging is shown for special accounts.
    );

    if (isset($_GET["stat"])) {
        echo($status[$_GET["stat"]]);
    }