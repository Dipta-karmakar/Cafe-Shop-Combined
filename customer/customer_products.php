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
                    
                    echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px; text-align: center;">Product added to cart successfully!</div>';
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
<div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0; padding: 0 20px;">
    <div>
        <a href="cart.php" style="background-color: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin-right: 15px;">
            View Cart 
            <?php if(isset($_SESSION["cart_item"]) && count($_SESSION["cart_item"]) > 0): ?>
                (<?php echo array_sum(array_column($_SESSION["cart_item"], 'quantity')); ?>)
            <?php endif; ?>
        </a>
    </div>
    
    <!-- Search Form -->
    <form method="GET" action="" style="display: flex; align-items: center;">
        <input type="text" name="search" placeholder="Search products..." 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
               style="padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px; margin-right: 10px;">
        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Search
        </button>
        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
            <a href="customer_dashboard.php" style="margin-left: 10px; color: #007bff; text-decoration: none;">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Products Grid -->
<div style="padding: 20px;">
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
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">';
        
        while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>
            <div style="border: 1px solid #cbcbcb; background-color: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <form method="post" action="customer_dashboard.php?action=add&code=<?php echo $fetch_products["id"]; ?>">
                    <div style="height: 155px; width: 100%; background-color: #f0f0f0; border: 1px solid #ddd; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                        <?php if(!empty($fetch_products["image"])): ?>
                            <img src="../images/<?php echo $fetch_products["image"]; ?>" alt="<?php echo $fetch_products["name"]; ?>" 
                                 style="max-height: 100%; max-width: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="color: #999;">No Image</span>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin: 15px 0 10px; font-size: 18px; font-weight: bold; color: #333;">
                        <?php echo $fetch_products["name"]; ?>
                    </div>
                    
                    <div style="color: #888; font-size: 14px; margin: 5px 0; text-transform: capitalize;">
                        <?php echo $fetch_products["category"]; ?>
                    </div>
                    
                    <div style="color: #666; font-size: 16px; margin: 10px 0;">
                        TK <?php echo $fetch_products["price"]; ?>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <input type="number" name="quantity" value="1" min="1" max="10" 
                               style="width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 3px; margin-right: 10px;">
                        <input type="submit" value="Add to Cart" 
                               style="background-color: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;">
                    </div>
                </form>
            </div>
    <?php
        }
        echo '</div>';
    } else {
        echo '<div style="text-align: center; color: #666; font-size: 18px; margin: 50px 0;">';
        if(!empty($searchQuery)) {
            echo 'No products found matching your search.';
        } else {
            echo 'No products available.';
        }
        echo '</div>';
    }
    ?>
</div>