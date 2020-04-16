<html>
    <head>
        <title>CodeLM 2016 Dashboard</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Dashboard">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/codemirror.css">

        <style>
            .CodeMirror {
                cursor: text;
                height: 100%;
            }
            
            .card-header {
                cursor: default;
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            
            .back {
                cursor: hand;
            }

            .card-header a {
                color: black;
                text-decoration: none;
            }
            
            .card#menu {
                z-index: 1050;
                position: absolute;
                background: white;
                top: 25%;
                right: 25%;
                width: 50%;
                height: 50%;
            }

            .overlay {
                z-index: 1049;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: #000;
                opacity: 0.5;
                filter: alpha(opacity=50);
            }

            .menu-choice {
                width: 100px;
                height: 100px;
                text-align: center;
                line-height: 100px;
                vertical-align: middle;
                font-size: 2em;
                cursor: hand;
                display: inline-block;
            }
        </style>
    </head>

    <body>
        <?php
            session_start();
            require_once("database.php");

            if (isset($_SESSION["team"])) {
                $team = new Team($_SESSION["team"]);
            }

            else {
                header("Location: index.php");
            }
        ?>

        <div class="overlay"></div>
        
        <div class="card" id="menu"></div>

        <textarea rows="10" name="java" class="java code"></textarea>

        <div class="hidden" id="main">
            <h4 class="card-header">CodeLM 2016</h4>
            <div class="card-block">
                <?php foreach (Problem::all($team) as $problem) { ?>
                    <p class="card menu-choice" data-toggle="tooltip" data-placement="bottom" title="<?php echo $problem->name ?>"><?php echo $problem->id ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="hidden" id="p1">
            <h4 class="card-header"><span class="back">&#9668;</span> Problem 1: Even or odd?</h4>
            <div class="card-block">
                <p>Given parameter <code>i</code> of type <code>int</code>, return <code>true</code> if the number is even and <code>false</code> if it is odd.</p>
                <div class="text-center">
                    <input type="button" value="Sample Data" class="btn btn-primary-outline btn-sm center-block" style="display: inline-block">
                    <input type="button" value="Submit" class="btn btn-primary-outline btn-sm center-block" style="display: inline-block">
                </div>
            </div>
        </div>

        <!--<div class="card center">
            <h4 class="card-header">CodeLM</h4>
            <div class="card-block">
                <p>Problems</p>
            </div>
        </div>-->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/tether.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/codemirror.js"></script>
        <script src="../js/clike.js"></script>
        <script src="../js/python.js"></script>
        <!--<script src="//raw.githubusercontent.com/mckamey/countdownjs/master/countdown.min.js"></script>-->
        <script>
            $(document).ready(function() {
                $("#menu").html($("#main").html()).css("top", $("body").height() / 4).css("right", $("body").width() / 4);

                $(".hidden").hide();

                $(".java").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-java", matchBrackets: true, lines: 5 }));
                });
                
                $(".python").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-python", lines: 5 }));
                });
                
                $(".cpp").each(function() {
                    $(this).data("CodeMirrorInstance", CodeMirror.fromTextArea(this, { lineNumbers: true, mode: "text/x-c++src", lines: 5 }));
                });
                
                //$(".card-block").hide();

                $('[data-toggle="tooltip"]').tooltip();
                
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
                    $(this).parent().parent().find(".documentation").prop("href", "//docs.python.org/3.5/");
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

                /*$(".card-header").click(function() {
                    $(this).parent().find(".card-block").slideToggle();
                });*/

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

                $("span.back").on("click", function() {
                    console.log("Hi");

                    $("#menu").html($("#main").html());

                    $(".overlay").fadeIn(1500);

                    $("#menu").animate({
                        "top": $("body").height() / 4,
                        "right": $("body").width() / 4,
                        "width": "50%",
                        "height": "50%"
                    }, 1500);
                });

                $(".menu-choice").click(function() {
                    $(this).tooltip("hide");

                    $("#menu").html($("#p" + $(this).text()).html());

                    $(".overlay").fadeOut(1500);

                    $("#menu").animate({
                        "top": "20px",
                        "right": "20px",
                        "width": "25%",
                        "height": "auto"
                    }, 1500);
                });

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