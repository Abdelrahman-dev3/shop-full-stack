<?php
ob_start();
session_start();
require '../includes/dbcon/pdo.php'; // connect with database

if (!isset($_SESSION['username'])) {
    header("location:admine_login.php");
    exit();
}
    $section = isset($_GET['section']) ? $_GET['section'] : 'home';
    if ($section == 'home') {
        if (isset($_SESSION['message'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Deleted Successfully',icon: 'success'}); 
                }); </script>";
        unset($_SESSION['message']); 
        }
        if (isset($_SESSION['message_up'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({title: 'Updated Successfully',icon: 'success'});
            });</script>";
        unset($_SESSION['message_up']); 
        }

            $sort_arr = ['ASC','DESC'];
            $sort = isset($_GET['sbv']) ? $_GET['sbv'] : 'ASC';
            if (!in_array($sort,$sort_arr)) {
                $sort = 'ASC';
            }
            $_SESSION['title'] ='Manage Categories';
        require 'startnav.php';
?><!-- structer home page -->
<div class="container_cat">
    <a class ='abcon'href="categories.php?section=add">+ Add categories</a>
    <h1>Manage Categories   <span>Ordering By: [<a href="?sbv=ASC" class="<?php echo ($sort == 'ASC') ? 'active' : ''; ?>">ASC</a>|<a href="?sbv=DESC"class="<?php echo ($sort == 'DESC') ? 'active' : ''; ?>">DESC</a>]</span></h1>
    <!-- start loop --> <!-- if visibility => true --> 
    <?php
    $stmt_man_cat = $dbcon->prepare("SELECT * FROM `categories` ORDER BY `order` $sort ");
    $stmt_man_cat->execute();
    $count_man_cat= $stmt_man_cat->rowCount();
    if ($count_man_cat > 0) {
        while ($rows = $stmt_man_cat->fetch()) {
            if ($rows['visibility'] == 1) {    
                echo "<div class='category'>
                <div style='display=flex;'>
                <h2>{$rows['name']}</h2>";
                if ($rows['description'] != "") {
                    echo "<p>" . substr($rows['description'],0,25) . "..." . "</p>";
                }
                echo "</div>
                <div class='status'>";
                if ($rows['allow_comment'] == 0) {
                    echo "<span class='disabled'>Comment Disabled</span>";
                }
                if ($rows['description'] == "") {
                    echo "<span class='disabled'>No Description</span>";
                }
                if ($rows['allow_ads'] == 0) {
                    echo "<span class='disabled'>Ads Disabled</span>";
                }
                echo "</div>
                <div class='actions'>
                <form action='?section=handle' method='post'>
                    <input type='hidden' name='id' value='{$rows['id']}'>
                    <button name='edit' class='cat_btn edit'> Edit</button>
                    <button name='delete' class='cat_btn delete'> Delete</button>
                </form>
                    </div>
                    </div>";
            }else{
            echo "<div class='category'>
                    <h2>Private</h2>
                    <div class='status'>
                    <span class='hidden'>Hidden</span>
                    </div>
                    <div class='actions'>
                    <form action='?section=handle' method='post'>
                        <input type='hidden' name='id' value='{$rows['id']}'>
                        <button name='edit' class='cat_btn edit'> Edit</button>
                        <button name='delete' class='cat_btn delete'> Delete</button>
                    </form>
                    </div>
                </div>";
        }
    }
}/*end if ($count_man_cat > 0)*/else{
    echo "<tr><td colspan='7' style='text-align:center;'>No categories available</td></tr>";
}
    }/*end home page*/elseif($section == 'add'){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $name_sec_add = $_POST['name'] ?? '';
            $desc_sec_add = $_POST['description'] ?? '';
            $order_sec_add = $_POST['ordering'] ?? '';
            $sql_vis_val = intval($_POST['visibility'] === 'yes');
            $sql_comm_val = intval($_POST['commenting'] === 'yes');
            $sql_ads_val = intval($_POST['ads'] === 'yes');
    
            $error_arr = [];
    
            if (empty($name_sec_add)) {
                $error_arr[] = "The Category name is empty, please write a value.";
            }
    
            if (empty($desc_sec_add)) {
                $error_arr[] = "The Category description is empty, please write a value.";
            }
    
            if (empty($order_sec_add) || !is_numeric($order_sec_add)) {
                $error_arr[] = "The Category ordering is invalid or empty, please enter a number.";
            }
    
            if (empty($error_arr)) {
                $stmt_check = $dbcon->prepare("SELECT `id` FROM `categories` WHERE `name` = ?");
                $stmt_check->execute([$name_sec_add]);        
                if ($stmt_check->rowCount() > 0) {
                    $error_arr[] = "This category name already exists!";
                }
            }

            if (empty($error_arr)) {
                try {
                    $stmt_set = $dbcon->prepare("INSERT INTO `categories` (`name`, `description`, `order`, `visibility`, `allow_comment`, `allow_ads`) VALUES (?, ?, ?, ?, ?, ?)");
                    $add = $stmt_set->execute([$name_sec_add, $desc_sec_add, $order_sec_add, $sql_vis_val, $sql_comm_val, $sql_ads_val]);
                    if ($add) {
                        $_SESSION['message_add'] = "Category added successfully!";
                    } else {
                        throw new Exception("Insert failed, no rows affected.");
                    }
                } catch (PDOException $e) {
                    die("Database Error: " . $e->getMessage());
                }                
            }

            if (isset($error_arr) && $error_arr != null) {
                echo "<script>document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Error!', text: '" . implode(",",$error_arr) . " ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
            }

        if (isset($_SESSION['message_add'])) {
            echo "<script>document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({title: 'Added Successfully',icon: 'success'});
            });</script>";
        unset($_SESSION['message_add']); 
        }
    }
    $_SESSION['title'] ='add Categories';
    #####              Add 
        require 'startnav.php';
        ?>
        <h1>Add New Category</h1>
        <form class="form_" action="?section=add" method="POST">
            <div class="form-grp">
                <label for="name">Category Name</label>
                <input value="" type="text" id="name" name="name" required>
            </div>
            <div class="form-grp">
                <label for="description">Description</label>
                <textarea value="" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-grp">
                <label for="ordering">Ordering</label>
                <input value="" type="number" id="ordering" name="ordering" required>
            </div>
            <div class="form-grp raid">
                <label>Visibility</label>
                <div class="radio-group">
                    <input type="radio" id="visibility_yes" name="visibility" value="yes" >
                    <label for="visibility_yes">Yes</label>
                    <input type="radio" id="visibility_no" name="visibility" value="no">
                    <label for="visibility_no">No</label>
                </div>
            </div>
            <div class="form-grp raid pers">
                <label>Allow Commenting</label>
                <div class="radio-group">
                    <input type="radio" id="commenting_yes" name="commenting" value="yes" >
                    <label for="commenting_yes">Yes</label>
                    <input type="radio" id="commenting_no" name="commenting" value="no">
                    <label for="commenting_no">No</label>
                </div>
            </div>
            <div class="form-grp raid pers">
                <label>Allow ADS</label>
                <div class="radio-group">
                    <input type="radio" id="ads_yes" name="ads" value="yes" >
                    <label for="ads_yes">Yes</label>
                    <input type="radio" id="ads_no" name="ads" value="no">
                    <label for="ads_no">No</label>
                </div>
            </div>
            <button id="btn_S" type="submit">Submit</button>
        </form>
    </body>
    </html>
    <?php
    
    }/* end add page */elseif($section == 'handle'){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        /* start delete section */
        if (isset($_POST['delete'])) {
            $stmt_man_cat = $dbcon->prepare("DELETE FROM `categories` WHERE id = ?");
            if ($stmt_man_cat->execute([$_POST['id']])) {
                $_SESSION['message'] = "Deleted successfully";
                $url = $_SERVER['HTTP_REFERER'] ;
                header("location:$url");
                exit();
            }
        }
        /* end delete section */
        
        /* start edit section */
        if (isset($_POST['edit'])) {
            $cat_id = $cat_name = $cat_description = $cat_order = $cat_visibility = $cat_allow_comment = $cat_allow_ads = "";
            $stmt = $dbcon->prepare("SELECT `id` , `name`, `description`, `order`, `visibility`, `allow_comment`, `allow_ads` FROM `categories` WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                $row = $stmt->fetch();
                $cat_id = $row['id']; # not show
                $cat_name = $row['name'];
                $cat_description = $row['description'];
                $cat_order = $row['order'];
                $cat_visibility = $row['visibility'];
                $cat_allow_comment = $row['allow_comment'];
                $cat_allow_ads = $row['allow_ads'];
            }else{
                echo 'this member is not found';
            }
            require 'startnav.php';
            ?>
            <h1>Update Category</h1>
            <form class="form_" action="?section=insert" method="POST">
                <div class="form-grp">
                    <label for="name">Category Name</label>
                    <input type="hidden" value="<?php echo $cat_id ; ?>" name="id" >
                    <input value="<?php echo $cat_name ; ?>" type="text" id="name" name="name" >
                </div>
                <div class="form-grp">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" required><?php echo $cat_description; ?></textarea>
                </div>
                <div class="form-grp">
                    <label for="ordering">Ordering</label>
                    <input value="<?php echo $cat_order; ?>" type="number" id="ordering" name="ordering" required>
                </div>
                <div class="form-grp raid">
                    <label>Visibility</label>
                    <div class="radio-group">
                        <input type="radio" id="visibility_yes" name="visibility" value="yes" 
                        <?php echo $cat_visibility == 1 ? 'checked' : ''; ?>>
                        <label for="visibility_yes">Yes</label>
                        <input type="radio" id="visibility_no" name="visibility" value="no"
                        <?php echo $cat_visibility == 0 ? 'checked' : ''; ?>>
                        <label for="visibility_no">No</label>
                    </div>
                </div>
                <div class="form-grp raid pers">
                    <label>Allow Commenting</label>
                    <div class="radio-group">
                        <input type="radio" id="commenting_yes" name="commenting" value="yes" 
                        <?php echo $cat_allow_comment == 1 ? 'checked' : ''; ?>>
                        <label for="commenting_yes">Yes</label>
                        <input type="radio" id="commenting_no" name="commenting" value="no"
                        <?php echo $cat_allow_comment == 0  ? 'checked' : ''; ?>>
                        <label for="commenting_no">No</label>
                    </div>
                </div>
                <div class="form-grp raid pers">
                    <label>Allow ADS</label>
                    <div class="radio-group">
                        <input type="radio" id="ads_yes" name="ads" value="yes" 
                        <?php echo $cat_allow_ads == 1 ? 'checked' : ''; ?>>
                        <label for="ads_yes">Yes</label>
                        <input type="radio" id="ads_no" name="ads" value="no"
                        <?php echo $cat_allow_ads == 0 ? 'checked' : ''; ?>>
                        <label for="ads_no">No</label>
                    </div>
                </div>
                <button id="btn_S" type="submit">Submit</button>
            </form>
        </body>
        </html>
    <?php        
        }/* end edit section */
        /* start insert section */

        /* end insert section */
    }/*end $_SERVER['REQUEST_METHOD']*/else{
        header("location:categories.php");
        exit();
    }
        
        
    }/* end handle page */elseif($section == 'insert'){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name_sec_add = $desc_sec_add = $order_sec_add = $vis_sec_add = $comm_sec_add = $ads_sec_add = $sql_vis_val = $sql_comm_val = $sql_ads_val = '';
            $stat = true;
            if (empty($_POST['name'])) {
                $_SESSION['errors'][] = "<script>document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Error!', text: 'The Category name is Empty, Please Write value ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
            } else {
                $name_sec_add = $_POST['name'];
            }
        
            if (empty($_POST['description'])) {
                $_SESSION['errors'][] = "<script>document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Error!', text: 'The Category description is Empty, Please Write value ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
            } else {
                $desc_sec_add = $_POST['description'];
            }
        
            if (empty($_POST['ordering'])) {
                $_SESSION['errors'][] = "<script>document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({title: 'Error!', text: 'The Category ordering is Empty, Please Write value ',icon: 'error',background: '#ffebee',color: '#c62828',confirmButtonColor: '#d32f2f'});});</script>";
            } else {
                $order_sec_add = $_POST['ordering'];
            }
            if (isset($_POST['visibility'])) {
                if($_POST['visibility'] == 'yes'){
                    $vis_sec_add = $_POST['visibility'];
                    $sql_vis_val = 1 ;
                }else{
                    $vis_sec_add = 'no' ;
                    $sql_vis_val = 0 ;
                }
            } else {
                $vis_sec_add = 'no' ;
                $sql_vis_val = 0 ;
            }
            
            if (isset($_POST['commenting'])) {
                if($_POST['commenting'] == 'yes'){
                    $comm_sec_add = $_POST['commenting'];
                    $sql_comm_val = 1;
                }else{
                    $comm_sec_add = 'no' ;
                    $sql_comm_val = 0;
                }
            } else {
                $comm_sec_add = 'no' ;
                $sql_comm_val = 0;
            }
            
            if (isset($_POST['ads'])) {
                if($_POST['ads'] == 'yes'){
                    $ads_sec_add = $_POST['ads'];
                    $sql_ads_val = 1;
                }else{
                    $ads_sec_add = 'no' ;
                    $sql_ads_val = 0;
                }
            } else {
                $ads_sec_add = 'no' ;
                $sql_ads_val = 0;
            }
            if (!empty($_SESSION['errors'])) {
                header("Location: categories.php?section=handle"); 
                exit();
            }
            if (!empty($name_sec_add ) && !empty($_POST['description']) && !empty($order_sec_add)) {
                $stmt_check = $dbcon->prepare("SELECT `id` FROM `categories` WHERE `name` = ?");
                $stmt_check->execute([$name_sec_add]);
                if ($stmt_check->rowCount() > 0) {
                    $stat = false;
                }
            }
            
        
            if (!empty($name_sec_add ) && !empty($_POST['description']) && !empty($order_sec_add)) {
                try {
                    $stmt_set = $dbcon->prepare("UPDATE `categories` SET `name`= ?, `description`= ?, `order`= ?, `visibility`= ?, `allow_comment`= ?, `allow_ads`= ? WHERE id = ?");
                    $updated = $stmt_set->execute([$name_sec_add, $desc_sec_add, $order_sec_add, $sql_vis_val, $sql_comm_val, $sql_ads_val, $_POST['id']]);
                
                    if ($updated) {
                        $_SESSION['message_up'] = "start";
                        header("location:categories.php");
                        exit();
                    } else {
                        throw new Exception("Update failed, no rows affected.");
                    }
                } catch (PDOException $th) {
                    die("Database Error: " . $th->getMessage());
                }
            }
    }else {
        header("location:categories.php");
        exit();
    }
}
ob_end_flush();
?>