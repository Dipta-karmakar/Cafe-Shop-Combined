<?php
session_start();
require_once("dbController.php");
$db_handle = new DBController();

// Add to cart logic
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["quantity"]) && !empty($_GET["id"])) {
                $productId = intval($_GET["id"]);
                $quantity = intval($_POST["quantity"]);
                $product = $db_handle->runQuery("SELECT * FROM products WHERE id = $productId");
                if ($product && count($product) > 0) {
                    $itemArray = array(
                        $product[0]["id"] => array(
                            'name' => $product[0]["name"],
                            'id' => $product[0]["id"],
                            'quantity' => $quantity,
                            'price' => $product[0]["price"],
                            'image' => $product[0]["image"]
                        )
                    );
                    if (!empty($_SESSION["cart_item"])) {
                        if (array_key_exists($product[0]["id"], $_SESSION["cart_item"])) {
                            $_SESSION["cart_item"][$product[0]["id"]]["quantity"] += $quantity;
                        } else {
                            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                        }
                    } else {
                        $_SESSION["cart_item"] = $itemArray;
                    }
                }
            }
            break;
        case "remove":
            if (!empty($_SESSION["cart_item"]) && !empty($_GET["id"])) {
                $productId = intval($_GET["id"]);
                if (array_key_exists($productId, $_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"][$productId]);
                }
                if (empty($_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   
  
    <title>Shopping cart</title>

    <link href="addtocart.css"  type="text/css" rel="stylesheet">
</head>
<body>


<div id="shopping-cart">
    <div class="text-heading">Shopping Cart</div>
    <a id="btnEmpty" href="addToCart.php?action=empty">Empty Cart</a>
    <?php
    if (isset($_SESSION["cart_item"])) {
        $total_quantity = 0;
        $total_price = 0;
    ?>
    <table class="tbl-cart" cellpadding="10" cellspacing="1">
        <tbody>
            <tr>
                <th style="text-align:left;">Image</th>
                <th style="text-align:left;">Name</th>
                <th style="text-align:right;" width="5%">Quantity</th>
                <th style="text-align:right;" width="100%">Unit Price</th>
                <th style="text-align:right;" width="100%">Price</th>
                <th style="text-align:center;" width="5%">Remove</th>
            </tr>
            <?php foreach ($_SESSION["cart_item"] as $item) { 
                $item_price = $item["quantity"] * $item["price"];
            ?>
            <tr>
                <td><img src="../images/<?php echo $item['image']; ?>" class="cart-item-image" style="width:50px;" /></td>
                <td><?php echo htmlspecialchars($item["name"]); ?></td>
                <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                <td style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
                <td style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                <td style="text-align:center;">
                    <a href="addToCart.php?action=remove&id=<?php echo $item["id"]; ?>" class="btnRemoveAction">
                        <img src="../icon/icons8-coffee-shop-64.png" alt="Remove Item" style="width:20px;" />
                    </a>
                </td>
            </tr>
            <?php
                $total_quantity += $item["quantity"];
                $total_price += $item_price;
            } ?>
            <tr>
                <td colspan="2" align="right">Total:</td>
                <td align="right"><?php echo $total_quantity; ?></td>
                <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <?php } else { ?>
        <div class="no-records">Your Cart is Empty</div>
    <?php } ?>
</div>


<div id="product-grid">
    <div class="txt-heading">Products</div>
    <?php
    $product_array = $db_handle->runQuery("SELECT * FROM products ORDER BY id ASC");
    if (!empty($product_array)) {
        foreach ($product_array as $key => $value) {
    ?>
        <div class="product-item">
            <form method="post" action="addToCart.php?action=add&id=<?php echo $product_array[$key]["id"]; ?>">
                <div class="product-image">
                    <img src="./images/<?php echo $product_array[$key]["image"]; ?>" style="width:100px;" />
                </div>
                <div class="product-title"><?php echo htmlspecialchars($product_array[$key]["name"]); ?></div>
                <div class="product-price"><?php echo "$".$product_array[$key]["price"]; ?></div>
                <div class="cart-action">
                    <input type="number" class="product-quantity" name="quantity" value="1" min="1" size="2" />
                    <input type="submit" value="Add to Cart" class="btnAddAction" />
                </div>
            </form>
        </div>
    <?php
        }
    }
    ?>


</body>
</html>