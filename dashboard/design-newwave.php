<html>
    <head>
        <title>New Wave Employees</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Dashboard">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/codemirror.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

        <style>
            .CodeMirror {
                border: 1px solid #eee;
                border-top-width: 0;
                height: 300px;
                cursor: text;
            }

            body {
                padding-bottom: 20px;
            }
            
            .card-header {
                cursor: hand;
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .card-header a {
                color: black;
                text-decoration: none;
            }

            /* New Wave */
            body {
                position: relative;
                padding:0 0 0 120px;
            }

            .container {
                margin: 0 0 5px 0;
            }

            .navbar {
                width: 100px;
                height: 100vh;
                margin-right: 0;
                margin-left: 0;
                float: left;
                display: block;

                position: absolute;
                top:0; bottom:0; left:0;
            }

            .nav-stacked .nav-item {
                display: block;
                float: none;
                margin-top: 0 !important;
                margin-left: 0 !important;
                margin-bottom: 1rem !important;
                text-align: center;
                /*border: 2px solid white;
                border-radius: 100px;*/
            }
        </style>
    </head>

    <body>
        <?php
            session_start();
            require_once("../database.php");

            if (isset($_SESSION["team"])) {
                $team = new Team($_SESSION["team"]);
            }

            else {
                header("Location: index.php");
            }
        ?>

        <nav class="navbar navbar-dark bg-primary" style="border-radius: 0 !important; display: block; margin: 0 auto">
            <!--<a class="navbar-brand" href="#">New Wave</a>-->
            <img src="../img/icon_32x32@2x.png" style="margin-bottom: 1rem">
            <ul class="nav navbar-nav nav-stacked" role="tablist">
                <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($i == 0) echo "active" ?>" style="font-size: 2rem" href="#p<?php echo $problem->id ?>" role="tab" data-toggle="tab" data-tooltip="tooltip" data-placement="right" title="<?php echo $problem->name ?>"><?php echo $problem->id ?></a>
                    </li>
                <?php $i++; } ?>
                <!--<li class="navbar-divider"></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Documentation</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="//docs.oracle.com/javase/8/docs/api/" target="_blank">Java</a>
                        <a class="dropdown-item" href="//docs.python.org/3.5/" target="_blank">Python</a>
                        <a class="dropdown-item" href="//www.cplusplus.com/reference/" target="_blank">C++</a>
                    </div>
                </li>-->
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-toggle="popover" data-placement="right" data-title="Team <?php echo $team->id ?>" data-content="Division: <?php echo $team->divisionString ?><br>Score: <?php echo $team->get_score() ?><br>Problems Solved: <?php echo $team->num_solved(); ?>/<?php echo sizeof(Problem::all($team)); ?>" data-container="body" data-html="true" data-trigger="hover"><i class="fa fa-users" style="font-size: 2rem"></i></a>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link active" href="#">Team <?php echo $team->id; ?>: <?php echo $team->divisionString ?> Division</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="score">Score: <?php echo $team->get_score(); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#"><span id="solved"><?php echo $team->num_solved(); ?></span>/<?php echo sizeof(Problem::all($team)); ?> Problems Solved</a>
                </li>-->
                <!--<li class="nav-item">
                    <a class="nav-link active" href="#" id="countdown"></a>
                </li>-->
            </ul>
        </nav>

        <div class="container">
            <br>

            <div class="tab-content">
                <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                    <div role="tabpanel" class="tab-pane fade in <?php if ($i == 0) echo "active" ?>" id="p<?php echo $problem->id ?>">
                        <div class="page-header">
                            <h1><small><?php echo $problem->id ?>.</small> <?php echo $problem->name ?></h1>
                        </div>

                        <br>

                        <div class="alert alert-info">
                            <button type="button" class="alertButton close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p class="alertText"></p>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <h4 class="card-header"><a href="#">Question</a></h4>
                                    <div class="card-block"><?php echo $problem->question ?></div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="card">
                                    <h4 class="card-header"><a href="#">Sample Data</a></h4>
                                    <div class="card-block">
                                        <em>Explanations to certain answers are provided in the question packet.</em>
                                        <br><br>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Input</th>
                                                <th>Correct Output</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($problem->sample as $input => $output) { ?>
                                                    <tr>
                                                        <td><?php echo $input ?></td>
                                                        <td><?php echo $output ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="post" action="submit.php" class="submit">
                            <div class="form-group">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a href="#" class="java-tab-button nav-link active" data-tab="java-tab" role="tab" data-toggle="tab">Java</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="python-tab-button nav-link" data-tab="python-tab" role="tab" data-toggle="tab">Python</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="cpp-tab-button nav-link" data-tab="cpp-tab" role="tab" data-toggle="tab">C++</a>
                                    </li>
                                    <li class="nav-item pull-right">
                                        <a href="//docs.oracle.com/javase/8/docs/api/" target="_blank" class="documentation nav-link">Documentation</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="java-tab tab-pane active">
                                        <textarea rows="10" name="java" class="java code"><?php echo $problem->java["stub"] ?></textarea>
                                    </div>
                                    <div role="tabpanel" class="python-tab tab-pane">
                                        <textarea rows="10" name="python" class="python code"><?php echo $problem->python["stub"] ?></textarea>
                                    </div>
                                    <div role="tabpanel" class="cpp-tab tab-pane">
                                        <textarea rows="10" name="cpp" class="cpp code"><?php echo $problem->cpp["stub"] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="problem" value="<?php echo $problem->id ?>">
                            <input type="hidden" name="language" value="0">
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-lg pull-right" <?php if ($team->is_solved($problem->id)) echo ' value="Solved" disabled' ?>>
                            </div>
                        </form>
                        
                        <br><br><br>
                        
                        <div class="card">
                            <h4 class="card-header"><a href="#">Submissions</a></h4>
                            <div class="card-block">
                                <ul class="submissions list-group">
                                    <?php foreach (Submission::for_team_problem($team->id, $problem->id) as $submission) { ?>
                                        <li class="list-group-item"><?php echo $submission->date ?> &bull; <?php echo $submission->language ?> &bull; <?php echo $submission->result . (is_numeric($submission->result) ?  "%" : "") ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php $i++; } ?>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/tether.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/codemirror.js"></script>
        <script src="../js/clike.js"></script>
        <script src="../js/python.js"></script>
        <!--<script src="//raw.githubusercontent.com/mckamey/countdownjs/master/countdown.min.js"></script>-->
        <script>
            $(document).ready(function() {
                $(".java").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-java", matchBrackets: true, lines: 5 }));
                });
                
                $(".python").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-python", lines: 5 }));
                });
                
                $(".cpp").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-c++src", lines: 5 }));
                });

                $(".alert").hide();
                
                $(".card-block").hide();
                
                $('[data-tooltip="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();

                $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                    $(e.target).parent().parent().parent().parent().find(".code").each(function() {
                        $(this).data("CodeMirrorInstance").refresh();
                    });
                });
                
                $(".java-tab-button").click(function() {
                    $(this).parent().parent().parent().find(".java-tab").show();
                    $(this).parent().parent().parent().find(".python-tab").hide();
                    $(this).parent().parent().parent().find(".cpp-tab").hide();
                    $(this).parent().parent().parent().parent().find('[name="language"]').val(0);
                    $(this).parent().parent().find(".documentation").prop("href", "//docs.oracle.com/javase/8/docs/api/");
                });
                
                $(".python-tab-button").click(function() {
                    $(this).parent().parent().parent().find(".java-tab").hide();
                    $(this).parent().parent().parent().find(".python-tab").show();
                    $(this).parent().parent().parent().find(".cpp-tab").hide();
                    $(this).parent().parent().parent().parent().find('[name="language"]').val(1);
                    $(this).parent().parent().find(".documentation").prop("href", "//docs.python.org/2.7/");
                });
                
                $(".cpp-tab-button").click(function() {
                    $(this).parent().parent().parent().find(".java-tab").hide();
                    $(this).parent().parent().parent().find(".python-tab").hide();
                    $(this).parent().parent().parent().find(".cpp-tab").show();
                    $(this).parent().parent().parent().parent().find('[name="language"]').val(2);
                    $(this).parent().parent().find(".documentation").prop("href", "//www.cplusplus.com/reference/");
                });

                $(".alertButton").click(function() {
                    $(this).parent().parent().find(".alert").fadeOut();
                });

                $(".card-header").click(function() {
                    $(this).parent().find(".card-block").slideToggle();
                });

                /*
                //var end = new Date(2016, 11, 1, 10, 30);
                var end = new Date(2014, 0, 1);
                var cd = countdown(function(time) {
                    if (end.getTime() <= new Date().getTime()) {
                        $("#countdown").text("Finished");
                        window.clearInterval(cd);
                    }

                    else {
                        $("#countdown").text(time.toString() + " remaining.");
                    }
                }, end, countdown.DEFAULTS);
                */

                window.onbeforeunload = function() {
                    return "Your progress will be saved, but any code you have written will be deleted.";
                };

                $(".submit").submit(function(event) {
                    event.preventDefault();

                    var form = this;
                    var alertText = $(this).parent().find(".alertText");

                    $(this).find('input[type="submit"]').prop("disabled", true);
                    alertText.text("Submitting...");
                    $(this).parent().find(".alert").fadeIn();

                    $.post("submit.php", $(this).serialize(), function(jsonRaw) {
                        var json;

                        try {
                            json = JSON.parse(jsonRaw);
                        }

                        catch (err) {
                            alertText.html('<span style="color: red">An internal error occurred. Please tell a judge.</span>');
                            $(form).find('input[type="submit"]').prop("disabled", false);
                            return;
                        }

                        alertText.html(isNormalInteger(String(json["result"])) ? json["message"] : '<span style="color: red">' + json["message"] + '</span>');

                        if (json["result"] == 100) {
                            $(form).find('input[type="submit"]').prop("value", "Solved");
                        }

                        else {
                            $(form).find('input[type="submit"]').prop("disabled", false);
                        }

                        $("#score").text("Score: " + json["score"]);
                        $("#solved").text(json["solved"]);

                        $(form).parent().find(".submissions").empty();
                        $.each(json["submissions"], function(i, submission) {
                            $(form).parent().find(".submissions").append('<li class="list-group-item">' + submission["date"] + ' &bull; ' + submission["language"] + ' &bull; ' + submission["result"] + (isNormalInteger(submission["result"]) ? "%" : "") + '</li>');
                        });
                    });
                });
            });
            
            function isNormalInteger(str) {
                var n = ~~Number(str);
                return String(n) === str && n >= 0;
            }
        </script>
    </body>
</html>