<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["loc"] !== 'welcomeforhall.php' ) {
        header("location: loginforhall.php");
        exit;
}


// Include config file
require_once "dbconnect.php";


$moviename = $starttime = $seats = "";
$moviename_err = $starttime_err = $seats_err = "";

$table = "";

$sql = "SELECT moviename, starttime, seats FROM movies WHERE hall = ?";


if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_name);
        // echo "AA";
        // Set parameters
        $param_name = mysqli_real_escape_string($link, $_SESSION["name"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_bind_result($stmt, $moviename, $starttime, $seats);
                // mysqli_stmt_store_result($stmt);
                $count = 1;
                while (mysqli_stmt_fetch($stmt)) {
                        $row = "<tr>
                        <th>" . $count . "</th>
                        <td>" . $moviename . "</td>
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




// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate Movie Name
        if (empty(trim($_POST["moviename"]))) {
                $moviename_err = "Please enter a movie name.";
        } else {
                $moviename = trim($_POST["moviename"]);
        }


        // Validate Start Time
        if (empty(trim($_POST["starttime"]))) {
                $starttime_err = "Please enter a start time for the movie.";
        } else {
                $starttime = trim($_POST["starttime"]);
        }


        // Validate Seats
        if (empty(trim($_POST["seats"]))) {
                $seats_err = "Please enter the number of seats available.";
        } else {
                $seats = trim($_POST["seats"]);
        }

        // Check input errors before inserting in database
        if (empty($moviename_err) && empty($seats_err) && empty($starttime_err)) {

                // Prepare an insert statement
                $sql = "INSERT INTO movies (moviename, hall, starttime, seats) VALUES (?, ?, ?, ?)";

                if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "ssss", $param_moviename, $_SESSION["name"] , $param_starttime, $param_seats);

                        // Set parameters
                        $param_moviename = mysqli_real_escape_string($link,$moviename);
                        $param_starttime = mysqli_real_escape_string($link,$starttime);
                        $param_seats = mysqli_real_escape_string($link,$seats);

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                                // Redirect to login page
                                
                                header("location: movieaddedforhall.php");
                        } else {
                                echo "Something went wrong. Please try again later.";
                        }
                }

                // Close statement
                mysqli_stmt_close($stmt);
        }

        // Close connection
        mysqli_close($link);
}










?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <title>Welcome</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
                body {
                        font: 14px sans-serif;
                        /* text-align: center; */
                }
                .wrapper {
                        width: 350px;
                        padding: 20px;
                }
        </style>
</head>

<body>
        <div class="page-header" align="center">
                <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. Welcome to bookmymovie.</h1>
        </div>
        <p style="font: 25px sans-serif;">
                List of movies currently showing in your theatre:
        </p>
        <table class="table table-hover table-striped">
                <thead>
                        <tr>
                                <th scope="col">#</th>
                                <th scope="col">Movie Name</th>
                                <th scope="col">Start Time</th>
                                <th scope="col">Seats Left</th>
                        </tr>
                </thead>
                <tbody>
                        <?php echo $table; ?>
                </tbody>
        </table>



        <div class="wrapper">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($moviename_err)) ? 'has-error' : ''; ?>">
                                <label>Movie Name</label>
                                <input type="text" name="moviename" class="form-control" value="<?php echo $moviename; ?>">
                                <span class="help-block"><?php echo $moviename_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($starttime_err)) ? 'has-error' : ''; ?>">
                                <label>Start Time</label>
                                <input type="datetime-local" name="starttime" class="form-control">
                                <span class="help-block"><?php echo $starttime_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($seats_err)) ? 'has-error' : ''; ?>">
                                <label>Number of Seats</label>
                                <input type="number" name="seats" class="form-control">
                                <span class="help-block"><?php echo $seats_err; ?></span>
                        </div>
                        <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Add Movie">
                        </div>
                </form>
        </div>

        <p align="center">
                <a href="allticketsforhall.php" class="btn btn-info">See your Tickets</a>
                <a href="logoutforhall.php" class="btn btn-danger">Log Out of Your Account</a>
        </p>
</body>

</html>