<?php
// Initialize the session
session_start();



// Include config file
require_once "dbconnect.php";


$table = "";

$sql = "SELECT moviename, starttime, seats, hall FROM movies ORDER BY starttime ASC";


if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_bind_result($stmt, $moviename, $starttime, $seats, $hall);
                // mysqli_stmt_store_result($stmt);
                $count = 1;
                while (mysqli_stmt_fetch($stmt)) {
                        $row = "<tr>
                        <th>" . $count . "</th>
                        <td>" . $moviename . "</td>
                        <td>" . $hall . "</td>
                        <td>" . $starttime . "</td>
                        <td>" . $seats . "</td>
                        </tr>";
                        $table .= $row;
                        $count = $count + 1;
                }
        } else {
                echo "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <title>bookmymovie</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <style type="text/css">
                body {
                        font: 18px sans-serif;
                        text-align: center;
                }

                .wrapper {
                        width: 100%;
                        padding: 50px;
                }

                .wrapper2 {
                        width: 100%;
                        padding: 5px;
                }
        </style>
</head>

<body>
        <div class="pb-2 mt-4 mb-2 border-bottom">
                <h1> Welcome to bookmymovie.</h1>
        </div>

        <p>
                <a href="login.php" class="btn btn-info">Login</a>
                <a href="loginforhall.php" class="btn btn-warning">Login for Theatre Managers</a>
        </p>

        <p style="font: 25px sans-serif;">
                List of all movies currently showing in nearby theatres:
        </p>
        <div class="wrapper2">
                <table class="table table-hover table-striped">
                        <thead>
                                <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Movie Name</th>
                                        <th scope="col">Theatre</th>
                                        <th scope="col">Start Time</th>
                                        <th scope="col">Seats Left</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php echo $table; ?>
                        </tbody>
                </table>
        </div>

</body>

</html>