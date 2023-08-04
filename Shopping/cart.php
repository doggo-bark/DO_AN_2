<?php
// Start a session
session_set_cookie_params(0);
session_start();
if (isset($_SESSION['username'])) {
    echo "Logged in as: " . $_SESSION['username'];
}

include "connect.php";

// Get the product ID from the request
$product_id = $_POST['product_id'];

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // The user is logged in, store the cart data in the database
    
    // Check if the product is already in the user's cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $db->prepare($sql);  //stmt stand for 'statement'
    $stmt->bind_param("ii", $_SESSION['user_id'], $product_id); // i for integer, s for string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the quantity of the product in the cart
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
        $stmt->execute();
    } else {
        // Insert a new row into the cart table
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
        $stmt->execute();
    }
} else {
    // The user is not logged in, store the cart data in the session
    
    // Check if the cart session variable is set
    if (!isset($_SESSION['cart'])) {
        // Initialize the cart session variable as an empty array
        $_SESSION['cart'] = array();
    }

    // Check if the product is already in the user's cart
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        // Update the quantity of the product in the cart
        $_SESSION['cart'][$product_id]++; 
    } else {
        // Add the product to the user's cart
        $_SESSION['cart'][$product_id] = 1;
  
    }
}

echo "<a href='index.php'>Back to homepage</a>  |  ";
echo "<a href='display_cart.php'>List cart items</a>";


// Close the database connection
$db->close();
header('Location: display_cart.php'); 

?>


