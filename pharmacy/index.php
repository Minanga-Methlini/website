<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../classes/MedicineManager.php';

$medicineManager = new MedicineManager($pdo);
$categories = $medicineManager->getCategories();

// Handle search and category filter
$searchTerm = $_GET['search'] ?? '';
$categoryId = $_GET['category'] ?? null;

if ($searchTerm) {
    $medicines = $medicineManager->searchMedicines($searchTerm);
} else {
    $medicines = $medicineManager->getMedicinesByCategory($categoryId);
}

// Handle add to cart
if ($_POST['action'] ?? '' === 'add_to_cart' && isLoggedIn()) {
    $medicineId = $_POST['medicine_id'];
    $quantity = $_POST['quantity'] ?? 1;
    $medicineManager->addToCart($_SESSION['user_id'], $medicineId, $quantity);
    $success = "Medicine added to cart successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare Pharmacy - Online Medicine Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-pills mr-2"></i>Medicare Pharmacy
                    </h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>Home
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="cart.php" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-shopping-cart mr-1"></i>Cart
                        </a>
                        <a href="../<?= $_SESSION['role'] ?>/dashboard.php" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-user mr-1"></i>Dashboard
                        </a>
                        <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <form method="GET" class="flex">
                        <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" 
                               placeholder="Search medicines..." 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-r-lg hover:bg-blue-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select onchange="window.location.href='?category=' + this.value" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= $categoryId == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Shop by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach ($categories as $category): ?>
                    <a href="?category=<?= $category['id'] ?>" 
                       class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition-shadow">
                        <div class="text-3xl text-blue-500 mb-2">
                            <?php
                            $icons = [
                                'Tablets' => 'fas fa-pills',
                                'Capsules' => 'fas fa-capsules',
                                'Syrups' => 'fas fa-flask',
                                'Inhalers' => 'fas fa-lungs',
                                'Injections' => 'fas fa-syringe',
                                'Ointments' => 'fas fa-hand-holding-medical'
                            ];
                            echo '<i class="' . ($icons[$category['name']] ?? 'fas fa-pills') . '"></i>';
                            ?>
                        </div>
                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($category['name']) ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Medicines Grid -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                <?= $searchTerm ? "Search Results for \"$searchTerm\"" : ($categoryId ? "Medicines" : "All Medicines") ?>
            </h2>
            
            <?php if (empty($medicines)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl text-gray-500">No medicines found</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($medicines as $medicine): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($medicine['name']) ?></h3>
                                    <?php if ($medicine['prescription_required']): ?>
                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Rx</span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($medicine['category_name']) ?></p>
                                <p class="text-sm text-gray-700 mb-3"><?= htmlspecialchars($medicine['description']) ?></p>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-2xl font-bold text-green-600">$<?= number_format($medicine['price'], 2) ?></span>
                                    <span class="text-sm text-gray-500"><?= htmlspecialchars($medicine['dosage']) ?></span>
                                </div>
                                
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm text-gray-600">Stock: <?= $medicine['stock_quantity'] ?></span>
                                    <span class="text-sm text-gray-600"><?= htmlspecialchars($medicine['manufacturer']) ?></span>
                                </div>
                                
                                <?php if (isLoggedIn()): ?>
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="action" value="add_to_cart">
                                        <input type="hidden" name="medicine_id" value="<?= $medicine['id'] ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?= $medicine['stock_quantity'] ?>" 
                                               class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                        <button type="submit" 
                                                class="flex-1 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors"
                                                <?= $medicine['stock_quantity'] == 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-cart-plus mr-1"></i>
                                            <?= $medicine['stock_quantity'] == 0 ? 'Out of Stock' : 'Add to Cart' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="../login.php" class="block w-full bg-gray-500 text-white text-center py-2 px-4 rounded hover:bg-gray-600">
                                        Login to Purchase
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
