<?php
// Include config file
require_once "dbconnect.php";

// Define variables and initialize with empty values
$username = $name = $password = $confirm_password = "";
$username_err = $name_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate name
        if (empty(trim($_POST["name"]))) {
                $name_err = "Please enter a name.";
        } else {
                $name = trim($_POST["name"]);
        }

        // Validate username
        if (empty(trim($_POST["username"]))) {
                $username_err = "Please enter a username.";
        } else {
                // Prepare a select statement
                $sql = "SELECT id FROM hall WHERE username = ?";

                if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "s", $param_username);

                        // Set parameters
                        // $param_username = trim($_POST["username"]);

                        $param_username = mysqli_real_escape_string($link,trim($_POST["username"]));

                        // Attempt to execute the prepared statement

                        if (mysqli_stmt_execute($stmt)) {
                                /* store result */
                                mysqli_stmt_store_result($stmt);

                                if (mysqli_stmt_num_rows($stmt) == 1) {
                                        $username_err = "This username is already taken.";
                                } else {
                                        $username = trim($_POST["username"]);
                                }
                        } else {
                                echo $stmt;
                                echo "Oops! Something went wrong. Please try again later.";
                        }
                }

                // Close statement
                mysqli_stmt_close($stmt);
        }

        // Validate password
        if (empty(trim($_POST["password"]))) {
                $password_err = "Please enter a password.";
        } else {
                $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
                $confirm_password_err = "Please enter confirm password.";
        } else {
                $confirm_password = trim($_POST["confirm_password"]);
                if (empty($password_err) && ($password != $confirm_password)) {
                        $confirm_password_err = "Password did not match.";
                }
        }

        // Check input errors before inserting in database
        if (empty($name_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

                // Prepare an insert statement
                $sql = "INSERT INTO hall (username, password, name) VALUES (?, ?, ?)";

                if ($stmt = mysqli_prepare($link, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_name);

                        // Set parameters
                        $param_name = mysqli_real_escape_string($link,$name);
                        $param_username = mysqli_real_escape_string($link,$username);
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

                        // Attempt to execute the prepared statement
                        if (mysqli_stmt_execute($stmt)) {
                                // Redirect to login page
                                header("location: loginforhall.php");
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
        <title>Sign Up for Theatre Managers</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <style type="text/css">
                body {
                        font: 14px sans-serif;
                        align: center;
                }

                .wrapper {
                        width: 450px;
                        padding: 20px;
                        /* align: center; */
                }
        </style>
</head>

<body>
        <div class="wrapper">
                <h2>Sign Up for Theatre Managers</h2>
                <p>Please fill this form to create an account. <br>(This is only for a new movie hall and will require authorization by the admin)</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                                <label>Hall's Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                                <span class="help-block"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                                <span class="help-block"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                                <span class="help-block"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                                <span class="help-block"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Submit">
                                <input type="reset" class="btn btn-default" value="Reset">
                        </div>
                        <p>Already have an account? <a href="loginforhall.php">Login here</a>.</p>
                        <p>Want to signup for a regular user? <a href="register.php">Sign Up here</a>.</p>
                </form>
        </div>
</body>

</html>