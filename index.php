<?php
session_start();

$host = 'localhost';
$db = 'orders';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order = $_POST['order'];
    $quantity = (int)$_POST['quantity']; // Ensure quantity is an integer
    $cash = (float)$_POST['cash']; // Ensure cash is a float

    $stmt = $pdo->prepare("SELECT price FROM menu WHERE item_name = :item_name");
    $stmt->execute(['item_name' => $order]);
    $price = $stmt->fetchColumn();

    $total = $price * $quantity;

    if ($cash < $total) {
        $message = "Sorry, your balance is not enough.";
        header("Location: balance.php?message=" . urlencode($message));
        exit();
    } else {
        $change = $cash - $total;
        $timestamp = date("Y-m-d H:i:s");

        $_SESSION['order_details'] = [
            'order' => $order,
            'quantity' => $quantity,
            'total' => $total,
            'cash' => $cash,
            'change' => $change,
            'timestamp' => $timestamp
        ];

        header('Location: orderDetails.php');
        exit();
    }
}

$stmt = $pdo->query("SELECT item_name, price FROM menu");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional external CSS -->
</head>
<body>
    <div class="container">
        <h1>Order Here</h1>
        <table border="1">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <br>

        <form method="post">
            <label for="order">Select an order:</label>
            <select name="order" id="order" required>
                <?php foreach ($menuItems as $item): ?>
                    <option value="<?php echo htmlspecialchars($item['item_name']); ?>">
                        <?php echo htmlspecialchars($item['item_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" required>
            <br><br>
            <label for="cash">Cash:</label>
            <input type="text" name="cash" id="cash" required pattern="^\d+(\.\d{1,2})?$" title="Please enter a valid amount">
            <br><br>
            <button type="submit">Submit</button>
        </form>

        <?php if (isset($_SESSION['order_details'])): ?>
            <h2>Order Details</h2>
            <p>
                <?php
                $details = $_SESSION['order_details'];
                echo nl2br(htmlspecialchars("Order: {$details['order']}\nQuantity: {$details['quantity']}\nTotal: {$details['total']}\nCash: {$details['cash']}\nChange: {$details['change']}\nTimestamp: {$details['timestamp']}"));
                ?>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
