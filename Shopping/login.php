<?php
session_set_cookie_params(0); 
session_start();
include('connect.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    //mysqli_real_escape_string used to escape dangerous characters like ' "  ;
    //escape means to adding escape characters so it would be treated as data
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($db, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['hashed_password'])) {
            $_SESSION['user_id'] = $user['id']; //keep track of logged-in user
            $_SESSION['username'] = $user['username'];  //used to display current logged-in user
            header('Location: index.php'); //redirect user to index.php after logged in
        } else {
            echo "Invalid login credentials";
        }
    } else {
        echo "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        form {
            background-color: pink;
            padding: 20px;
            border-radius: 5px;
            margin: 0 auto;
            width: 30%;
            
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        form input{
            width: 100%;
         
        }
        
        button[type="submit"] {
            background-color: peru;
            color: black;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form method="post" action="login.php">
        <label>Username:</label>
        <input type="text" name="username"><br><br>
        <label>Password:</label>
        <input type="password" name="password"><br><br>
        <button type="submit">Login</button> or <a href="register.php">Register</a>
    </form>
</body>
</html>
