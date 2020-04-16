<html>
    <head>
        <title>CodeLM 2016 Judging Dashboard - Team Info</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Judging Dashboard - Team Info">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    </head>

    <body>
        <?php
            require_once("../database.php");
            session_start();
            
            if (!isset($_SESSION["judge"]) || $_SESSION["judge"] !== "true") {
                header("Location: /");
            }

            $team = new Team($_GET["id"]);

            $target_dir = "../submissions/team" . $team->id;
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span> Judging Dashboard</h1>
            <h2><small>For official use only.</small></h2>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <h4 class="card-header">Team Info</h4>
                        <div class="card-block">
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <th>Division</th>
                                    <td class="division"><?php echo $team->divisionString ?></td>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <td class="id"><?php echo $team->id ?></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td class="name"><?php echo $team->name ?></td>
                                </tr>
                                <tr>
                                    <th>Members</th>
                                    <td class="members"><?php echo $team->members ?></td>
                                </tr>
                                <tr>
                                    <th>School</th>
                                    <td class="school"><?php echo $team->school ?></td>
                                </tr>
                                <tr>
                                    <th>Score</th>
                                    <td class="score"><?php echo $team->get_score() ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <h4 class="card-header">Hand-Graded Question</h4>
                        <div class="card-block">
                            <?php if (!file_exists($target_dir)) { ?>
                                <em>This team has not submitted anything yet.</em>
                            <?php } else if (count(scandir($target_dir)) != 2) { ?>
                                <em>This team has submitted the following files:</em>
                                <br><br>
                                <ul class="list-group">
                                    <?php foreach (array_diff(scandir($target_dir), array(".", "..")) as $file) { ?>
                                        <li class="list-group-item">
                                            <a href="<?php echo $target_dir ?>/<?php echo $file ?>" target="_blank"><?php echo $file ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>

                            <br>

                            <form id="openendedform">
                                <div class="form-group">
                                    <input type="number" placeholder="Score" name="openended" id="openended" value="<?php echo $team->openended == 0 ? "" : $team->openended ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary pull-right">
                                </div>
                                <br>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <h4 class="card-header">Submissions <input type="button" class="manual btn btn-primary btn-sm pull-right" value="Manual Add" data-team="<?php echo $team->id ?>"></h4>
                        <div class="card-block">
                            <ul class="list-group">
                                <?php foreach (Submission::for_team($team->id) as $submission) { ?>
                                    <li class="list-group-item" data-id="<?php echo $submission->id ?>">
                                        <a href="../submissions/<?php echo $submission->name . $submission->language_extension ?>" target="_blank">Problem <?php echo $submission->problem ?> &bull; <?php echo $submission->date ?> &bull; <?php echo $submission->language ?> &bull; <?php echo $submission->result . (is_numeric($submission->result) ?  "%" : "") ?></a> <span class="remove close pull-right"><span aria-hidden="true"><i class="fa fa-times"></i></span></span> <span class="modify close pull-right" style="margin-right: 5px;"><span aria-hidden="true"><i class="fa fa-pencil"></i></span></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#openendedform").submit(function(e) {
                    e.preventDefault();

                    $.post("modifysubmission.php", { operation: "openended", value: $("#openended").val(), team: <?php echo $team->id ?> }, function(data) {
                        data = data.trim();

                        alert((data == "1" ? "Success" : "Failure") + ". Reloading...");
                        location.reload();
                    });
                });

                $(".remove").click(function() {
                    var $this = $(this);

                    if (confirm("Are you sure you want to remove this submission?")) {
                        $.post("modifysubmission.php", { operation: "remove", id: $this.parent().attr("data-id") }, function(data) {
                            data = data.trim();

                            alert((data == "1" ? "Success" : "Failure") + ". Reloading...");
                            location.reload();
                        });
                    }
                });

                $(".modify").click(function() {
                    var $this = $(this);

                    var value = prompt("Please enter the new value (% correct, compilation, toomuch, toomany, etc.)");

                    if (value) {
                        $.post("modifysubmission.php", { operation: "correct", value: value, id: $this.parent().attr("data-id") }, function(data) {
                            data = data.trim();
                            alert((data == "1" ? "Success" : "Failure") + ". Reloading...");
                            location.reload();
                        });
                    }
                });
              
              $(".manual").click(function() {
                    var $this = $(this);

                    var value = prompt("Please enter the question id.");

                    if (value) {
                        $.post("modifysubmission.php", { operation: "add", value: value, team: $this.attr("data-team") }, function(data) {
                            data = data.trim();
                            alert((data == "1" ? "Success" : "Failure") + ". Reloading...");
                            location.reload();
                        });
                    }
                });
            });
        </script>
    </body>
</html>