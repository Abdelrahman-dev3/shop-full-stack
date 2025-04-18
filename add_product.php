<?php
session_start();
include "function.php";

if (!isset($_SESSION['user_id'])) {
    header("location:login.php");
    exit();
}
$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $name     = trim(strip_tags($_POST['name']))                          ?? '';
    $price    = floatval(strip_tags(htmlspecialchars($_POST['price'])))   ?? '';
    $desc     = trim(strip_tags(htmlspecialchars($_POST['description']))) ?? '';
    $country  = trim(strip_tags(htmlspecialchars($_POST['country'])))     ?? '';
    $category = $_POST['category'] ?? 0;
    $status   = $_POST['status']   ?? 0;

    if (empty($name)) {
        $errors[] = "Product Name is required";
    }
    if (strlen($name) <= 4) {
        $errors[] = "Product Name must be 4 characters or more";
    }

    if (strlen($desc) <= 10) {
        $errors[] = "Product Description must be 10 characters or more";
    }

    if (empty($_FILES['image']['name'])) {
        $errors[] = "The Image Is Requierd";
    }

    $file = $_FILES['image'];
    //  init file
    $file_name = $file['name'];
    $file_size = $file['size'];
    //  get file extension
    $file_name_parts = explode('.', $file_name);
    $file_ext = strtolower(end($file_name_parts));
    //  image allowed extensions
    $imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp", "svg", "tiff", "tif", "ico", "jfif", "avif"];
    if(in_array($file_ext, $imageExtensions)){
        if($file_size > 5000000){
            $errors[] = "Image size must be less than 5MB";
        }else{
            //  new random name
            $new_name = uniqid('', true) . '.' . $file_ext;
            move_uploaded_file($file['tmp_name'], "uploudes/images/" . $new_name);
        }
    }
    
    if(!in_array($file_ext, $imageExtensions) && !empty($_FILES['image']['name'])){
        $errors[] = "Invalid image Extensions";
    }

    $clean_name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if ($_POST['name'] !== $clean_name) {
        $errors[] = "Product Name is invalid";
    }

    if (empty($desc)) {
        $errors[] = "Product Description is required";
    }

    if ($price <= 0 ) {
        $errors[] = "Product Price must be greater than 0";
    }
    if (empty($price)) {
        $errors[] = "Product Price is required";
    }

    if (empty($country)) {
        $errors[] = "Product Country is required";
    }

    if ($category == 0) {
        $errors[] = "Product Category is required";
    }

    if ($status == 0) {
        $errors[] = "Product Status is required";
    }

    if (empty($errors)) {
        try {
            $stmt_set = $dbcon->prepare("INSERT INTO `items`( `name`, `description`, `price`, `country` , `image` ,`status`,`cat_id`,`member_id` ) VALUES ( ? , ? , ? , ? , ? , ? , ? , ? )");
            $add = $stmt_set->execute([$name, $desc,$price, $country , $new_name , $status, $category, $_SESSION['user_id']]);
            if ($add) {
                $success = true;
            } else {
                $errors[] = "An error occurred while adding the product";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }                
    }
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - ShopMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="layout/css/add_product.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
</head>
<body>
<nav class="navbar">
        <div class="  ms-4 logo">Shop<span>Master</span></div>
        <a style="color: white; text-decoration: none;margin-left: -185px;" href="cart.php" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <?php
            $stmt = $dbcon->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $cart_count = $stmt->fetch()['count'];
            if ($cart_count > 0):
            ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php
    foreach (getcat() as $cat) {
        echo " <li><a href='categories.php?id=" . $cat['id'] . "&catname=" .  $cat['name'] . "'>{$cat['name']}</a></li>";
    }
            ?>
            
        </ul>
        <div class="icons">
        <?php if (isset($_SESSION['user_id'])): ?>
            <button type="button" class="btn btn-outline-info" onclick="window.location.href='profile.php'">Profile</button>
            <button type="button" class="btn btn-outline-danger me-5 ms-2" onclick="window.location.href='logout2.php'">Logout</button>
        <?php else: ?>
            <button type="button" class="btn btn-outline-info" onclick="window.location.href='login.php'">Login</button>
            <button type="button" class="btn btn-outline-primary me-5 ms-2" onclick="window.location.href='signup.php'">Sign</button>
        <?php endif; ?>
        </div>
    </nav>

    <div class="add-product-container">
        <div class="add-product-card">
            <div class="add-product-header">
                <i class="fas fa-plus-circle"></i>
                <h2>Add New Product</h2>
                <p>Add your product to your store</p>
            </div>

            <div class="add-product-form">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Product added successfully
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" id="name" name="name" placeholder=" " required>
                            <label for="name">Product Name</label>
                        </div>

                        <div class="form-group">
                            <input type="number" id="price" name="price" step="0.01" placeholder=" " required>
                            <label for="price">Product Price (USD)</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea id="description" name="description" placeholder=" " required></textarea>
                        <label for="description">Product Description</label>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <select id="category" name="category" required>
                                <option value="0">Choose Category</option>
                                <?php
                                $categories = getcat();
                                foreach ($categories as $cat) {
                                    echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" id="country" name="country" placeholder=" " required>
                            <label for="country">Product Country</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <select id="status" name="status" required>
                            <option value="0">Status</option>
                            <option value="New">New</option>
                            <option value="Used">Used</option>
                            <option value="Old">Old</option>
                        </select>
                    </div>


                    <div class="image-upload">
                        <div class="upload-area" id="dropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag and drop the image here or click to select</p>
                            <input type="file" id="image" name="image" accept="image/*" >
                        </div>
                        <div class="image-preview" id="imagePreview"></div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus"></i>
                        Add Product
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const dropZone = document.getElementById("dropZone");
    const fileInput = document.getElementById("image");
    const imagePreview = document.getElementById("imagePreview");

    // عند الضغط على منطقة الرفع
    dropZone.addEventListener("click", () => {
        console.log("clicked");
        fileInput.click();
    });

    // عند سحب وإفلات الصورة
    dropZone.addEventListener("dragover", (event) => {
        event.preventDefault();
        dropZone.classList.add("drag-over");
    });

    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("drag-over");
    });

    dropZone.addEventListener("drop", (event) => {
        event.preventDefault();
        dropZone.classList.remove("drag-over");

        const file = event.dataTransfer.files[0];
        if (file) {
            previewImage(file);
        }
    });

    // عند اختيار صورة من الكمبيوتر
    fileInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            previewImage(file);
        }
    });

    // دالة عرض الصورة
    function previewImage(file) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function (event) {
            imagePreview.innerHTML = `<img src="${event.target.result}" alt="Preview" style="max-width: 100%; height: auto; border-radius: 10px;"/>`;
        };
    }
});
</script>
</body>
</html>