<?php
    /*
        This file is in charge of running submitted code and reporting the response. The response is represented as follows:
        array(
            "id"            => The id of the problem.
            "result"        => "The result. Could be an error message or the percent correct.",
            "message"       => "A message explaining the result.",
            "score"         => "The score after the submission was added.",
            "solved"        => "The number of problems solved.",
            "submissions"   => array(
                array(
                    "date"      => "The datetime of the submission.",
                    "language"  => "The language of the submission.",
                    "result"    => "The response. Could be an error message or the percent correct."
                );
            );
        );
    */

    set_time_limit(10);

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

    $debug = false;

    if ($status["debug"] && $team->special == 1) {
        $debug = true;
        echo("Debugging is enabled.<br>");
    }

    if ($team->is_solved($problem->id)) {
        $_POST["language"] = -1;
        result($team, $problem, "aleadysolved", "alreadysolved");
        return;
    }

    $name = md5(rand());
    while (is_numeric(substr($name, 0, 1))) {
        $name = substr($name, 1);
    }
    
    if (!$status["submit"]) {
        result($team, $problem, $name, "closed");
        return;
    }
    
    if ($_POST["language"] == 0) {
        $path = "../submissions/$name.java";
        $file = fopen($path, "w"); // If there's an error, mkdir submissions and chmod it to 077.
        $code = strtr("import java.util.ArrayList;import java.util.List;import java.util.Collections;import java.util.Arrays;import java.util.regex.Matcher;import java.util.regex.Pattern;import java.util.stream.Collectors;public class %name%{public static void main(String[]args){%tests%}%code%}", array("%name%" => $name, "%tests%" => $problem->java["tests"], "%code%" => $_POST["java"]));
        $code = str_replace("\\n", "\n", $code);
        $code = str_replace("\\t", "\t", $code);
        fwrite($file, $code);
        fclose($file);
    
        $ignore = array();
        $ret = null;
        exec("cd ../submissions && javac $name.java", $ignore, $ret);
        if ($ret != null && $ret != 0) {
            if ($debug) {
                var_dump($ignore);
            }

            result($team, $problem, $name, "compilation");
            return;
        }
        
        $output = array();
        exec("cd ../submissions && timeout 10 java $name", $output, $ret);
        
        $result = "success";
        
        if ($ret != null && $ret != 0) {
            if ($ret == 1) {
                $result = "error";
            }
            
            else if ($ret == 124) {
                $result = "timeout";
            }
            
            else {
                $result = "return";
            }
        }
        
        if ($result == "success") {
            if (sizeof($output) > sizeof($problem->correct)) {
                if ($debug) {
                    echo(sizeof($output) . " outputs vs " . sizeof($problem->correct) . " correct.");
                }

                $result = "toomuch";
            }
            
            else if (sizeof($output) < sizeof($problem->correct)) {
                $result = "toofew";
            }
        }
    
        if ($result != "success") {
            result($team, $problem, $name, $result);
            return;
        }
    }
    
    else if ($_POST["language"] == 1) {
        $path = "../submissions/$name.py";
        $file = fopen($path, "w"); // If there's an error, mkdir submissions and chmod it to 077.
        $code = strtr("%code%\n%tests%", array("%tests%" => $problem->python["tests"], "%code%" => $_POST["python"]));
        $code = str_replace("\\n", "\n", $code);
        $code = str_replace("\\t", "\t", $code);
        fwrite($file, $code);
        fclose($file);

        $ignore = array();
        $ret = null;
        exec("cd ../submissions && python -m py_compile $name.py", $ignore, $ret);
        if ($ret != null && $ret != 0) {
            result($team, $problem, $name, "compilation");
            return;
        }

        $output = array();
        exec("cd ../submissions && timeout 10 python $name.py", $output, $ret);
        
        $result = "success";
        
        if ($ret != null && $ret != 0) {
            if ($ret == 1) {
                $result = "error";
            }
            
            else if ($ret == 124) {
                $result = "timeout";
            }
            
            else {
                $result = "return";
            }
        }
        
        if ($result == "success") {
            if (sizeof($output) > sizeof($problem->correct)) {
                $result = "toomuch";
            }
            
            else if (sizeof($output) < sizeof($problem->correct)) {
                $result = "toofew";
            }
        }
    
        if ($result != "success") {
            result($team, $problem, $name, $result);
            return;
        }
    }
    
    else if ($_POST["language"] == 2) {
        $path = "../submissions/$name.cpp";
        $file = fopen($path, "w"); // If there's an error, mkdir submissions and chmod it to 077.
        $code = strtr("#include <iostream>\n#include <string>\nusing namespace std;\n\n%code%\n\nint main() {cout.setf(std::ios::boolalpha);%tests%}", array("%tests%" => $problem->cpp["tests"], "%code%" => $_POST["cpp"]));
        $code = str_replace("\\n", "\n", $code);
        $code = str_replace("\\t", "\t", $code);
        fwrite($file, $code);
        fclose($file);
    
        $ignore = array();
        $ret = null;
        exec("cd ../submissions && g++ -o $name $name.c", $ignore, $ret);
        if ($ret != null && $ret != 0) {
            if ($debug) {
                var_dump($ignore);
            }

            result($team, $problem, $name, "compilation");
            return;
        }
        
        $output = array();
        exec("cd ../submissions && timeout 10 ./$name", $output, $ret);
        
        $result = "success";
        
        if ($ret != null && $ret != 0) {
            if ($ret == 1) {
                $result = "error";
            }
            
            else if ($ret == 124) {
                $result = "timeout";
            }
            
            else {
                $result = "return";
            }
        }
        
        if ($result == "success") {
            if (sizeof($output) > sizeof($problem->correct)) {
                $result = "toomuch";
            }
            
            else if (sizeof($output) < sizeof($problem->correct)) {
                $result = "toofew";
            }
        }
    
        if ($result != "success") {
            result($team, $problem, $name, $result);
            return;
        }
    }
    
    else {
        result($team, $problem, $name, "language");
        return;
    }
    
    $percent = 0;

    if ($problem->sort_output) {
        sort($output, SORT_STRING);
        sort($problem->correct, SORT_STRING);
    }
        
    for ($i = 0; $i < sizeof($problem->correct); $i++) {
        $correct = $problem->correct[$i];

        if (!$problem->case_sensitive) {
            $output[$i] = strtolower($output[$i]);

            if (is_array($correct)) {
                foreach ($correct as $value) {
                    $value = strtolower($value);
                }
            }

            else {
                $correct = strtolower($correct);
            }
        }

        if ($problem->truncate_numbers) {
            $output[$i] = floor(intval($output[$i]));
        }

        if (is_array($correct)) {
            if ($debug) {
                echo("Comparing array correct=" . json_encode($correct) . " to output=" . $output[$i] . ". Result: " . (in_array($output[$i], $correct) ? "true" : "false") . "<br>");
            }

            if (in_array($output[$i], $correct)) {
                $percent += 1;
            }
        }

        else {
            if ($debug) {
                echo("Comparing correct=" . $correct . " to output=" . $output[$i] . ". Result: " . ($correct == $output[$i] ? "true" : "false") . "<br>");
            }

            if ($correct == $output[$i]) {
                $percent += 1;
            }
        }
    }
        
    $percent /= sizeof($problem->correct);
    $percent *= 100;
    $percent = round($percent, 2);
        
    result($team, $problem, $name, $percent);
    
    function result($team, $problem, $name, $result) {
        if ($_POST["language"] != -1) {
            Submission::create($problem->id, $team->id, $_POST["language"], $name, $result);
        }
        
        $submissions = array();
        foreach (Submission::for_team_problem($team->id, $problem->id) as $submission) {
            array_push($submissions, array(
                "date"      => $submission->date,
                "language"  => $submission->language,
                "result"    => $submission->result
            ));
        }

        if (is_numeric($result)) {
            $message = "You were correct for $result% of tests.";
        }

        else if ($result == "alreadysolved") {
            $message = "This problem was already solved.";
        }

        else if ($result == "closed") {
            $message = "The competition is over. You may no longer submit answers.";
        }

        else if ($result == "compilation") {
            $message = "Your answer did not compile. Please ensure it is valid code and runs without compilation errors.";
        }

        else if ($result == "error") {
            $message = "Your answer resulted in an error. Please ensure it runs correctly.";
        }

        else if ($result == "timeout") {
            $message = "Your answer took too long to run. Please ensure that there are no infinite loops and long recursion.";
        }

        else if ($result == "return") {
            $message = "Your answer resulted in an unknown process return value. Please tell a judge.";
        }

        else if ($result == "toomuch") {
            $message = "Your answer gave too many outputs. If you are solving a problem that does not require printing, please ensure that you have no extraneous print statements.";
        }

        else if ($result == "toofew") {
            $message = "Your answer did not give enough outputs. If you are solving a problem that does not require printing, this is probably an error. Please tell a judge.";
        }

        else {
            $message = "Unknown result: $result";
        }
        
        echo(json_encode(array(
            "id"            => $problem->id,
            "result"        => $result,
            "message"       => $message,
            "score"         => $team == null ? 0 : $team->get_score(),
            "solved"        => $team == null ? 0 : $team->num_solved(),
            "submissions"   => $submissions
        )));
    }
?>