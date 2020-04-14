<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["loc"] !== 'welcomeforadmin.php' ) {
        header("location: loginforadmin.php");
        exit;
}


// Include config file
require_once "dbconnectadmin.php";


$username = $username_err = "";

$moviename = $starttime = $seats = "";
$moviename_err = $starttime_err = $seats_err = "";

$table = "";

$sql = "SELECT name, username FROM hall WHERE doneauth = 0";


if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        // mysqli_stmt_bind_param($stmt, "s", $param_name);
        // echo "AA";
        // Set parameters
        // $param_name = mysqli_real_escape_string($link, $_SESSION["name"]);

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_bind_result($stmt, $name, $username);
                // mysqli_stmt_store_result($stmt);
                $count = 1;
                while (mysqli_stmt_fetch($stmt)) {
                        $row = "<tr>
                        <th>" . $count . "</th>
                        <th>" . $name . "</th>
                        <td>" . $username . "</td>
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
        if (empty(trim($_POST["username"]))) {
                $username_err = "Please enter a valid username from list.";
        } else {
                $username = trim($_POST["username"]);
        }

        // Check input errors before inserting in database
        if (empty($username_err)) {

                // Prepare an insert statement
                $sql = "UPDATE hall SET doneauth = 1 WHERE username = ? ";
 
                if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "s", $param_username);

                        // Set parameters
                        $param_username = mysqli_real_escape_string($link,$username);

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                                // Redirect to login page
                                header("location: userauthforadmin.php");
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
                <h2>Remember, "With Great Power Comes Greater Responsiblity"</h2>
        </div>
        <p style="font: 25px sans-serif;">
                List of users requesting hall status:
        </p>
        <table class="table table-hover table-striped">
                <thead>
                        <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name of Theatre</th>
                                <th scope="col">Username</th>
                        </tr>
                </thead>
                <tbody>
                        <?php echo $table; ?>
                </tbody>
        </table>



        <div class="wrapper">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label>Username of Theatre</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                                <span class="help-block"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Authorize">
                        </div>
                </form>
        </div>

        <p align="center">
                <!-- <a href="allticketsforhall.php" class="btn btn-info">See your Tickets</a> -->
                <a href="logoutforadmin.php" class="btn btn-danger">Log Out of Your Account</a>
        </p>
</body>

</html>