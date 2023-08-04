<head>
    <style>
    form {
    width: 500px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f4f4f4;
    }

    form input[type="text"],
    form input[type="email"],
    form textarea {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    form input[type="submit"] {
        width: 100%;
        padding: 14px 20px;
        margin: 8px 0;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    form input[type="submit"]:hover {
        background-color: #45a049;
    }

    h1 {
            background-color: #4CAF99;
            color: white;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Checkout Form</h1>
</body>


<?php
session_set_cookie_params(0);
session_start();
include "connect.php";
if (isset($_SESSION['username'])) {
    echo "Logged in as: " . $_SESSION['username'];
    echo "<form action='logout.php' method='post'>";
    echo "<input type='submit' value='Logout'>";
    echo "</form>";
}



// Check if the form has been submitted
if (!isset($_POST['submit'])) {
    // Display the checkout form to the user
    echo "<form action='checkout.php' method='post'>";
    echo "<label for='name'>Name:</label>";
    echo "<input type='text' name='name' id='name' required>";
    echo "<br>";
    echo "<label for='email'>Email:</label>";
    echo "<input type='email' name='email' id='email' required>";
    echo "<br>";
    echo "<label for='address'>Shipping Address:</label>";
    echo "<textarea name='address' id='address' required></textarea>";
    echo "<br>";
    echo "<input type='submit' name='submit' value='Place Order'>";
    echo "</form>";
}  






/////////  If user is not logged in, then handle checkout with session
if (isset($_POST['submit']) && !isset($_SESSION['user_id']))
{
        // Get the user information from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
    
        // Calculate the total cost of the order
        $total_cost = 0;
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Query the database for the product price
            $sql = "SELECT price FROM products WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $total_cost += $row['price'] * $quantity;
        }
    
        // Insert a new row into the orders table
        $sql = "INSERT INTO orders (user_id, date, total) VALUES (?, NOW(), ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("id", $_SESSION['user_id'], $total_cost);
        $stmt->execute();
        $order_id = $stmt->insert_id;   //insert_id property returns the ID generated by an insert or update statement
                                        //that is to retrieve ID of the newly created order
    
        // Insert rows into the order_items table
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            // Query the database for the product price
            $sql = "SELECT price FROM products WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $price = $row['price'];
    
            // Insert a new row into the order_items table
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price); //d for double
            $stmt->execute();
        }

        // Clear the shopping cart
        unset($_SESSION['cart']); //unset function is used to destroy a variable
    
        // Display a confirmation message to the user
        echo "Thank you for your purchase! Your order has been received and is being processed. ";   
        
}







/////////   If user is logged in, then handle checkout with database
if (isset($_POST['submit']) && isset($_SESSION['user_id'])){
        // Get the user information from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        $address = $_POST['address'];

        // Calculate the total cost of the order
        $total_cost = 0;

        // Query the database for the contents of the user's shopping cart
        $sql = "SELECT p.price, c.quantity, c.product_id FROM products p JOIN cart c ON p.id = c.product_id WHERE c.user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $total_cost += $row['price'] * $row['quantity'];
        }

        // Insert a new row into the orders table
        $sql3 = "INSERT INTO orders (user_id, date, total) VALUES (?, NOW(), ?)";
        $stmt3 = $db->prepare($sql3);
        $stmt3->bind_param("id", $_SESSION['user_id'], $total_cost);
        $stmt3->execute();
        $order_id = $stmt3->insert_id;

        // Insert a new row into the order_items table
        $sql2 = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        while ($row = $result->fetch_assoc()) {
            $stmt2 = $db->prepare($sql2);
            $stmt2->bind_param("iiid", $order_id, $row['product_id'], $row['quantity'], $row['price']);
            $stmt2->execute();
        }
      
        // Clear the shopping cart by deleting rows from the cart table
        $sql4 = "DELETE FROM cart WHERE user_id = ?";
        $stmt4 = $db->prepare($sql4);
        $stmt4->bind_param("i", $_SESSION['user_id']);
        $stmt4->execute();

       
        // Display a confirmation message to the user
        echo "Thank you for your purchase! Your order has been received and is being processed. ";
}

$db->close();
?>