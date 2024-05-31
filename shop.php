<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);
    $product_quantity = 1; // Default quantity set to 1

    $stmt = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message[] = 'already added to cart!';
    } else {
        $stmt = $conn->prepare("INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdis", $user_id, $product_name, $product_price, $product_quantity, $product_image);
        $stmt->execute();
        $message[] = 'product added to cart!';
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Our Shop</h3>
   <p> <a href="home.php">Home</a> / Shop </p>
</div>

<section class="products">

   <h1 class="title">Latest Products</h1>

   <div class="box-container">

      <?php  
         $select_products = $conn->query("SELECT * FROM `products`");

         if ($select_products->num_rows > 0) {
            while ($fetch_products = $select_products->fetch_assoc()) {
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="uploaded_img/<?php echo htmlspecialchars($fetch_products['image']); ?>" alt="">
      <div class="name"><?php echo htmlspecialchars($fetch_products['name']); ?></div>
      <div class="price">₱<?php echo htmlspecialchars($fetch_products['price']); ?>/-</div>
      <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch_products['name']); ?>">
      <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($fetch_products['price']); ?>">
      <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($fetch_products['image']); ?>">
      <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
