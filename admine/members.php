<?php
ob_start();
session_start();

if (!isset($_SESSION['id'])) {
    header("location:admine_login.php");
    exit();
}

$page = isset($_GET['page'])? $_GET['page']: 'manage';

$error_fullname = $error_email = $error_username = $msg = $username = $email = $password = $full_name = "" ;

require '../includes/dbcon/pdo.php'; // connect with database

if ($page == 'manage') {

    if (isset( $_SESSION['mem_succ_added'])) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({title: 'Added Successfully', icon: 'success'});});</script>";
    unset($_SESSION['mem_succ_added']); 
    }

    if (isset( $_SESSION['mem_succ_up'])) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({title: 'Updated Successfully', icon: 'success'});});</script>";
    unset($_SESSION['mem_succ_up']); 
    }

    $_SESSION['title'] ='Manage Member';
    $_SESSION['manage.style'] = true;

    require 'startnav.php';
?>
<h1>Manage Members</h1>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Registered Date</th>
                <th>Control</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $stmt = $dbcon->prepare("SELECT * FROM `users` WHERE group_id != 1");
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch()) {
                echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['date']}</td>
                <td>
                <form action='?page=handle' method='POST'>
                    <input type='hidden' name='id' value='{$row['user_id']}'>
                    <input type='submit' name='edit' class='btn_ btn-success' value='Edit'>
                    <input type='submit' name='delete' class='btn_ btn-danger' value='Delete'>";
                    // if ($row['regsters'] == 0) {
                    //     echo "<input type='submit' name='accept' class='btn_ btn-accept' value='Accept'>";  //    #Accept member feature
                    // }
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
    <input type='submit' class="add-member" value='+ Add New Member'>
    </form>
</div>
</body>
</html>
<?php
}elseif($page == 'add'){
    $_SESSION['title'] ='add Member';
    require 'startnav.php';
?>
<h1>add Member</h1>
    <form action="?page=insert" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username" <?php echo isset($_SESSION['mem_err']['username']) ? 'style="color: red;"' : ''; ?>>Username</label>
            <input value="<?php echo isset($_SESSION['form_data']['username']) ? $_SESSION['form_data']['username'] : ''; ?>" type="text" id="username" name="username" require>
            <p><?php echo isset($_SESSION['mem_err']['rep']) ? $_SESSION['mem_err']['rep'] : ''; ?></p>
        </div>

        <div class="form-group">
            <label for="password" <?php echo isset($_SESSION['mem_err']['password']) ? 'style="color: red;"' : ''; ?>>Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="email" <?php echo isset($_SESSION['mem_err']['email']) ? 'style="color: red;"' : ''; ?>>Email</label>
            <input value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>" type="email" id="email" name="email" require>
        </div>

        <div class="form-group">
            <label for="fullname" <?php echo isset($_SESSION['mem_err']['fullname']) ? 'style="color: red;"' : ''; ?>>Full Name</label>
            <input value="<?php echo isset($_SESSION['form_data']['fullname']) ? $_SESSION['form_data']['fullname'] : ''; ?>" type="text" id="fullname" name="fullname" required>
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
        <button type="submit" class="btn">Save</button>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    let dropZone = document.getElementById("dropZone");
    let fileInput = document.getElementById("image");
    let imagePreview = document.getElementById("imagePreview");

    dropZone.addEventListener("click", () => {
        console.log("clicked");
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

        let file = event.dataTransfer.files[0];
        if (file) {
            previewImage(file);
        }
    });

    fileInput.addEventListener("change", (event) => {
        let file = event.target.files[0];
        if (file) {
            previewImage(file);
        }
    });

    function previewImage(file) {
        let reader = new FileReader();
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
}elseif($page == 'insert'){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_SESSION['mem_err'] = [];

        $username_ins = $_POST['username'] ?? '';
        $password_ins = $_POST['password'] ?? '';
        $email_ins = $_POST['email'] ?? '';
        $fullname_ins = $_POST['fullname'] ?? '';

    if (empty($username_ins)) {
        $_SESSION['mem_err']['username'] = 'not allowed set empty username';
    }

    if (empty($password_ins)) {
        $_SESSION['mem_err']['password'] = 'not allowed set empty password';
    }

    if (empty($email_ins)) {
        $_SESSION['mem_err']['email'] = 'not allowed set empty email';
    } 

    if (empty($fullname_ins)) {
        $_SESSION['mem_err']['fullname'] = 'not allowed set empty fullname';
    } 

    if (empty($_FILES['image']['name'])) {
        $_SESSION['mem_err']['image'] = 'The Image Is Requierd';
    }

    $file = $_FILES['image'];
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_name_parts = explode('.', $file_name);
    $file_ext = strtolower(end($file_name_parts));
    $imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp", "svg", "tiff", "tif", "ico", "jfif", "avif"];
    if(in_array($file_ext, $imageExtensions)){
        if($file_size > 5000000){
            $_SESSION['mem_err'][] = "Image size must be less than 5MB";
        }else{
            $new_name = uniqid('', true) . '.' . $file_ext;
            move_uploaded_file($file['tmp_name'], "../uploudes/images/" . $new_name);
        }
    }
    
    if(!in_array($file_ext, $imageExtensions) && !empty($_FILES['image']['name'])){
        $_SESSION['mem_err'][] = "Invalid image Extensions";
    }

    if (!empty($_SESSION['mem_err'])) {
        header("location:members.php?page=add");
        exit();
    }

    $_SESSION['form_data'] = [
        'username' => $username_ins,
        'email'    => $email_ins,
        'fullname' => $fullname_ins
    ];


    if (empty($_SESSION['mem_err'])) {
        $stmt = $dbcon->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username_ins]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['mem_err']['rep'] = 'Username is already taken. Please choose another one';
            header("location:members.php?page=add");
            exit();
        }
    }

    if (empty($_SESSION['mem_err'])) {
        try {
            $stmt = $dbcon->prepare("INSERT INTO `users`(`username`, `password`, `email`, `full_name`, regsters , is_verified , `image`) VALUES ( ? , ? , ? , ? , 1 , 1 , ?)");
            $stmt->execute([$username_ins,$password_ins,$email_ins,$fullname_ins,$new_name]);
            $_SESSION['mem_succ_added'] = "start";
            unset($_SESSION['form_data']);
            header("location:members.php");
            exit();
        } catch (PDOException $th) {
            echo $th->getMessage();
        }
    }
?>      
</div>
</body>
</html>
<?php
}else{
    header('location:members.php');
    exit();
}
}elseif($page == 'handle'){ # $_GET in varible declerd $page   # start handle page 
    if (!empty($_SESSION['mem_err'])) {
        echo "<script>document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: '" . implode(" ", $_SESSION['mem_err']) . "',
                icon: 'error',
                background: '#ffebee',
                color: '#c62828',
                confirmButtonColor: '#d32f2f'
            }); 
        });</script>";
        unset($_SESSION['mem_err']);
    }    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $save_username = $save_email = $save_password = $save_full_name = $save_user_id = '';
        if (isset($_POST['delete'])) {
            $stmt = $dbcon->prepare("DELETE FROM `users` WHERE user_id = ?");
            if ($stmt->execute([$_POST['id']])) {
                $url = $_SERVER['HTTP_REFERER'] ;
            header("location:$url");
            exit();
            }
        }

        if (isset($_POST['edit'])) {
            $stmt = $dbcon->prepare("SELECT `user_id` ,`username`, `password`, `email`, `full_name` FROM `users` WHERE `user_id` = ?");
            $stmt->execute([$_POST['id']]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                $row = $stmt->fetch();
                $save_user_id = $row['user_id']; 
                $save_username = $row['username'];
                $save_email = $row['email'];
                $save_password = $row['password'];
                $save_full_name = $row['full_name'];
            }else{
                echo 'this member is not found';
            }
        }

        // accept member feature      if you need it
        
        // if (isset($_POST['accept'])) {
        //     $stmt = $dbcon->prepare("UPDATE `users` SET `regsters`= 1 WHERE  `user_id` = ?");
        //     $stmt->execute([$_POST['id']]);
        //     $prev = $_SERVER['HTTP_REFERER'];
        //     header("location:$prev");
        //     exit();
        // }

        if (isset($_POST['save_member_changes'])) {
            $em = '';
            $_SESSION['mem_err_up'] = [];

            $us =  trim(htmlspecialchars($_POST['username'])) ?? '';
            $pas = password_hash($_POST['password'],PASSWORD_DEFAULT);
            $ful = trim(htmlspecialchars($_POST['fullname'])) ?? '';

        if (empty($us)) {
            $_SESSION['mem_err_up'][] = 'not allowed empty value in username';
        } 

        if (empty($pas)) {
            $_SESSION['mem_err_up'][] = 'not allowed empty value in password';
        }

        $email_validate = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if ($email_validate) {
            $em = $_POST['email'];
        } else {
            $_SESSION['mem_err_up'][] = 'The value of email is incorrect';
        }

        if (empty($ful)) {
            $_SESSION['mem_err_up'][] = 'not allowed empty value in full_name';
        } 

            if (empty($_SESSION['mem_err_up'])) {
                $stmt = $dbcon->prepare("UPDATE `users` SET `username`= ?, `password`= ?, `email`= ?, `full_name`= ? WHERE user_id = ?");
                $stmt->execute([$us, $pas , $em , $ful ,$_POST['u_id']]);
                $_SESSION['mem_succ_up'] = "start";
                header("location:members.php");
                exit();
            }

        }
            require 'startnav.php';
        ?>
<h1>Edit <?php echo ucwords($save_username) ;?></h1>
    <form action="" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input value="<?php echo $save_username ;?>" type="text" id="username" name="username"required>
            <input value="<?php echo $save_user_id ;?>" type="hidden" name="u_id" > 
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password"  value="<?php echo $save_password; ?>" name="password" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input value="<?php echo $save_email ;?>" type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input value="<?php echo $save_full_name ;?>" type="text" id="fullname" name="fullname" required>
        </div>
        <button type="submit" name='save_member_changes' class="btn">Save</button>
    </div>
</body>
</html>
<?php
}else{
    header('location:members.php');
    exit();
}
}/* end handle page*/elseif($page == 'edit'){
    $_SESSION['title'] ='Edit Member';
    $stmt = $dbcon->prepare(
        "SELECT
            *
        FROM
            `users`
        WHERE
            `user_id` = ? ");
    
    $stmt->execute([$_SESSION['id']]);
    
    $count = $stmt->rowCount();
    
    if ($count > 0) {
        while ($row = $stmt->fetch()) {
            $username = $row['username'] ;
            $email = $row['email'];
            $password = $row['password'] ;
            $full_name = $row['full_name'] ;
        }
        require 'startnav.php';
        
    ?>
            <h1>Edit Member</h1>
        <form action="?page=update" method="POST">
            <input value="<?php echo $_SESSION['id'] ;?>" type="hidden"  name="id">
            <div class="form-group">
                <label for="username">Username</label>
                <input value="<?php echo $username ;?>" type="text" id="username" name="username" required>
                <p><?php echo $error_username ;?></p> 
                </div>
            <div class="form-group">
                <label for="password">Password</label>
            <input type="hidden"  name="oldpassword" value="<?php echo $password; ?>">
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input value="<?php echo $email ;?>" type="email" id="email" name="email" required>
                <p><?php echo $error_email ;?></p>
            </div>
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input value="<?php echo $full_name ;?>" type="text" id="fullname" name="fullname" required>
                <p><?php echo $error_fullname ;?></p>
            </div>
            <button type="submit" class="btn">Save</button>
        </div>
    </body>
    </html>
    <?php
    }else{
        echo "the data is not found";
    }
}elseif($page == 'update'){
    $_SESSION['title'] ='Update Member';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require '../includes/classes/members.class.php';
        $obj = new Data();
        
        $obj->set_name(htmlspecialchars(trim($_POST['username'])));
        if (empty($_POST['password'])) {
            $obj->set_password($_POST['oldpassword']);
        } else {
            $obj->set_password($_POST['password']);
        }
        $obj->set_email($_POST['email']);
        $obj->set_fullname($_POST['fullname']);
        if (!empty($obj->get_name()) && !empty($obj->get_password()) && !empty($obj->get_email()) && !empty($obj->get_fullname())) {
            $stmts = $dbcon->prepare("UPDATE `users` SET `username`= ? , `password`= ? ,`email`= ? ,`full_name`= ? WHERE user_id = ?");
            $stmts->execute([$obj->get_name(),$obj->get_password(),$obj->get_email(),$obj->get_fullname(),$_POST['id']]);         
            $msg = "success changes";
        } else {
            header("refresh:2;url=members.php?page=edit");
            exit();
        }
        
        require 'startnav.php';
?>
            <h1>Update Member</h1>
            <h2 style="font-size:35px;color: <?php echo ($msg == "not allow empty values") ? 'red' : 'green'; ?>;"><?php echo $msg; ?>        </div></h2>
    </body>
    </html>
<?php
    }else{
        header("refresh:2;url=members.php?page=edit");
        exit();    
    }
}elseif($page == 'pending'){
    $_SESSION['title'] ='Pending Member';
    $_SESSION['manage.style'] = true;
    require 'startnav.php';
?>
<h1>Pending Members</h1>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Registered Date</th>
                <th>Control</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $stmt = $dbcon->prepare("SELECT * FROM `users` WHERE regsters = 0");
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count > 0) {
                while ($row = $stmt->fetch()) {
                echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['date']}</td>
                <td>
                <form action='?page=handle' method='POST'>
                    <input type='hidden' name='id' value='{$row['user_id']}'>
                    <input type='submit' name='delete' class='btn_ btn-danger' value='Delete'>
                </form>
                </td>
            </tr>";
                    //<input type='submit' name='accept' class='btn_ btn-accept' value='Accept'>  # Accept member feature
                } 
            }else{
                echo "<tr><td colspan='7' style='text-align:center;'>No data available</td></tr>";
            }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php
}else{
    header("location:members.php");
    exit();    
}
ob_end_flush();