<?php
// Handle add to cart action
if(!empty($_GET["action"])) {
    switch($_GET["action"]) {
        case "add":
            if(!empty($_POST["quantity"])) {
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                $select_product->execute([$_GET["code"]]);
                $productByCode = $select_product->fetch(PDO::FETCH_ASSOC);
                
                if($productByCode) {
                    $itemArray = array(
                        $productByCode["id"] => array(
                            'name' => $productByCode["name"],
                            'code' => $productByCode["id"],
                            'quantity' => $_POST["quantity"],
                            'price' => $productByCode["price"],
                            'image' => $productByCode["image"]
                        )
                    );
                    
                    if(!empty($_SESSION["cart_item"])) {
                        if(in_array($productByCode["id"], array_keys($_SESSION["cart_item"]))) {
                            foreach($_SESSION["cart_item"] as $k => $v) {
                                if($productByCode["id"] == $k) {
                                    if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                        $_SESSION["cart_item"][$k]["quantity"] = 0;
                                    }
                                    $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                                }
                            }
                        } else {
                            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                        }
                    } else {
                        $_SESSION["cart_item"] = $itemArray;
                    }
                    
                    echo '<div class="success-message">Product added to cart successfully!</div>';
                }
            }
            break;
    }
}

// Handle search
$searchQuery = "";
$whereClause = "";
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $whereClause = "WHERE name LIKE :search OR category LIKE :search";
}
?>

<!-- Cart and Search Section -->
<div class="cart-search-section">
    <div>
        <a href="cart.php" class="cart-link">
            View Cart 
            <?php if(isset($_SESSION["cart_item"]) && count($_SESSION["cart_item"]) > 0): ?>
                (<?php echo array_sum(array_column($_SESSION["cart_item"], 'quantity')); ?>)
            <?php endif; ?>
        </a>
    </div>
    
    <!-- Search Form -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Search products..." 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
               class="search-input">
        <button type="submit" class="search-button">
            Search
        </button>
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="customer_dashboard.php" class="clear-link">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Products Grid -->
<div class="products-container">
    <?php
    // Fetch products
    if(!empty($whereClause)) {
        $select_products = $conn->prepare("SELECT * FROM `products` $whereClause ORDER BY name ASC");
        $select_products->execute([':search' => "%{$searchQuery}%"]);
    } else {
        $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY name ASC");
        $select_products->execute();
    }
    
    if($select_products->rowCount() > 0) {
        echo '<div class="products-grid">';
        
        while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>
            <div class="product-card">
                <form method="post" action="customer_dashboard.php?action=add&code=<?php echo $fetch_products["id"]; ?>">
                    <div class="product-image-container">
                        <?php if(!empty($fetch_products["image"])): ?>
                            <img src="../images/<?php echo $fetch_products["image"]; ?>" alt="<?php echo $fetch_products["name"]; ?>" class="product-image">
                        <?php else: ?>
                            <span class="no-image-text">No Image</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-name">
                        <?php echo $fetch_products["name"]; ?>
                    </div>
                    
                    <div class="product-category">
                        <?php echo $fetch_products["category"]; ?>
                    </div>
                    
                    <div class="product-price">
                        TK <?php echo $fetch_products["price"]; ?>
                    </div>
                    
                    <div class="product-actions">
                        <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-input">
                        <input type="submit" value="Add to Cart" class="add-to-cart-btn">
                    </div>
                </form>
            </div>
    <?php
        }
        echo '</div>';
    } else {
        echo '<div class="no-products">';
        if(!empty($searchQuery)) {
            echo 'No products found matching your search.';
        } else {
            echo 'No products available.';
        }
        echo '</div>';
    }
    ?>
</div>