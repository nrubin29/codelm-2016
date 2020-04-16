<html>
    <head>
        <title>CodeLM 2016 Countdown</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Countdown">
        <meta name="author" content="Noah Rubin">
        
        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <style>
            #text {
                position: relative;
                top: 50%;
                transform: translateY(-50%);
                text-align: center;
            }

            h1 {
                font-size: 64px !important;
            }
        </style>
    </head>

    <body>
        <div id="text">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span></h1>
            <h1><small id="countdown"></small></h1>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/countdown.min.js"></script>
        <script>
            $(document).ready(function() {
                var end = new Date(2016, 1, 9, 11, 45);
                var cd = countdown(function(time) {
                    if (end.getTime() <= new Date().getTime()) {
                        $("#countdown").text("Time!");
                        window.clearInterval(cd);
                    }

                    else {
                        $("#countdown").text(time.toString() + " remaining.");
                    }
                }, end, countdown.DEFAULTS);
            });
        </script>
    </body>
</html>