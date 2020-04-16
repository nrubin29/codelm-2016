<html>
    <head>
        <title>CodeLM 2016 Judging Dashboard</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Judging Dashboard">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">

        <style>
            #logout {
                position: fixed;
                right: 10px;
                bottom: 10px;
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

        <a href="../destroy.php" class="btn btn-danger" id="logout">Log Out</a>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span> Judging Dashboard</h1>
            <h2><small>For official use only.</small></h2>
        </div>

        <div class="container">
            <div id="teams">
                <div class="form-inline">
                    <input id="search" class="form-control" placeholder="Search for ID">
                    <div class="btn-group pull-right" data-toggle="buttons">
                        <label class="division-changer btn btn-primary" data-division="Demo">
                            <input type="checkbox" name="options" autocomplete="off"> Demo
                        </label>
                        <label class="division-changer btn btn-primary" data-division="Intermediate">
                            <input type="checkbox" name="options" autocomplete="off"> Intermediate
                        </label>
                        <label class="division-changer btn btn-primary" data-division="Advanced">
                            <input type="checkbox" name="options" autocomplete="off"> Advanced
                        </label>
                        <label class="division-changer btn btn-primary" data-division="Practice Intermediate">
                            <input type="checkbox" name="options" autocomplete="off"> Practice Intermediate
                        </label>
                        <label class="division-changer btn btn-primary" data-division="Practice Advanced">
                            <input type="checkbox" name="options" autocomplete="off"> Practice Advanced
                        </label>
                    </div>
                </div>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Division</th>
                            <th>Rank</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Members</th>
                            <th>School</th>
                            <th>Score</th>
                            <th>Score - Open Ended</th>
                            <th>Submissions</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        <?php for ($i = 0; $i < 5; $i++) { ?>
                            <?php $rank = 1; foreach (Team::get_teams($i) as $team) { ?>
                                <tr>
                                    <td class="division"><?php echo $team->divisionString ?></td>
                                    <td class="rank"><?php echo $rank ?></td>
                                    <td class="id"><?php echo $team->id ?></td>
                                    <td class="name"><?php echo $team->name ?></td>
                                    <td class="members"><?php echo $team->members ?></td>
                                    <td class="school"><?php echo $team->school ?></td>
                                    <td class="score"><?php echo $team->get_score() ?></td>
                                    <td class="scoreminus"><?php echo $team->get_score() - $team->openended ?></td>
                                    <td class="submissions"><a href="team.php?id=<?php echo $team->id ?>" target="_blank">View</a></td>
                                </tr>
                            <?php $rank++; } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
        <script>
            $(document).ready(function() {
                var divisions = [];
                var list = new List('teams', {valueNames: [ 'division', 'rank', 'id', 'name', 'members', 'school', 'score', 'submissions' ]});

                $(".division-changer").click(function() {
                    if ($(this).is(".active")) {
                        divisions.splice(divisions.indexOf($(this).attr("data-division")), 1);
                    }

                    else {
                        divisions.push($(this).attr("data-division"));
                    }

                    list.filter(function(item) {
                        return divisions.length == 0 || divisions.indexOf(item.values().division) > -1;
                    });
                });

                $("#search").on("change paste keyup", function() {
                    list.search($(this).val(), ['id']);
                });

                $(".submission-toggler").click(function() {
                    $(this).parent().find("ul").slide("toggle");
                });
            });
        </script>
    </body>
</html>