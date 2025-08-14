<?php
class MedicineManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all medicine categories
    public function getCategories() {
        $stmt = $this->pdo->query("SELECT * FROM medicine_categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get medicines by category
    public function getMedicinesByCategory($categoryId = null) {
        if ($categoryId) {
            $stmt = $this->pdo->prepare("
                SELECT m.*, mc.name as category_name 
                FROM medicines m 
                JOIN medicine_categories mc ON m.category_id = mc.id 
                WHERE m.category_id = ? AND m.status = 'active'
                ORDER BY m.name
            ");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->pdo->query("
                SELECT m.*, mc.name as category_name 
                FROM medicines m 
                JOIN medicine_categories mc ON m.category_id = mc.id 
                WHERE m.status = 'active'
                ORDER BY m.name
            ");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Search medicines
    public function searchMedicines($searchTerm) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, mc.name as category_name 
            FROM medicines m 
            JOIN medicine_categories mc ON m.category_id = mc.id 
            WHERE (m.name LIKE ? OR m.description LIKE ?) AND m.status = 'active'
            ORDER BY m.name
        ");
        $searchTerm = "%$searchTerm%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get single medicine
    public function getMedicine($id) {
        $stmt = $this->pdo->prepare("
            SELECT m.*, mc.name as category_name 
            FROM medicines m 
            JOIN medicine_categories mc ON m.category_id = mc.id 
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Add to cart
    public function addToCart($userId, $medicineId, $quantity = 1) {
        // Check if item already in cart
        $stmt = $this->pdo->prepare("SELECT * FROM shopping_cart WHERE user_id = ? AND medicine_id = ?");
        $stmt->execute([$userId, $medicineId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity
            $stmt = $this->pdo->prepare("UPDATE shopping_cart SET quantity = quantity + ? WHERE user_id = ? AND medicine_id = ?");
            return $stmt->execute([$quantity, $userId, $medicineId]);
        } else {
            // Add new item
            $stmt = $this->pdo->prepare("INSERT INTO shopping_cart (user_id, medicine_id, quantity) VALUES (?, ?, ?)");
            return $stmt->execute([$userId, $medicineId, $quantity]);
        }
    }
    
    // Get cart items
    public function getCartItems($userId) {
        $stmt = $this->pdo->prepare("
            SELECT sc.*, m.name, m.price, m.image, (sc.quantity * m.price) as subtotal
            FROM shopping_cart sc
            JOIN medicines m ON sc.medicine_id = m.id
            WHERE sc.user_id = ?
            ORDER BY sc.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update cart quantity
    public function updateCartQuantity($userId, $medicineId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $medicineId);
        }
        
        $stmt = $this->pdo->prepare("UPDATE shopping_cart SET quantity = ? WHERE user_id = ? AND medicine_id = ?");
        return $stmt->execute([$quantity, $userId, $medicineId]);
    }
    
    // Remove from cart
    public function removeFromCart($userId, $medicineId) {
        $stmt = $this->pdo->prepare("DELETE FROM shopping_cart WHERE user_id = ? AND medicine_id = ?");
        return $stmt->execute([$userId, $medicineId]);
    }
    
    // Get cart total
    public function getCartTotal($userId) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(sc.quantity * m.price) as total
            FROM shopping_cart sc
            JOIN medicines m ON sc.medicine_id = m.id
            WHERE sc.user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>
