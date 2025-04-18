<?php
ob_start();
session_start();
date_default_timezone_set('Africa/Cairo');
if (isset($_SESSION['username'])) {
    require '../includes/dbcon/pdo.php'; // connect with database
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    if ($page == 'home') {
        if (isset($_SESSION['message_add_err']) && $_SESSION['message_add_err'] != null) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({title: 'Error!', text: '" . implode(",",$_SESSION['message_add_err']) . ", please write a value ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
        unset($_SESSION['message_add_err']); 
        }

        if (isset($_SESSION['fe_d_com'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({title: 'Error!', text: There was a problem while deleting , please write a value ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
        unset($_SESSION['fe_d_com']); 
        }


        if (isset($_SESSION['message_add_item'])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Added Successfully', icon: 'success'});
                });
            </script>";
            unset($_SESSION['message_add_item']); 
        }

        if (isset($_SESSION['suc_d_com'])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Deleted Successfully', icon: 'success'});
                });
            </script>";
            unset($_SESSION['suc_d_com']); 
        }

        if (isset($_SESSION['up_ad'])) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Updated Successfully', icon: 'success'});
                });
            </script>";
            unset($_SESSION['up_ad']); 
        }
        //  start items
        $_SESSION['title'] ='Items';
        $_SESSION['manage.style'] = true;
        require 'startnav.php';
    ?>
<h1>Items</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Adding Date</th>
                <th>category</th>
                <th>useranme</th>
                <th>Control</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $stmt = $dbcon->prepare("SELECT
                                        items.item_id AS item_id
                                        ,items.name AS item_name
                                        ,items.description AS item_desc
                                        ,items.price AS item_price
                                        ,items.add_date AS date_add
                                        ,categories.name AS catagory
                                        ,users.username AS username
                                        ,items.aprove AS aprove
                                    FROM
                                        `items` 
                                    INNER JOIN
                                        categories ON items.cat_id = categories.id
                                    INNER JOIN
                                        users ON items.member_id = users.user_id
                                    ORDER BY
                                        items.add_date DESC");
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch()) {
                echo "<tr>
                <td>{$row['item_name']}</td>
                <td>{$row['item_desc']}</td>
                <td>{$row['item_price']}</td>
                <td>{$row['date_add']}</td>
                <td>{$row['catagory']}</td>
                <td>{$row['username']}</td>
                <td>
                <form action='?page=handle' method='POST'>
                    <input type='hidden' name='id' value='{$row['item_id']}'>
                    <input type='submit' name='edit' class='btn_ btn-success' value='Edit'>
                    <input type='submit' name='delete' class='btn_ btn-danger' value='Delete'>";
                    if ($row['aprove'] == 0) {
                    echo "<input type='submit' name='accept' class='btn_ btn-accept' value='Accept'>";
                    }
                    echo "</form>
                    </td>
                </tr>";
                    }
            }else{
                echo "<tr><td colspan='7' style='text-align:center;'>No data available</td></tr>";
            }
        ?>
        </tbody>
    </table>
    <form action="?page=add" method="post">
    <input type='submit' class="add-member" value='+ Add New Item'>
    </form>
</div>
</body>
</html>
<?php
    }elseif($page == 'add'){
# بس كدا 
        $_SESSION['title'] ='Add Item';
        require 'startnav.php';
        ?>
<h1 style='margin-top: 0;'>Add New Item</h1>
<form class="form_" action="?page=insert_add" method="POST" enctype="multipart/form-data">
    <div style="display: flex;" class="flex">
        <div style="width:78%;" class="p">
            <div class="form-grp item_inp_wid">
                <label for="name">Name</label>
                <input type="text" name="name" required >
            </div>
            <div class="form-grp item_inp_wid">
                <label for="name">Description</label>
                <input type="text" name="description" required>
            </div>
            <div class="form-grp item_inp_wid">
                <label for="name">Price</label>
                <input type="text" name="price" required>
            </div>
            <div class="form-grp item_inp_wid">
                <label for="name">Country</label>
                <input type="text"  name="country" required>
            </div>
        </div>
        <div class="image-upload">
            <div class="upload-area" id="dropZone">
                <div>
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag and drop the image here or click to select</p>
                </div>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="image-preview" id="imagePreview"></div>
        </div>
    </div>
    <div class="form-grp item_inp_wid">
        <label for="Stutus">Stutus</label>
        <div class="custom-select-wrapper">
            <select name="Stutus" class="custom-select">
                <option value="New">New</option>
                <option value="Used">Used</option>
                <option value="Old">Old</option>
            </select>
        </div>      
    </div>
    <div class="form-grp item_inp_wid">
        <label for="member">member</label>
        <div class="custom-select-wrapper">
            <select name="member" class="custom-select">
                <?php
                    $stmt_sel = $dbcon->prepare("SELECT user_id , username FROM `users` ");
                    $stmt_sel->execute();
                    $count_sel= $stmt_sel->rowCount();
                    if ($count_sel > 0) {
                        while ($rows = $stmt_sel->fetch()) {
                            echo "<option value='{$rows['user_id']}'>{$rows['username']}</option>";
                        }
                    }
                ?>
            </select>
        </div>      
    </div>
    <div class="form-grp item_inp_wid">
        <label for="category">category</label>
        <div class="custom-select-wrapper">
            <select name="category" class="custom-select">
                <?php
                    $stmt_cat = $dbcon->prepare("SELECT `id` , `name` FROM `categories` ");
                    $stmt_cat->execute();
                    $count_cat= $stmt_cat->rowCount();
                    if ($count_cat > 0) {
                        while ($r_c = $stmt_cat->fetch()) {
                            echo "<option value='{$r_c['id']}'>{$r_c['name']}</option>";
                        }
                    }
                ?>
            </select>
        </div>      
    </div>
    <button id="btn_S" type="submit">Submit</button>
    </form>
</div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const dropZone = document.getElementById("dropZone");
    const fileInput = document.getElementById("image");
    const imagePreview = document.getElementById("imagePreview");

    dropZone.addEventListener("click", () => {
        fileInput.click();
    });

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

    fileInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            previewImage(file);
        }
    });

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
        <?php
    }elseif($page == "insert_add"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $name_item = $_POST['name'] ?? '';
            $desc_item = $_POST['description'] ?? '';
            $price_item = $_POST['price'] ?? '';
            $country_item = $_POST['country'] ?? '';
            $Stutus_item = $_POST['Stutus'] ?? '';
            $member_item = $_POST['member'] ?? '';
            $category_item = $_POST['category'] ?? '';

            $file = $_FILES['image'];
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_name_parts = explode('.', $file_name);
            $file_ext = strtolower(end($file_name_parts));
            $imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp", "svg", "tiff", "tif", "ico", "jfif", "avif"];
            if(in_array($file_ext, $imageExtensions)){
                if($file_size > 5000000){
                    $_SESSION['message_add_err'][] = "Image size must be less than 5MB";
                }else{
                    $new_name = uniqid('', true) . '.' . $file_ext;
                    move_uploaded_file($file['tmp_name'], "../uploudes/images/" . $new_name);
                }
            }
            
            if(!in_array($file_ext, $imageExtensions) && !empty($_FILES['image']['name'])){
                $_SESSION['message_add_err'][] = "Invalid image Extensions";
            }
    
            $_SESSION['message_add_err'] = [];
    
            if (empty($name_item)) {
                $_SESSION['message_add_err'][] = "The Item name is empty";
            }
    
            if (empty($desc_item)) {
                $_SESSION['message_add_err'][] = "The Item description is empty";
            }
    
            if (empty($price_item)) {
                $_SESSION['message_add_err'][] = "The Item price is empty";
            }

            if (empty($country_item)) {
                $_SESSION['message_add_err'][] = "The Item country is empty";
            }

            if ($_SESSION['message_add_err'] == null) {
                try {
                    $stmt_set = $dbcon->prepare("INSERT INTO `items`( `name`, `description`, `price`, `country`, `image`, `status`,`cat_id`,`member_id`) VALUES ( ? , ? , ? , ? , ? , ? , ? , ? )");
                    $add = $stmt_set->execute([$name_item, $desc_item, $price_item, $country_item, $new_name, $Stutus_item,$category_item,$member_item]);
                    if ($add) {
                        $_SESSION['message_add_item'] = "start";
                        header("Location:items.php");
                        exit;
                    } else {
                        throw new Exception("Insert failed, no rows affected.");
                    }
                } catch (PDOException $e) {
                    die("Database Error: " . $e->getMessage());
                }                
            }
            
            if (!empty($_SESSION['message_add_err'])) {
                header("Location:items.php");
                exit;
            }
        }//server request end
    } elseif($page == "handle"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            if (isset($_POST['delete'])) {
                $stmt_man_it = $dbcon->prepare("DELETE FROM `items` WHERE item_id = ?");
                if ($stmt_man_it->execute([$_POST['id']])) {
                    $url = $_SERVER['HTTP_REFERER'] ;
                    header("location:$url");
                    exit();
                }
                
            }

            if (isset($_POST['accept'])) {
                $stmt_ap = $dbcon->prepare("UPDATE `items` SET `aprove` = 1 WHERE  `item_id` = ?");
                $stmt_ap->execute([$_POST['id']]);
                $prev = $_SERVER['HTTP_REFERER'];
                header("location:$prev");
                exit();
            }

            if (isset($_POST['edit'])) {
            
                    $item_id = $name_item2 = $desc_item2 = $price_item2 = $country_item2 = $Stutus_item2 = $member_item2 = $category_item2 = "";
                    $stmt = $dbcon->prepare("SELECT * FROM `items` WHERE item_id = ?");
                    $stmt->execute([$_POST['id']]);
                    $count = $stmt->rowCount();
                    if ($count > 0) {
                        $row = $stmt->fetch();
                        $item_id = $row['item_id']; # not show
                        $name_item2 = $row['name'];
                        $desc_item2 = $row['description'];
                        $price_item2 = $row['price'];
                        $country_item2 = $row['country'];
                        $Stutus_item2 = $row['status'];
                        $member_item2 = $row['cat_id'];
                        $category_item2 = $row['member_id'];
                    }else{
                        echo 'this member is not found';
                    }

                    $_SESSION['title'] ='Edit Item';
                    require 'startnav.php';
                    require 'timeAgo.php';
                    ?>
                    <h1 style='margin-top: 0;'>Edit Item</h1>
                    <form class="form_" action="?page=update" method="POST">
                        <div class="form-grp item_inp_wid">
                            <label for="name">Name</label>
                            <input value="<?php echo $item_id ; ?>" type="hidden" name="id">
                            <input value="<?php echo $name_item2 ; ?>" type="text" name="name" required>
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="name">Description</label>
                            <input value="<?php echo $desc_item2 ;?>" type="text" name="description" required>
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="name">Price</label>
                            <input value="<?php echo $price_item2 ;?>" type="text" name="price" required>
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="name">Country</label>
                            <input value="<?php echo $country_item2 ;?>" type="text"  name="country" required>
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="Stutus">Stutus</label>
                            <div class="custom-select-wrapper">
                                <select name="Stutus" class="custom-select">
                                    <option value="New"
                                    <?php echo ($Stutus_item2 == "New") ? 'selected' : ''?>
                                    >New</option>
                                    <option value="Used"
                                    <?php echo ($Stutus_item2 == "Used") ? 'selected' : ''?>
                                    >Used</option>
                                    <option value="Old"
                                    <?php echo ($Stutus_item2 == "Old") ? 'selected' : ''?>
                                    >Old</option>
                                </select>
                            </div>      
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="member">member</label>
                            <div class="custom-select-wrapper">
                                <select name="member" class="custom-select">
                                    <?php
                                        $stmt_sel = $dbcon->prepare("SELECT user_id , username FROM `users` ");
                                        $stmt_sel->execute();
                                        $count_sel= $stmt_sel->rowCount();
                                        if ($count_sel > 0) {
                                            while ($rows = $stmt_sel->fetch()) {
                                                echo "<option value='" . $rows['user_id'] . "'";
                                                if ($rows['user_id'] == $member_item2) {echo "selected";}
                                                echo ">{$rows['username']}</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>      
                        </div>
                        <div class="form-grp item_inp_wid">
                            <label for="category">category</label>
                            <div class="custom-select-wrapper">
                                <select name="category" class="custom-select">
                                    <?php
                                        $stmt_cat = $dbcon->prepare("SELECT `id` , `name` FROM `categories` ");
                                        $stmt_cat->execute();
                                        $count_cat= $stmt_cat->rowCount();
                                        if ($count_cat > 0) {
                                            while ($r_c = $stmt_cat->fetch()) {
                                                echo "<option value='" . $r_c['id'] . "'";
                                                if($r_c['id'] == $member_item2){echo "selected" ;}
                                                echo ">{$r_c['name']}</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>      
                        </div>
                        <button id="btn_S" type="submit">Submit</button>
                    </form>
                    <div class='cont_commints'>
                    <h2>Comments</h2>
            <?php
                    $st = $dbcon->prepare("SELECT comments.*,users.username AS username FROM `comments` INNER JOIN users ON users.user_id = comments.user_id WHERE item_id = ?");
                    $st->execute([$_POST['id']]);
                    $count_commt= $st->rowCount();
                    if ($count_commt > 0) {
                        while ($rs = $st->fetch()) {
                            echo "<div class='comment-box'>
                                    <div class='username'>{$rs['username']}</div>
                                    <div class='timestamp'>" . timeAgo($rs['comment_date']) . "</div>
                                    <p>{$rs['comment']}</p>
                                    <form action='?page=delet_comment' method='POST'>
                                    <input type='hidden' name='comm_id' value='{$rs['comment_id']}'>
                                    <button type=submit class='delete-btn'>Delete</button>
                                </div>";
                        }
                    }else{
                            echo "<tr><td colspan='7' style='text-align:center;'>No comments</td></tr>";
                        }
                    ?>
                </div>
            </div>
        </div>
                </body>
                </html>
                <?php
            }
        } else {
            header('location:items.php');
        }/*end else => $_SERVER['REQUEST_METHOD'] handle */
        

    } elseif($page == "delet_comment") {
        $st = $dbcon->prepare("DELETE FROM `comments` WHERE comment_id = ? ");
        $st->execute([$_POST['comm_id']]);
        $count_commt= $st->rowCount();
        if ($count_commt > 0) {
            $_SESSION['suc_d_com'] = "start";
            header("Location: $_SERVER[PHP_SELF]");
            exit();
        }else{
            $_SESSION['fe_d_com'] = "start";
            header("Location: $_SERVER[PHP_SELF]");
            exit();
        }
    }elseif($page == 'update'){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_msg = [];
            $id_item = $_POST['id'] ?? '';
            $name_item = $_POST['name'] ?? '';
            $desc_item = $_POST['description'] ?? '';
            $price_item = $_POST['price'] ?? '';
            $country_item = $_POST['country'] ?? '';
            $Stutus_item = $_POST['Stutus'] ?? '';
            $member_item = $_POST['member'] ?? '';
            $category_item = $_POST['category'] ?? '';

            if (empty($name_item)) {
                $error_msg['message_add_err'][] = "The Item name is empty";
            }
    
            if (empty($desc_item)) {
                $error_msg['message_add_err'][] = "The Item description is empty";
            }
    
            if (empty($price_item)) {
                $error_msg['message_add_err'][] = "The Item price is empty";
            }

            if (empty($country_item)) {
                $error_msg['message_add_err'][] = "The Item country is empty";
            }

            if ($error_msg['message_add_err'] == null) {
                try {
                    $stmt_upd = $dbcon->prepare("UPDATE 
                                                    `items`
                                                SET 
                                                    `name`= ? 
                                                    ,`description`= ? 
                                                    ,`price`= ? 
                                                    ,`country`= ? 
                                                    ,`status`= ? 
                                                    ,`cat_id`= ? 
                                                    ,`member_id`= ?
                                                WHERE
                                                    item_id = ?");
                    $update = $stmt_upd->execute([$name_item, $desc_item, $price_item, $country_item, $Stutus_item,$category_item,$member_item ,$id_item]);
                    if ($update) {
                        $_SESSION['up_ad'] = "start";
                        header("Location:items.php");
                        exit;
                    } else {
                        throw new Exception("Insert failed, no rows affected.");
                    }
                } catch (PDOException $e) {
                    die("Database Error: " . $e->getMessage());
                }                
            }

        
        }
    }else {
        header("location:items.php");
        exit();
    }/*end else pages*/
}/*end if (isset($_SESSION['username']))*/
ob_end_flush();