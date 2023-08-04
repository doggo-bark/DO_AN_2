
<head>
    <title>Shopping Cart</title>
    <style>
    .cart-item {
    padding: 10px;
    margin: 10px;
   
    border: 1px solid #ccc;
    }
   
    .cart-item:hover {
    background-color: #f5f5f5;
    }

    .cart-item p {
        font-size: 14px;
        color: #666;
    }
   
    </style>
</head>

<body>
    <header>
        <h1>Shopping Cart</h1>
    </header>

</body>

<?php

// end the session whenever you disconnect from the server
// set the lifetime of the session cookie to 0, which means that 
// the cookie will expire when the browser is closed
session_set_cookie_params(0); 

// Start a session
session_start();
if (isset($_SESSION['username'])) {
    echo "Logged in as: " . $_SESSION['username'];
    echo "<form action='logout.php' method='post'>";
    echo "<input type='submit' value='Logout'>";
    echo "</form>";
}
include "connect.php";

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // The user is logged in, retrieve the cart data from the database

    // Query the database for the contents of the user's shopping cart
    $sql = "SELECT p.name, p.price, c.quantity FROM products p JOIN cart c ON p.id = c.product_id WHERE c.user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the contents of the shopping cart
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div class='cart-item'>";
            echo "<h2>" . $row["name"] . "</h2>";
            echo "<p>Price: $" . $row["price"] . "</p>";
            echo "<p>Quantity: " . $row["quantity"] . "</p>";
            echo "</div>";
        }
        // Add a link to direct the user to the checkout page
        echo "<a href='checkout.php'>Proceed to Checkout</a>";
    } else {
        echo "Your shopping cart is empty";
    }
} else {
    // The user is not logged in, retrieve the cart data from the session
    
    // Check if the cart session variable is set
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Display the contents of the shopping cart
        foreach ($_SESSION['cart'] as $product_id => $quantity) {   // symbol => is used to associate a key with a value
                                                                    // assign array key to $product_id, value to $quantity    
            // Query the database for the product details
            $sql = "SELECT name, price FROM products WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            echo "<div class='cart-item'>";
            echo "<h2>" . $row["name"] . "</h2>";
            echo "<p>Price: $" . $row["price"] . "</p>";
            echo "<p>id: " . $product_id . "</p>";
            
            echo "<p>Quantity: " . $quantity . "</p>";
            echo "</div>";
        }
        // Add a link to direct the user to the checkout page
        echo "<a href='checkout.php'>Proceed to Checkout</a>";
    } else {
        echo "Your shopping cart is empty";
    }
}

// Close the database connection
$db->close();
?>
