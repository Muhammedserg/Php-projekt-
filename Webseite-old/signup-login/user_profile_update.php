<?php

include 'config.php';

session_start();

class UserProfileUpdate {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function updateProfile() {
        if(isset($_POST['update'])) {
            $message = array(); // Initialize message array

            // Validate and sanitize name and email
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            // Update name and email in the database
            $update_profile = $this->conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
            $update_profile->execute([$name, $email, $this->userId]);
            $message[] = 'Profile details have been updated successfully!';

            // Check if new image is provided
            if (!empty($_FILES['image']['name'])) {
                $image = $_FILES['image'];
                $image_folder = 'uploaded_img/'.$image['name'];

                // Check image size
                if ($image['size'] > 2000000) {
                    $message[] = 'Image size is too large!';
                } else {
                    // Update image in the database
                    $update_image = $this->conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
                    $update_image->execute([$image['name'], $this->userId]);

                    // Move uploaded image to folder
                    move_uploaded_file($image['tmp_name'], $image_folder);

                    $message[] = 'Profile picture has been updated successfully!';
                }
            }

            // Validate and update password if provided
            $old_pass = $_POST['old_pass'];
            $new_pass = $_POST['new_pass'];
            $confirm_pass = $_POST['confirm_pass'];

            if (!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)) {
                // Fetch old password from database
                $select_password = $this->conn->prepare("SELECT password FROM `users` WHERE id = ?");
                $select_password->execute([$this->userId]);
                $fetch_password = $select_password->fetch(PDO::FETCH_ASSOC);

                // Verify old password
                if (password_verify($old_pass, $fetch_password['password'])) {
                    // Check if new password and confirm password match
                    if ($new_pass === $confirm_pass) {
                        // Hash new password
                        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

                        // Update password in database
                        $update_password = $this->conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                        $update_password->execute([$hashed_password, $this->userId]);

                        $message[] = 'Password has been updated successfully!';
                    } else {
                        $message[] = 'New password and confirm password do not match!';
                    }
                } else {
                    $message[] = 'Old password is incorrect!';
                }
            }

            return $message;
        }
    }
}

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
    header('location:login.php');
}

$userProfileUpdate = new UserProfileUpdate($conn, $user_id);
$message = $userProfileUpdate->updateProfile();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Update</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
if(isset($message)){
    foreach($message as $msg){
        echo '
        <div class="message">
            <span>'.htmlspecialchars($msg).'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
        ';
    }
}
?>

<h1 class="title">Update <span>User</span> Profile</h1>

<section class="update-profile-container">

    <?php
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    ?>

    <form action="" method="post" enctype="multipart/form-data">
        <img src="uploaded_img/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="">
        <div class="flex">
            <div class="inputBox">
                <span>Username:</span>
                <input type="text" name="name" required class="box" placeholder="Enter your name" value="<?= htmlspecialchars($fetch_profile['name']); ?>">
                <span>Email:</span>
                <input type="email" name="email" required class="box" placeholder="Enter your email" value="<?= htmlspecialchars($fetch_profile['email']); ?>">
                <span>Profile Picture:</span>
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_profile['image']); ?>">
                <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
            </div>
            <div class="inputBox">
                <span>Old Password:</span>
                <input type="password" class="box" name="old_pass" placeholder="Enter previous password" >
                <span>New Password:</span>
                <input type="password" class="box" name="new_pass" placeholder="Enter new password" >
                <span>Confirm Password:</span>
                <input type="password" class="box" name="confirm_pass" placeholder="Confirm new password" >
            </div>
        </div>
        <div class="flex-btn">
            <input type="submit" value="Update Profile" name="update" class="btn">
            <a href="user_page.php" class="option-btn">Go Back</a>
        </div>
    </form>

</section>

</body>
</html>
