<html>
    <head>
        <title>CodeLM 2016 Registration</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Registration">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">

        <style>
            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
            }
        </style>
    </head>

    <body>
        <?php
            require_once("../database.php");
            session_start();
            
            if (!isset($_SESSION["judge"]) || $_SESSION["judge"] !== "true") {
                header("Location: /");
            }
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span> Registration</h1>
            <h2><small>For official use only.</small></h2>
        </div>

        <div class="container">
            <div class="col-lg-6 center">
                <div id="alert" class="alert alert-info" role="alert"></div>
                <form id="form">
                    <div class="form-group">
                        <label>Division</label>
                        <br>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary active">
                                <input type="radio" name="division" value="one" autocomplete="off" checked> Intermediate
                            </label>
                            <label class="btn btn-primary">
                                <input type="radio" name="division" value="two" autocomplete="off"> Advanced
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Team Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="members">Members</label>
                        <input type="text" id="members" name="members" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="members">School</label>
                        <input type="text" id="school" name="school" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register" class="btn btn-lg btn-primary center-block">
                    </div>
                </form>
                <!--<div align="center">
                    <a href="index.php" class="btn btn-primary btn-sm">Back to Judging Dashboard</a>
                </div>-->
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#alert").hide();

                $("#form").submit(function(event) {
                    event.preventDefault();

                    $("#alert").fadeOut();

                    $.post("register.php", $("#form").serialize(), function(data) {
                        data = data.trim();
                      
                        if (data != -1) {
                            $("#alert").text("Team registered with id " + data + " and password " + $('input[name="password"]').val() + ".");
                            $("input").val("");
                            $('input[type="submit"]').val("Register");
                        }

                        else {
                            $("#alert").text("Registration failed: " + data);
                        }

                        $("#alert").fadeIn();
                    });
                });
            });
        </script>
    </body>
</html>