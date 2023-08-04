<!DOCTYPE html>
<html>
<head>
    <title>SecureCart</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
        }
        .product {
        
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        
        }
        .product img {
            max-width: 200px;
            max-height: 200px;
        }
        
        .product img, .product h2, .product p, button {
        margin: 0 10px;
        }

        input {
        font-size: 14px;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        }
        input:hover {
            background-color: #3e8e41;
        }


    </style>
</head>
<body>
    
        <?php
        session_set_cookie_params(0);
        include "connect.php";
        session_start();
        if (isset($_SESSION['username'])) {
            echo "Logged in as: " . $_SESSION['username'];
            echo "<form action='logout.php' method='post'>";
            echo "<input type='submit' value='Logout'>";
            echo "</form>";
        } else {
            echo "<form action='login.php' method='post'>";
            echo "<input type='submit' value='Login'>";
            echo "</form>";
        }


        // Query the database for a list of products
        $sql = "SELECT * FROM products";
        $result = $db->query($sql);

        // Display the list of products
        if ($result->num_rows > 0) {
            // $row=mysqli_fetch_array($result);    $row[0]
            // $row=mysqli_fetch_assoc($result);    $row['id']
            //associative array is an array that uses keys instead of indexes
            while($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<h2>" . $row["name"] . "</h2>";
                echo "<p>" . $row["description"] . "</p>";
                echo "<p>Price: $" . $row["price"] . "</p>";
                
                //the "Add to Cart" button send a request to the server when clicked
                echo "<form action='cart.php' method='post'>";
                echo "<input type='hidden' name='product_id' value='" . $row["id"] . "'>";
                echo "<input type='submit' value='Add to Cart'>";
                echo "</form>";

                echo "<img src='images/" . $row["image"] . "' alt='" . $row["name"] . "'>";
                echo "</div>";
                
            }
        } else {
            //$result would be empty, and $row would be null
            echo "No products found bruhhh";
        }

        // Close the database connection
        $db->close();
        ?>
   
</body>
</html>


