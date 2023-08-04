<?php
include('connect.php');

if (isset($_POST['username']) && isset($_POST['name'])&& isset($_POST['email']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address");
    }

    // Sanitize the username and email address
    //"admin<script>alert('Hello!');</script>"
    $username = filter_var($username, FILTER_SANITIZE_STRING);  
    $name = filter_var($name, FILTER_SANITIZE_STRING); 
    //"john.doe@example.com<script>alert('Hello!');</script>"
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);  

    // Insert the new user into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, name, email, hashed_password) VALUES ('$username', '$name', '$email', '$hashed_password')";
    mysqli_query($db, $query);

    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
         label {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 10px;
         }
    </style>
</head>
<body>
    <form method="post" action="register.php">
        <label>Username:</label>
        <input type="text" name="username"><br><br>
        <label>Name:</label>
        <input type="text" name="name"><br><br>
        <label>Email:</label>
        <input type="text" name="email"><br><br>
        <label>Password:</label>
        <input type="password" name="password"><br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
