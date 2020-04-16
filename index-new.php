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

            if ($status["ended"]) {
                header("Location: /end.php");
            }

            else if (isset($_SESSION["team"])) {
                header("Location: /dashboard");
            }
            
            else if (isset($_SESSION["judge"])) {
                header("Location: /judge");
            }
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span></h1>
            <h2>It's here.</h2>
        </div>

        <div class="container">
            <div class="col-lg-6 center">
                <div class="card">
                    <h4 class="card-header">Log In</h4>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="alert" class="alert alert-danger" role="alert">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                        <form id="formLogin" method="post" action="login.php">
                            <div class="form-group center">
                                <input type="password" name="password" placeholder="Password or Key" class="form-control input-lg">
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Log In" class="btn btn-primary btn-lg center-block">
                            </div>
                        </form>
                    </div>
                </div>

                <div style="height: 10px"></div>

                <img src="img/newwave.png" height="75px" style="display: inline-block; padding-right: 10px">
                <div style="display: inline-block; text-align: left; vertical-align: middle; padding-top: 10px">
                    <h4 style="margin-bottom: 0">New Wave</h4>
                    <h5>Computers</h5>
                </div>
                <img src="img/sig.png" height="50px" style="display: inline-block; margin-top: 10px" class="pull-right">
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#alert").hide();

                $("#formLogin").submit(function(event) {
                    event.preventDefault();

                    $("#alert").fadeOut();

                    $.post("login.php", $("#formLogin").serialize(), function(data) {
                        if (data == -4) {
                            location.href = "end.php";
                        }

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
            });
        </script>
    </body>
</html>