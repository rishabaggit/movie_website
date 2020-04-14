<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["loc"] !== 'welcome.php' ) {
        header("location: login.php");
        exit;
}



// Include config file
require_once "dbconnectuser.php";


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


// Now Work on Form
$movie = $hall = $time = "";
$seat_err = $movie_err = $hall_err = $time_err =  "";


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if movie is empty
        if (empty(trim($_POST["movie"]))) {
                $movie_err = "Please enter a valid movie from the list.";
        } else {
                $movie = trim($_POST["movie"]);
        }


        // Check if hall is empty
        if (empty(trim($_POST["hall"]))) {
                $hall_err = "Please enter a valid theatre from the list.";
        } else {
                $hall = trim($_POST["hall"]);
        }


        // Check if time is empty
        if (empty(trim($_POST["time"]))) {
                $time_err = "Please enter a valid starttime from the list.";
        } else {
                $time = trim($_POST["time"]);
                // echo $time;
        }


        // Validate credentials
        if (empty($movie_err) && empty($hall_err)) {
                // Prepare a select statement

                $param_time = mysqli_real_escape_string($link, $time);
                $sql = "SELECT seats FROM movies WHERE moviename = ? AND hall = ? AND starttime = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sss", $param_movie, $param_hall, $param_time);

                        // Set parameters
                        $param_movie = mysqli_real_escape_string($link, $movie);
                        $param_hall = mysqli_real_escape_string($link, $hall);
                        $param_time = mysqli_real_escape_string($link, $time);

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                                // Store result
                                mysqli_stmt_store_result($stmt);

                                // Check if username exists, if yes then verify password
                                // echo mysqli_stmt_num_rows($stmt);

                                if (mysqli_stmt_num_rows($stmt) == 1) {
                                        // Bind result variables
                                        mysqli_stmt_bind_result($stmt, $seat);
                                        if (mysqli_stmt_fetch($stmt)) {
                                                if ($seat > 0) {
                                                        $sql1 = "INSERT INTO tickets ( name, username, moviename, hall, starttime, seatnum) VALUES (?, ?, ?, ?, ?, ?)";

                                                        if ($stmt = mysqli_prepare($link, $sql1)) {
                                                                // Bind variables to the prepared statement as parameters
                                                                mysqli_stmt_bind_param($stmt, "sssssi", $param_name, $param_username, $param_moviename, $param_hall, $param_starttime, $param_seatnum);

                                                                // Set parameters
                                                                $param_name = mysqli_real_escape_string($link, $_SESSION["name"]);
                                                                $param_username = mysqli_real_escape_string($link, $_SESSION["username"]);
                                                                $param_moviename = mysqli_real_escape_string($link, $movie);
                                                                $param_hall = mysqli_real_escape_string($link, $hall);
                                                                $param_starttime = mysqli_real_escape_string($link, $time);
                                                                $param_seatnum = $seat;

                                                                // Attempt to execute the prepared statement
                                                                if (mysqli_stmt_execute($stmt)) {
                                                                        $sql = "UPDATE movies SET seats = seats - 1 WHERE moviename = ? AND hall = ? AND starttime = ?";

                                                                        if ($stmt = mysqli_prepare($link, $sql)) {
                                                                                // Bind variables to the prepared statement as parameters
                                                                                mysqli_stmt_bind_param($stmt, "sss", $param_movie, $param_hall, $param_time);

                                                                                // Set parameters
                                                                                $param_movie = mysqli_real_escape_string($link, $movie);
                                                                                $param_hall = mysqli_real_escape_string($link, $hall);
                                                                                $param_time = mysqli_real_escape_string($link, $time);

                                                                                // Attempt to execute the prepared statement
                                                                                if (mysqli_stmt_execute($stmt)) {
                                                                                        header("location: alltickets.php");
                                                                                } else {
                                                                                        echo "Something went wrong. Please try again later.";
                                                                                }
                                                                        }
                                                                        // Redirect to login page
                                                                } else {
                                                                        echo "Something went wrong. Please try again later.";
                                                                }
                                                        }

                                                        // Close statement
                                                        mysqli_stmt_close($stmt);
                                                } else {
                                                        $seat_err = "Sorry no seats left in that selection.";
                                                }
                                        }
                                } else {
                                        // Display an error message if username doesn't exist
                                        $movie_err = "Please verify the movies and theatre names.";
                                }
                        } else {
                                echo "Oops! Something went wrong. Please try again later.";
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
                Choose a movie to Book Ticket for the same.
        </p>
        <div class="wrapper">

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-row form-group">
                                <div class="col <?php echo (!empty($movie_err)) ? 'has-error' : ''; ?>">
                                        <input type="text" name="movie" class="form-control" placeholder="Movie Name">
                                        <span class="help-block"><?php echo $movie_err; ?></span>
                                </div>
                                <div class="col">
                                        <input type="text" name="hall" class="form-control" placeholder="Theatre">
                                        <span class="help-block"><?php echo $hall_err; ?></span>
                                </div>
                                <div class="col">

                                        <input type="datetime-local" name="time" class="form-control" placeholder="Start Time">
                                        <span class="help-block"><?php echo $time_err; ?></span>
                                        <!-- <input type="text" class="form-control" placeholder="Zip"> -->
                                </div>
                        </div>
                        <div class="form-group">
                                <span class="help-block"><?php echo $seat_err; ?></span><br>
                                <input type="submit" class="btn btn-primary" value="BookTicket">
                        </div>
                </form>
        </div>


        <p style="font: 25px sans-serif;">
                List of movies currently showing in nearby theatres:
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

        <p>
                <a href="alltickets.php" class="btn btn-info">See your Tickets</a>
                <a href="passwordreset.php" class="btn btn-warning">Reset Your Password</a>
                <a href="logout.php" class="btn btn-danger">Log Out of Your Account</a>
        </p>
</body>

</html>