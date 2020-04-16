<html>
    <head>
        <title>CodeLM 2016 Judging Dashboard</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2016 Judging Dashboard">
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
        ?>

        <div class="jumbotron jumbotron-fluid text-center">
            <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2016</span> Judging Dashboard</h1>
            <h2><small>For official use only.</small></h2>
        </div>

        <div class="container">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="#demo" class="nav-link active" role="tab" data-toggle="tab">Demo</a>
                </li>
                <li class="nav-item">
                    <a href="#intermediate" class="nav-link" role="tab" data-toggle="tab">Intermediate</a>
                </li>
                <li class="nav-item">
                    <a href="#advanced" class="nav-link" role="tab" data-toggle="tab">Advanced</a>
                </li>
                <li class="nav-item">
                    <a href="#practice_intermediate" class="nav-link" role="tab" data-toggle="tab">Practice Intermediate</a>
                </li>
                <li class="nav-item">
                    <a href="#practice_advanced" class="nav-link" role="tab" data-toggle="tab">Practice Advanced</a>
                </li>
                <li class="nav-item pull-right">
                    <a href="../destroy.php" class="nav-link">Log Out</a>
                </li>
                <li class="nav-item pull-right">
                    <a href="registration.php" class="nav-link" target="_blank">Registration</a>
                </li>
            </ul>

            <br><br>

            <div class="tab-content">
                <?php $divisions = array("demo", "intermediate", "advanced", "practice_intermediate", "practice_advanced"); for ($i = 0; $i < sizeof($divisions); $i++) { ?>
                    <div role="tabpanel" class="tab-pane <?php if ($i == 0) echo "active" ?>" id="<?php echo $divisions[$i] ?>">
                        <?php $number = 1; foreach (Team::get_teams($i) as $team) { ?>
                            <div class="card">
                                <h4 class="card-header"><?php echo $number; ?></h4>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Members</th>
                                                    <th>School</th>
                                                    <th>Score</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?php echo $team->id ?></td>
                                                    <td><?php echo $team->name ?></td>
                                                    <td><?php echo $team->members ?></td>
                                                    <td><?php echo $team->school ?></td>
                                                    <td class="score"><?php echo $team->get_score() ?></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-sm-6">
                                            <ul class="list-group">
                                                <?php foreach (Submission::for_team($team->id) as $submission) { ?>
                                                    <li class="list-group-item" data-id="<?php echo $submission->id ?>">
                                                      <a href="../submissions/<?php echo $submission->name . $submission->language_extension ?>" target="_blank">Problem <?php echo $submission->problem ?> &bull; <?php echo $submission->date ?> &bull; <?php echo $submission->language ?> &bull; <?php echo $submission->result . (is_numeric($submission->result) ?  "%" : "") ?></a> <span class="remove close pull-right"><span aria-hidden="true"><i class="fa fa-times"></i></span></span> <span class="modify close pull-right" style="margin-right: 5px;"><span aria-hidden="true"><i class="fa fa-pencil"></i></span></span>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <br>
                                            <input type="button" class="manual btn btn-primary" value="Manual Add" data-team="<?php echo $team->id ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $number++; } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $(".remove").click(function() {
                    var $this = $(this);

                    if (confirm("Are you sure you want to remove this submission?")) {
                        $.post("modifysubmission.php", { operation: "remove", id: $this.parent().attr("data-id") }, function(data) {
                            data = data.trim();
                            if (data == "1") {
                                alert("Success.");
                                $this.parent(".card-block").find(".score").html("BAD");
                                $this.parent().remove();
                            }

                            else {
                                alert("Failure. " + data);
                            }

                            //alert((data == "1" ? "Success" : "Failure") + ". Reloading...");
                            //location.reload();
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