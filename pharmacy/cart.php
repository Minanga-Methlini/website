<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../classes/MedicineManager.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$medicineManager = new MedicineManager($pdo);

// Handle cart actions
if ($_POST['action'] ?? '' === 'update_quantity') {
    $medicineId = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $medicineManager->updateCartQuantity($_SESSION['user_id'], $medicineId, $quantity);
    header('Location: cart.php');
    exit;
}

if ($_POST['action'] ?? '' === 'remove_item') {
    $medicineId = $_POST['medicine_id'];
    $medicineManager->removeFromCart($_SESSION['user_id'], $medicineId);
    header('Location: cart.php');
    exit;
}

$cartItems = $medicineManager->getCartItems($_SESSION['user_id']);
$cartTotal = $medicineManager->getCartTotal($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Medicare Pharmacy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-pills mr-2"></i>Medicare Pharmacy
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-store mr-1"></i>Shop
                    </a>
                    <a href="../<?= $_SESSION['role'] ?>/dashboard.php" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-user mr-1"></i>Dashboard
                    </a>
                    <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">
            <i class="fas fa-shopping-cart mr-2"></i>Shopping Cart
        </h1>

        <?php if (empty($cartItems)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">Your cart is empty</h2>
                <p class="text-gray-500 mb-6">Add some medicines to get started!</p>
                <a href="index.php" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-store mr-2"></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="flex items-center justify-between border-b border-gray-200 py-4 last:border-b-0">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                                <p class="text-gray-600">$<?= number_format($item['price'], 2) ?> each</p>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="action" value="update_quantity">
                                    <input type="hidden" name="medicine_id" value="<?= $item['medicine_id'] ?>">
                                    <label class="text-sm text-gray-600">Qty:</label>
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                           min="1" max="10" 
                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                           onchange="this.form.submit()">
                                </form>
                                
                                <div class="text-right">
                                    <p class="font-semibold text-lg">$<?= number_format($item['subtotal'], 2) ?></p>
                                </div>
                                
                                <form method="POST">
                                    <input type="hidden" name="action" value="remove_item">
                                    <input type="hidden" name="medicine_id" value="<?= $item['medicine_id'] ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xl font-semibold">Total: $<?= number_format($cartTotal, 2) ?></span>
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="index.php" class="flex-1 bg-gray-500 text-white text-center py-3 px-6 rounded-lg hover:bg-gray-600">
                            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                        </a>
                        <button class="flex-1 bg-green-500 text-white py-3 px-6 rounded-lg hover:bg-green-600">
                            <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
