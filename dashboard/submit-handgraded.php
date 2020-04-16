<?php
    /*
        This file is in charge of uploading submitted code. The response is represented as follows:
        array(
            "result"        => "The result. true or an error.",
            "message"       => "A message explaining the result.",
            "solved"        => "The number of problems solved."
        );
    */

    require_once("../database.php");
    require_once("../status.php");

    session_start();
    Problem::setup();
    $problem = Problem::$all[strval($_POST["problem"])];

    if (isset($_SESSION["team"])) {
        $team = new Team($_SESSION["team"]);
    }

    else if (isset($_POST["team"])) {
        $team = new Team($_POST["team"]);
    }

    else {
        return;
    }
    
    if (!$status["submit"]) {
        result($team, "closed");
        return;
    }

    $target_dir = "../submissions/team" . $team->id;

    if (!file_exists($target_dir)) {
        mkdir($target_dir);
    }

    if (count(scandir($target_dir)) != 2) {
        result($team, "alreadysubmitted");
        return;
    }

    $success = true;

    for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {
        if (!move_uploaded_file($_FILES["files"]["tmp_name"][$i], "$target_dir/" . $_FILES["files"]["name"][$i])) {
            $success = false;
            break;
        }
    }

    if (!$success) {
        foreach (array_diff(scandir($target_dir), array(".", "..")) as $file) {
            unlink($file);
        }
    }

    if ($success) {
        result($team, "success");
    }

    else {
        result($team, "failure");
    }
    
    function result($team, $result) {
        if ($result == "closed") {
            $message = "The competition is over. You may no longer submit answers.";
        }

        else if ($result == "alreadysubmitted") {
            $message = "You have already submitted something for this problem.";
        }

        else if ($result == "success") {
            $message = "Successfully uploaded files. Your program will be graded after the competition ends.";
        }

        else if ($result == "failed") {
            $message = "Upload failed. Please tell a judge.";
        }

        else {
            $message = "Unknown result: $result";
        }
        
        echo(json_encode(array(
            "result"        => $result,
            "message"       => $message,
            "solved"        => $team == null ? 0 : $team->num_solved(),
        )));
    }
?>