<?php
session_start();

// Simulate appointment ID and amount
if (!isset($_SESSION['appointment_id'])) {
    $_SESSION['appointment_id'] = rand(1000, 9999);
}
if (!isset($_SESSION['amount'])) {
    $_SESSION['amount'] = "2500.00";
}

// CSRF token for security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle fake payment submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token!";
    } else {
        $success = "Payment Successful! Appointment ID: " . $_SESSION['appointment_id'];
        // Mark appointment as "paid" in session (simulate DB)
        $_SESSION['paid'] = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dummy Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen py-8 min-h-screen flex items-center justify-center" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md text-center">
        <h2 class="text-2xl font-bold mb-4">Appointment Payment</h2>
        <p class="mb-4">Amount to pay: <strong>LKR <?php echo $_SESSION['amount']; ?></strong></p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Pay Now</button>
            </form>
        <?php else: ?>
            <a href="dashboard.php" class="inline-block mt-4 text-blue-600 hover:underline">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>
