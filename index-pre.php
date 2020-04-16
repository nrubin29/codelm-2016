<html>
    <head>
        <title>CodeLM 2016</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Login">
        <meta name="author" content="Noah Rubin">
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <style>
            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
            }

            .nav-tabs > li, .nav-pills > li {
                float: none !important;
                display: inline-block !important;
            }

            .nav-tabs, .nav-pills {
                text-align:center;
            }

            .btn-group {
                padding-left: 10px;
            }
        </style>
    </head>

    <body>
        <?php
            require_once("status.php");

            session_start();

            if (isset($_SESSION["team"])) {
                header("Location: /dashboard");
            }
            
            else if (isset($_SESSION["judge"])) {
                header("Location: /judge");
            }
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span></h1>
            <h2><small>The hype is real.</small></h2>
            <h3><small id="countdown"></small></h3>
        </div>

        <div class="container">
            <div class="col-lg-6 center">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#login" role="tab">Log In</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#register" role="tab">Register for Practice</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#about" role="tab">About</a>
                    </li>
                </ul>

                <br>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="login" role="tabpanel">
                        <div class="card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="alert" class="alert alert-danger" role="alert">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                                <form id="formLogin" method="post" action="login.php" autocomplete="off">
                                    <div class="form-group center">
                                        <input type="password" name="password" placeholder="Password or Key" class="form-control input-lg">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Log In" class="btn btn-primary btn-lg center-block">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="register" role="tabpanel">
                        <div class="card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="alertRegister" class="alert alert-danger" role="alert">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                                <form id="formRegister" method="post" action="registerpractice.php" autocomplete="off">
                                    <div class="form-group center" style="padding-bottom: 40px">
                                        <div class="btn-group pull-right" data-toggle="buttons" style="width: 102%">
                                            <label class="btn btn-primary active" style="width: 50%">
                                                <input type="radio" name="division" value="3" autocomplete="off" checked> Intermediate
                                            </label>
                                            <label class="btn btn-primary" style="width: 50%">
                                                <input type="radio" name="division" value="4" autocomplete="off"> Advanced
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group center">
                                        <input type="text" name="name" placeholder="Name" class="form-control input-lg">
                                    </div>
                                  <div class="form-group center">
                                        <input type="text" name="school" placeholder="School" class="form-control input-lg">
                                    </div>
                                    <div class="form-group center">
                                        <input type="password" name="password" placeholder="Password" class="form-control input-lg">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Register" class="btn btn-primary btn-lg center-block">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="about" role="tabpanel">
                        <div class="card">
                            <div class="card-block">
                                <legend>Dashboard</legend>
                                <ul class="list-group">
                                    <li class="list-group-item">Noah Rubin</li>
                                </ul>
                                <br>
                                <legend>Teacher Supervisor</legend>
                                <ul class="list-group">
                                    <li class="list-group-item">Thomas Swope</li>
                                </ul>
                                <br>
                                <legend>Questions</legend>
                                <ul class="list-group">
                                    <li class="list-group-item">Max Roling</li>
                                    <li class="list-group-item">Noah Rubin</li>
                                    <li class="list-group-item">Thomas Swope</li>
                                    <li class="list-group-item">David Vonderheide</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/countdown.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#alert").hide();
                $("#alertRegister").hide();

                var end = new Date(2016, 1, 9, 9, 15);
                var cd = countdown(function(time) {
                    if (end.getTime() <= new Date().getTime()) {
                        $("#countdown").text("It's time.");
                        window.clearInterval(cd);
                    }

                    else {
                        $("#countdown").text(time.toString());
                    }
                }, end, countdown.DEFAULTS);

                $("#formLogin").submit(function(event) {
                    event.preventDefault();

                    $("#alert").fadeOut();

                    $.post("login.php", $("#formLogin").serialize(), function(data) {
                        if (data == -3) {
                            location.href = "/judge";
                        }
                        
                        else if (data == -2) {
                            $("#alert").fadeIn().find("p").text("You cannot log in at this time.");
                        }

                        else if (data == -1) {
                            $("#alert").fadeIn().find("p").text("The team password you entered is incorrect.");
                        }

                        else {
                            location.href = "/dashboard";
                        }
                    });
                });

                $("#formRegister").submit(function(event) {
                    event.preventDefault();

                    $("#alertRegister").fadeOut();

                    $.post("registerpractice.php", $("#formRegister").serialize(), function(data) {
                        if (data == -2) {
                            $("#alertRegister").fadeIn().find("p").text("You cannot register at this time.");
                        }

                        else if (data == -1) {
                            $("#alertRegister").fadeIn().find("p").text("That password has already been taken.");
                        }

                        else {
                            location.href = "/dashboard";
                        }
                    });
                });
            });
        </script>
    </body>
</html>