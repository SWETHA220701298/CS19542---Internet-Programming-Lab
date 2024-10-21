<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDER ITEMS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style1.css">
</head>
<body>

    <div class="top-bar bg-dark text-white py-2 text-center">
        <span>ONLINE SHOPPING FOR FOOD</span>
        <span class="float-right mr-3">Order Online +91-892-808-5056</span>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-info">
        <a class="navbar-brand" href="#"><img src="logo.jpg" alt="JKS Logo" width="80"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.html">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="food.html">Our Food</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.html">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="order.html">Order Now</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="checkout.html">Checkout</a>
                </li>
            </ul>
        </div>
    </nav>
</body>
</html>
<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Food Order"; // Make sure your database name is correct
$port = 3307; // Optional: Use the port if required, remove it if unnecessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $payment_method = $_POST['payment_method']; // Get the payment method

    // Get the cart items from the form (passed as JSON string in hidden input)
    $cartItems = json_decode($_POST['cartItems'], true); // Decode the JSON string to an array

    if (!empty($cartItems)) {
        $order_items = [];
        $total_price = 0;

        // Process each item in the cart
        foreach ($cartItems as $item) {
            $item_name = $item['title']; // Assumed key in the array
            $item_cost = floatval($item['unitPrice']); // Remove currency symbol for calculation
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1; // Add quantity support

            $item_total = $item_cost * $quantity;

            // Add to the order summary
            $order_items[] = "$item_name x $quantity ($" . number_format($item_total, 2) . ")";
            $total_price += $item_total;
        }

        // Convert order items to a string for storage
        $order_items_str = implode(", ", $order_items);

        // Insert order into the database
        $sql = "INSERT INTO orders (customer_name, customer_phone, customer_address, order_items, total_price, payment_method)
                VALUES ('$customer_name', '$customer_phone', '$customer_address', '$order_items_str', '$total_price', '$payment_method')";

        if ($conn->query($sql) === TRUE) {
            echo "<h2>Order placed successfully!</h2>";
            echo "<p>Thank you, $customer_name, for your order.</p>";
            echo "<p><strong>Order Summary:</strong></p>";
            echo "<ul>";
            foreach ($order_items as $item) {
                echo "<li>$item</li>";
            }
            echo "</ul>";
            echo "<p>Total Price: $" . number_format($total_price, 2) . "</p>";
            echo "<p>Payment Method: $payment_method</p>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "<p>No items in the cart to process.</p>";
    }
} else {
    echo "<p>Invalid request method. Please submit the form properly.</p>";
}

$conn->close();
?>
