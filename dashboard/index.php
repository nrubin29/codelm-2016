<?php session_start(); ?>
<html>
    <head>
        <title>CodeLM 2016 Dashboard</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Dashboard">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/codemirror.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/list-group-item-styles.css">
      
        <style>
            .CodeMirror {
                border: 1px solid #eee;
                border-top-width: 0;
                height: 300px;
                cursor: text;
            }

            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
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

            body {
                padding-bottom: 20px;
            }

            /*a[data-toggle="popover"] {
                padding-bottom: 0 !important;
                border-bottom: 2px dotted black;
            }*/

            .nav-pills > li {
                float: none !important;
                display: inline-block !important;
            }

            .nav-pills {
                text-align: center;
            }

            .modal {
                text-align: center;
            }

            @media screen and (min-width: 768px) {
                .modal:before {
                    display: inline-block;
                    vertical-align: middle;
                    content: " ";
                    height: 100%;
                }
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }
        </style>
    </head>

    <body>
        <?php
            require_once("../database.php");
            require_once("../status.php");

            if ($status["ended"]) {
                header("Location: /end.php");
            }

            if (isset($_SESSION["team"])) {
                $team = new Team($_SESSION["team"]);
            }

            else {
                header("Location: /");
            }
        ?>

        <nav class="navbar navbar-light bg-faded" style="border-radius: 0 !important">
            <a class="navbar-brand" href="#"><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span></a>
            <ul class="nav navbar-nav" role="tablist">
                <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($i == 0) echo "active" ?>" href="#p<?php echo $problem->id ?>" role="tab" data-toggle="tab" data-tooltip="tooltip" data-placement="bottom" title="<?php echo $problem->name ?>"><?php echo $problem->display_id ?></a>
                    </li>
                <?php $i++; } ?>
                <li class="nav-item">
                    <a class="nav-link" href="#stats" role="tab" data-toggle="tab">Stats</a>
                </li>
                <li class="navbar-divider"></li>
                <li class="nav-item">
                    <a class="nav-link" href="../files/starter<?php echo $team->division ?>.zip" target="_blank">Download Starter Code</a>
                </li>
                <!--<li class="navbar-divider"></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Documentation</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="//docs.oracle.com/javase/8/docs/api/" target="_blank">Java</a>
                        <a class="dropdown-item" href="//docs.python.org/3.5/" target="_blank">Python</a>
                        <a class="dropdown-item" href="//www.cplusplus.com/reference/" target="_blank">C++</a>
                    </div>
                </li>-->
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><?php echo $team->name; ?></a> <!-- After href: data-toggle="popover" data-placement="bottom" data-title="About <?php echo $team->name ?>" data-content="ID: <?php echo $team->id ?><br>Division: <?php echo $team->divisionString ?><br>School: <?php echo $team->school ?><br>Members: <?php echo $team->members ?>" data-container="body" data-html="true" data-trigger="hover" -->
                </li>
                <li class="navbar-divider"></li>
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="score">Score: <?php echo $team->get_score(); ?></a>
                </li>
                <li class="navbar-divider"></li>
                <li class="nav-item">
                    <a class="nav-link active" href="#"><span id="solved"><?php echo $team->num_solved(); ?></span>/<?php echo sizeof(Problem::all($team)); ?> Problems Solved</a>
                </li>
                <!--<li class="navbar-divider"></li>
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="countdown"></a>
                </li>-->
                <li class="navbar-divider"></li>
                <li class="nav-item">
                    <a class="nav-link" href="../destroy.php">Log Out</a>
                </li>
            </ul>
        </nav>

        <div class="container">
            <br>

            <div class="tab-content">
                <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                    <div role="tabpanel" class="tab-pane fade in <?php if ($i == 0) echo "active" ?>" id="p<?php echo $problem->id ?>">
                        <div class="page-header">
                            <h1><small><?php echo $problem->display_id ?>.</small> <?php echo $problem->name ?> <small>(<?php echo $problem->points ?> points)</small></h1>
                        </div>

                        <br>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <h4 class="card-header"><a href="#">Question <i class="fa fa-chevron-down"></i></a></h4>
                                    <div class="card-block">
                                        <?php foreach ($problem->question as $section => $content) { ?>
                                            <b><?php echo $section ?></b>
                                            <p><?php echo $content ?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="card">
                                    <h4 class="card-header"><a href="#">Sample Data <i class="fa fa-chevron-down"></i></a></h4>
                                    <div class="card-block">
                                        <?php if (is_array($problem->sample)) { ?>
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
                                        <?php } else { ?>
                                            <em>More examples are provided in the question packet.</em>
                                            <br><br>
                                            <samp><?php echo $problem->sample ?></samp>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($problem->hand_graded) { ?>
                            <form method="post" action="submit-handgraded.php" id="submit-handgraded" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="files[]" class="form-control input-lg" multiple>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-lg pull-right">
                                </div>
                                <input type="hidden" name="problem" value="<?php echo $problem->id ?>">
                            </form>
                        <?php } else { ?>
                          <form method="post" action="submit.php" class="submit">
                              <div class="form-group">
                                  <ul class="nav nav-tabs" role="tablist">
                                      <li class="nav-item">
                                          <a href="#" class="java-tab-button nav-link active" data-tab="java-tab" role="tab" data-toggle="tab">Java</a>
                                      </li>
                                      <li class="nav-item">
                                          <a href="#" class="python-tab-button nav-link" data-tab="python-tab" role="tab" data-toggle="tab">Python</a>
                                      </li>
                                      <?php if ($problem->cpp) { ?>
                                          <li class="nav-item">
                                              <a href="#" class="cpp-tab-button nav-link" data-tab="cpp-tab" role="tab" data-toggle="tab">C++</a>
                                          </li>
                                      <?php } ?>
                                      <li class="nav-item pull-right">
                                          <a href="//www.asciitable.com/index/asciifull.gif" target="_blank" class="nav-link">ASCII Table</a>
                                      </li>
                                      <li class="nav-item pull-right">
                                          <a href="//docs.oracle.com/javase/8/docs/api/" target="_blank" class="documentation nav-link">Documentation</a>
                                      </li>
                                  </ul>
                                  <div class="tab-content">
                                      <div role="tabpanel" class="java-tab tab-pane active">
                                          <textarea rows="10" name="java" class="java code" data-storage-id="java<?php echo $problem->id ?>"><?php echo $problem->java["stub"] ?></textarea>
                                      </div>
                                      <div role="tabpanel" class="python-tab tab-pane">
                                          <textarea rows="10" name="python" class="python code" data-storage-id="python<?php echo $problem->id ?>"><?php echo $problem->python["stub"] ?></textarea>
                                      </div>
                                      <div role="tabpanel" class="cpp-tab tab-pane">
                                          <textarea rows="10" name="cpp" class="cpp code" data-storage-id="cpp<?php echo $problem->id ?>"><?php echo $problem->cpp["stub"] ?></textarea>
                                      </div>
                                  </div>
                              </div>
                              <input type="hidden" name="problem" value="<?php echo $problem->id ?>">
                              <input type="hidden" name="language" value="0">
                              <div class="form-group">
                                  <input type="submit" class="btn btn-primary btn-lg pull-right" <?php if ($team->is_solved($problem->id)) echo ' value="Solved" disabled' ?>>
                              </div>
                          </form>
                        <?php } ?>
                    </div>
                <?php $i++; } ?>
                <div role="tabpanel" class="tab-pane fade in" id="stats">
                    <div class="jumbotron text-center">
                        <h1><?php echo $team->name ?> <small>from <?php echo $team->school ?></small></h1>
                        <h2>Team #<?php echo $team->id ?> &mdash; <?php echo $team->divisionString ?> Division</h2>
                        <h3><?php echo $team->members ?></h3>
                    </div>

                    <div class="card">
                        <h4 class="card-header">Submissions <i class="fa fa-chevron-down opposite"></i></h4>
                        <div class="card-block show">
                            <div class="center-block">
                                <ul class="nav nav-pills center" role="tablist">
                                    <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?php if ($i == 0) echo "active" ?>" data-toggle="tab" href="#stats<?php echo $problem->id ?>" role="tab"><?php echo $problem->display_id ?></a>
                                        </li>
                                    <?php $i++; } ?>
                                </ul>
                            </div>

                            <br>

                            <div class="tab-content">
                                <?php $i = 0; foreach (Problem::all($team) as $problem) { ?>
                                    <div class="tab-pane fade in <?php if ($i == 0) echo "active" ?>" id="stats<?php echo $problem->id ?>" role="tabpanel">
                                        <div class="col-lg-6 center">
                                            <ul class="list-group submissions">
                                                <?php foreach (Submission::for_team_problem($team->id, $problem->id) as $submission) { ?>
                                                    <li class="list-group-item list-group-item-<?php echo is_numeric($submission->result) && $submission->result == "100" ? "success" : ($submission->result == "compilation" ? "warning" : "danger") ?>">
                                                        <h2 class="list-group-item-heading">Result: <?php echo $submission->result . (is_numeric($submission->result) ?  "%" : "") ?></h2>
                                                        <p class="list-group-item-text">Timestamp: <?php echo $submission->date ?></p>
                                                        <p class="list-group-item-text">Language: <?php echo $submission->language ?></p>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php $i++; } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <div class="modal fade" id="submittingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="submittingModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Submitting... <i class="fa fa-spin fa-spinner pull-right" style="margin-top: 5px"></i></h4>
                </div>
                <div class="modal-body" style="text-align: center">
                  <em>This should not take more than 10 seconds.</em>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/animaterotate.js"></script>
        <script src="../js/tether.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/codemirror.js"></script>
        <script src="../js/clike.js"></script>
        <script src="../js/python.js"></script>
        <!--<script src="../js/countdown.min.js"></script>-->
        <script>
            $(document).ready(function() {
                // I could hard-code but this is easier.
                var modalTitleHTML = $(".modal-title").html();
                var modalBodyHTML = $(".modal-body").html();
              
                $(".java").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-java", matchBrackets: true, lines: 5 }));
                });
                
                $(".python").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-python", lines: 5 }));
                });
                
                $(".cpp").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-c++src", lines: 5 }));
                });

                $.each(localStorage, function(key, value) {
                    if ($('.code[data-storage-id="' + key + '"]').length > 0) {
                        $('.code[data-storage-id="' + key + '"]').data("CodeMirrorInstance").getDoc().setValue(value);
                    }
                });
                
                $(".modal-footer").hide();
                $(".card-block:not(.show)").hide();
                
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

                $(".card-header").click(function() {
                    var hidden = !$(this).parent().find(".card-block").is(":visible");
                  
                    $(this).parent().find(".card-block").slideToggle();

                    var initial = $(this).find("i").hasClass("opposite") ? (hidden ? 360 : 180) : (hidden ? 180 : 360);
                    $(this).find("i").animateRotate(initial, initial - 180);
                    //$(this).find("i").animateRotate(hidden ? 180 : 360, hidden ? 0 : 180);
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
                    $(".code").each(function() {
                        localStorage.setItem($(this).attr("data-storage-id"), $(this).data("CodeMirrorInstance").getValue());
                    });
                };

                $(".submit").submit(function(event) {
                    event.preventDefault();

                    var form = this;
                    var text = $(".modal-body");

                    $(this).find('input[type="submit"]').prop("disabled", true);
                  
                    $(".modal-title").html(modalTitleHTML);
                    text.html(modalBodyHTML);
                    $(".modal-footer").hide();
                    $("#submittingModal").modal("show");

                    $.post("submit.php", $(this).serialize(), function(jsonRaw) {
                        $(".modal-footer").show();
                        $(".modal-title").html('Result');  
                      
                        var json;

                        try {
                            json = JSON.parse(jsonRaw);
                        }

                        catch (err) {
                            text.html('<span style="color: red">An internal error occurred. Please refresh the page and resubmit. If the problem persists, please tell a judge.<br><br>' + jsonRaw + '</span>');
                            $(form).find('input[type="submit"]').prop("disabled", false);
                            return;
                        }

                        text.html(isNormalInteger(String(json["result"])) ? json["message"] : '<span style="color: red">' + json["message"] + '</span>');

                        if (json["result"] == 100 || json["result"] == "alreadysolved") {
                            $(form).find('input[type="submit"]').prop("value", "Solved");
                        }

                        else {
                            $(form).find('input[type="submit"]').prop("disabled", false);
                        }

                        $("#score").text("Score: " + json["score"]);
                        $("#solved").text(json["solved"]);

                        // TODO: Fix this for the new stats page.
                        $("#stats" + json["id"]).find(".submissions").empty();
                        $.each(json["submissions"], function(i, submission) {
                            $("#stats" + json["id"]).find(".submissions").append(
                                '<li class="list-group-item list-group-item-' + (isNormalInteger(submission["result"]) && submission["result"] == "100" ? "success" : (submission["result"] == "compilation" ? "warning" : "danger")) + '">' +
                                    '<h2 class="list-group-item-heading">Result: ' + submission["result"] + (isNormalInteger(submission["result"]) ? "%" : "") + '</h2>' +
                                    '<p class="list-group-item-text">Timestamp: ' + submission["date"] + '</p>' +
                                    '<p class="list-group-item-text">Language: ' + submission["language"] + '</p>'+
                                '</li>'
                            );
                        });
                    });
                });

                $("#submit-handgraded").submit(function(event) {
                    event.preventDefault();

                    var form = this;
                    var text = $(".modal-body");

                    $(this).find('input[type="submit"]').prop("disabled", true);

                    $(".modal-title").html(modalTitleHTML);
                    text.html(modalBodyHTML);
                    $(".modal-footer").hide();
                    $("#submittingModal").modal("show");

                    $.ajax({
                        url: "submit-handgraded.php",
                        type: "POST",
                        xhr: function() {  // Custom XMLHttpRequest
                            var myXhr = $.ajaxSettings.xhr();
                            if (myXhr.upload) { // Check if upload property exists
                                // myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // For handling the progress of the upload
                            }
                            return myXhr;
                        },
                        data: new FormData($("#submit-handgraded")[0]),
                        success: function(jsonRaw) {
                            $(".modal-footer").show();
                            $(".modal-title").html('Result');

                            var json;

                            try {
                                json = JSON.parse(jsonRaw);
                            }

                            catch (err) {
                                text.html('<span style="color: red">An internal error occurred. Please tell a judge. ' + jsonRaw + '</span>');
                                $(form).find('input[type="submit"]').prop("disabled", false);
                                return;
                            }

                            text.html(json["message"]);
                            $(form).find('input[type="submit"]').prop("value", "Submitted");
                            $("#solved").text(json["solved"]);
                        },
                        error: function(jsonRaw) {
                            alert(JSON.stringify(jsonRaw));
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                });
            });

            setInterval(function() {
                $.get("../status.php", {stat:"ended"}, function(data) {
                    if (data == true) {
                        location.href = "/";
                    }
                });
            }, 5 * 1000);
            
            function isNormalInteger(str) {
                var n = ~~Number(str);
                return String(n) === str && n >= 0;
            }
        </script>
    </body>
</html>