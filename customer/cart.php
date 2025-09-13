<?php
session_start();
require_once "dbcontroller.php";
$db_handle = new DBController();

// Handle cart actions
if(!empty($_GET["action"])) {
    switch($_GET["action"]) {
        case "remove":
            if(!empty($_SESSION["cart_item"])) {
                foreach($_SESSION["cart_item"] as $k => $v) {
                    if($_GET["code"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    if(empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
        case "update":
            if(!empty($_SESSION["cart_item"]) && isset($_POST["quantity"])) {
                foreach($_POST["quantity"] as $code => $qty) {
                    if($qty > 0) {
                        $_SESSION["cart_item"][$code]["quantity"] = $qty;
                    } else {
                        unset($_SESSION["cart_item"][$code]);
                    }
                }
                if(empty($_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"]);
                }
            }
            break;
    }
}

$cart_count = 0;
$cart_total = 0;
if(isset($_SESSION["cart_item"])) {
    foreach($_SESSION["cart_item"] as $item) {
        $cart_count += $item["quantity"];
        $cart_total += ($item["price"] * $item["quantity"]);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Shopping Cart</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

.header {
    background-color: #333;
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    margin: 0;
}

.products-link {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

.products-link:hover {
    background-color: #218838;
}

.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.cart-actions {
    display: flex;
    gap: 10px;
}

.empty-cart-btn {
    background-color: #dc3545;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.empty-cart-btn:hover {
    background-color: #c82333;
}

.update-cart-btn {
    background-color: #007bff;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.update-cart-btn:hover {
    background-color: #0056b3;
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.cart-table th,
.cart-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.cart-table th {
    background-color: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.product-name {
    font-weight: bold;
    color: #333;
}

.quantity-input {
    width: 60px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
    text-align: center;
}

.remove-btn {
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
}

.remove-btn:hover {
    background-color: #c82333;
}

.cart-total {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
    text-align: right;
}

.total-amount {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin: 10px 0;
}

.checkout-section {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.checkout-btn {
    background-color: #28a745;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
}

.checkout-btn:hover {
    background-color: #218838;
}

.empty-cart {
    text-align: center;
    padding: 50px;
    color: #666;
}

.empty-cart h2 {
    color: #999;
    margin-bottom: 20px;
}

.continue-shopping {
    background-color: #007bff;
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    border-radius: 5px;
    display: inline-block;
}

.continue-shopping:hover {
    background-color: #0056b3;
}
</style>
<script>
function confirmEmpty() {
    return confirm('Are you sure you want to empty your cart?');
}

function validateQuantity(input) {
    if (input.value < 0) {
        input.value = 0;
    }
}
</script>
</head>
<body>
<div class="header">
    <h1>Shopping Cart</h1>
    <a href="customer_dashboard.php" class="products-link">Continue Shopping</a>
</div>

<div class="container">
    <?php if(!empty($_SESSION["cart_item"])): ?>
    
    <div class="cart-header">
        <h2>Your Cart (<?php echo $cart_count; ?> items)</h2>
        <div class="cart-actions">
            <form method="post" action="cart.php?action=update" style="display: inline;">
                <?php foreach($_SESSION["cart_item"] as $k => $v): ?>
                    <input type="hidden" name="quantity[<?php echo $k; ?>]" value="<?php echo $v['quantity']; ?>">
                <?php endforeach; ?>
                <button type="submit" class="update-cart-btn" style="display: none;" id="updateBtn">Update Cart</button>
            </form>
            <a href="cart.php?action=empty" class="empty-cart-btn" onclick="return confirmEmpty()">Empty Cart</a>
        </div>
    </div>

    <form method="post" action="cart.php?action=update" id="cartForm">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($_SESSION["cart_item"] as $item): ?>
                <tr>
                    <td>
                        <?php if(!empty($item["image"])): ?>
                            <img src="<?php echo $item["image"]; ?>" alt="<?php echo $item["name"]; ?>" class="product-image">
                        <?php else: ?>
                            <div style="width: 80px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #999; font-size: 12px;">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td class="product-name"><?php echo $item["name"]; ?></td>
                    <td>TK <?php echo $item["price"]; ?></td>
                    <td>
                        <input type="number" 
                               name="quantity[<?php echo $item["code"]; ?>]" 
                               value="<?php echo $item["quantity"]; ?>" 
                               min="0" 
                               class="quantity-input"
                               onchange="showUpdateButton(); validateQuantity(this);">
                    </td>
                    <td>TK <?php echo number_format(($item["price"] * $item["quantity"]), 2); ?></td>
                    <td>
                        <a href="cart.php?action=remove&code=<?php echo $item["code"]; ?>" 
                           class="remove-btn" 
                           onclick="return confirm('Are you sure you want to remove this item?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="update-cart-btn" style="display: none;" id="updateFormBtn">Update Cart</button>
    </form>

    <div class="cart-total">
        <div>
            <strong>Total Items: <?php echo $cart_count; ?></strong>
        </div>
        <div class="total-amount">
            Total Amount: TK <?php echo number_format($cart_total, 2); ?>
        </div>
    </div>

    <div class="checkout-section">
        <h3>Ready to Checkout?</h3>
        <p>Review your items above and proceed to checkout when ready.</p>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
        <div style="margin-top: 15px;">
            <a href="customer_dashboard.php" class="continue-shopping">Add More Items</a>
        </div>
    </div>

    <?php else: ?>
    
    <div class="empty-cart">
        <h2>Your cart is empty</h2>
        <p>You haven't added any items to your cart yet.</p>
        <a href="customer_dashboard.php" class="continue-shopping">Start Shopping</a>
    </div>
    
    <?php endif; ?>
</div>

<script>
function showUpdateButton() {
    document.getElementById('updateFormBtn').style.display = 'inline-block';
}
</script>
</body>
</html>