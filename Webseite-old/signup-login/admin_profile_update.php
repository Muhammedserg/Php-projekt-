<?php

include 'config.php';

session_start();

class AdminProfileUpdater {
    private $conn;
    private $adminId;

    public function __construct($conn, $adminId) {
        $this->conn = $conn;
        $this->adminId = $adminId;
    }

    public function updateProfile() {
        if(isset($_POST['update'])) {
            $message = array(); // Initialize message array

            // Validate and sanitize name and email
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            // Validate and sanitize password fields
            $previous_pass = filter_input(INPUT_POST, 'previous_pass', FILTER_SANITIZE_STRING);
            $new_pass = filter_input(INPUT_POST, 'new_pass', FILTER_SANITIZE_STRING);
            $confirm_pass = filter_input(INPUT_POST, 'confirm_pass', FILTER_SANITIZE_STRING);

            // Check if the provided old password matches the one in the database
            $select_password = $this->conn->prepare("SELECT password FROM `users` WHERE id = ?");
            $select_password->execute([$this->adminId]);
            $fetch_password = $select_password->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($previous_pass, $fetch_password['password'])) {
                $message[] = 'Old password is incorrect!';
            }

            // Validate new password and confirm password match
            if (!empty($new_pass) && !empty($confirm_pass)) {
                if ($new_pass !== $confirm_pass) {
                    $message[] = 'New password and confirm password do not match!';
                } else {
                    // Hash the new password
                    $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

                    // Update password in the database
                    $update_password = $this->conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                    $update_password->execute([$hashed_pass, $this->adminId]);
                    $message[] = 'Password has been updated successfully!';
                }
            }

            // Update name and email in the database
            $update_profile = $this->conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
            $update_profile->execute([$name, $email, $this->adminId]);
            $message[] = 'Profile details have been updated successfully!';

            // Upload new image if provided
            if (!empty($_FILES['image']['name'])) {
                $image = $_FILES['image'];
                $image_folder = 'uploaded_img/'.$image['name'];

                if ($image['size'] > 2000000) {
                    $message[] = 'Image size is too large!';
                } else {
                    move_uploaded_file($image['tmp_name'], $image_folder);
                    $update_image = $this->conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
                    $update_image->execute([$image['name'], $this->adminId]);
                    $message[] = 'Profile picture has been updated successfully!';
                }
            }

            return $message;
        }
    }
}

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:login.php');
}

$adminProfileUpdater = new AdminProfileUpdater($conn, $admin_id);
$message = $adminProfileUpdater->updateProfile();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Profile Update</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
if(!empty($message)){
    foreach($message as $msg){
        echo '
        <div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<h1 class="title">Update <span>Admin</span> Profile</h1>

<section class="update-profile-container">

    <?php
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$admin_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
        <div class="flex">
            <div class="inputBox">
                <span>Username:</span>
                <input type="text" name="name" required class="box" placeholder="Enter your name" value="<?= $fetch_profile['name']; ?>">
                <span>Email:</span>
                <input type="email" name="email" required class="box" placeholder="Enter your email" value="<?= $fetch_profile['email']; ?>">
                <span>Profile Picture:</span>
                <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
                <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
            <div class="inputBox">
                <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
                <span>Old Password:</span>
                <input type="password" class="box" name="previous_pass" placeholder="Enter previous password">
                <span>New Password:</span>
                <input type="password" class="box" name="new_pass" placeholder="Enter new password">
                <span>Confirm Password:</span>
                <input type="password" class="box" name="confirm_pass" placeholder="Confirm new password">
            </div>
        </div>
        <div class="flex-btn">
            <input type="submit" value="Update Profile" name="update" class="btn">
            <a href="admin_page.php" class="option-btn">Go Back</a>
        </div>
    </form>

</section>

</body>
</html>
