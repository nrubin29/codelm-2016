<html>
    <head>
        <title>CodeLM 2016 is over</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 is over">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="css/bootstrap.min.css">

        <style>
            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
            }

            img.center {
                display: block;
                margin: 0 auto;
            }

            body {
                padding-bottom: 20px;
            }

            .nav-tabs > li, .nav-pills > li {
                float: none !important;
                display: inline-block !important;
            }

            .nav-tabs, .nav-pills {
                text-align:center;
            }
        </style>
    </head>

    <body>
        <?php
            require_once("status.php");

            /*if (!$status["ended"]) {
                header("Location: /");
            }*/
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span> <small>is over.</small></h1>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-lg-6 center">
                    <blockquote class="blockquote">
                        <p>Big thanks to everyone who completed. This is the first year we have opened CodeLM to other schools and we hope you enjoyed it. Below you can access various resources from the event. Please have each person on your team fill out the feedback form. Your feedback is vital to the future of CodeLM. See you next year!</p>
                        <footer>The CodeLM Team</footer>
                    </blockquote>

                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#resources" role="tab">Resources</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#credits" role="tab">Credits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#sponsor" role="tab">Sponsor</a>
                        </li>
                    </ul>

                    <br>

                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="resources" role="tabpanel">
                            <ul class="list-group">
                                <li class="list-group-item"><a href="//docs.google.com/forms/d/1vnNFJxFf6-inUW9m6nM6C3SvssHu4I6c6TdV5e6hn2A/viewform" target="_blank">Take Feedback Survey</a></li>
                                <li class="list-group-item"><a href="//drive.google.com/folderview?id=0B8epdyHsLxdSOXRtYTNJTnI2bEk&usp=sharing" target="_blank">View Pictures</a></li>
                                <li class="list-group-item"><a href="//youtu.be/914ysyH6AVw" target="_blank">Watch Intro Video</a></li>
                                <li class="list-group-item">Download Questions (<a href="files/questions1.pdf" target="_blank">Intermediate</a> &bull; <a href="files/questions2.pdf" target="_blank">Advanced</a>)</li>
                                <li class="list-group-item">Download Starter Code (<a href="files/starter1.zip" target="_blank">Intermediate</a> &bull; <a href="files/starter2.zip" target="_blank">Advanced</a>)</li>
                                <li class="list-group-item">Download Solutions (Coming Soon)</li>
                                <li class="list-group-item"><a href="//github.com/nrubin29/codelm" target="_blank">View Dashboard Source Code on GitHub (Will Be Updated Soon)</a></li>
                            </ul>
                        </div>
                        <div class="tab-pane fade in" id="credits" role="tabpanel">
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
                                        <li class="list-group-item">Ethan Boyer</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade in text-center" id="sponsor" role="tabpanel">
                            <div class="card">
                                <div class="card-block">
                                    <a href="http://www.sig.com/" target="_blank"><img src="img/sig.png" height="75px" class="center"></a>
                                    <br>
                                    <em>SIG is a global quantitative trading firm founded with an entrepreneurial mindset and a rigorous analytical approach to decision making.</em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>