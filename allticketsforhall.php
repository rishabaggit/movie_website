<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
}



// Include config file
require_once "dbconnect.php";


$table = "";

$sql = "SELECT moviename, starttime, seatnum, name, id FROM tickets WHERE hall = ?";

// echo "Hello";

if ($stmt = mysqli_prepare($link, $sql)) {

        // echo "Hello";

        mysqli_stmt_bind_param($stmt, "s", $param_name);
        // echo "AA";
        // Set parameters
        $param_name = mysqli_real_escape_string($link, $_SESSION["name"]);


        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_bind_result($stmt, $moviename, $starttime, $seats, $name, $id);
                // mysqli_stmt_store_result($stmt);
                // echo "Hello";
                $count = 1;
                while (mysqli_stmt_fetch($stmt)) {
                        $row = "<tr>
                        <th>" . $count . "</th>
                        <td>" . $moviename . "</td>
                        <td>" . $name . "</td>
                        <td>" . $starttime . "</td>
                        <td>" . $seats . "</td>
                        <td>" . $id . "</td>
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
        <title>List of Tickets</title>
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
                <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. Welcome to bookmymovie.</h1>
        </div>

        <p style="font: 25px sans-serif;">
                Your Tickets:
        </p>
        <div class="wrapper2">
                <table class="table table-hover table-striped">
                        <thead>
                                <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Movie Name</th>
                                        <th scope="col">Name of Person</th>
                                        <th scope="col">Start Time</th>
                                        <th scope="col">Seats Number</th>
                                        <th scope="col">Ticket Id</th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php echo $table; ?>
                        </tbody>
                </table>
        </div>

        <p>
                <a href="welcomeforhall.php" class="btn btn-info">Go Back to Add Movies</a>
                <a href="logout.php" class="btn btn-danger">Log Out of Your Account</a>
        </p>
</body>

</html>