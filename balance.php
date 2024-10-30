<?php
if (!isset($_GET['message'])) {
    header('Location: index.php');
    exit();
}

$message = htmlspecialchars(trim($_GET['message']), ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insufficient Funds</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS for styling -->
</head>
<body>
    <div class="container">
        <h1>Insufficient Funds</h1>
        <p><?php echo $message; ?></p>
        <a href="index.php" class="btn">Go Back</a>
    </div>
</body>
</html>
