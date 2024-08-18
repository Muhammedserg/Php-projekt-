<?php

include 'config.php';

session_start();

class AdminPage {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function checkLoggedIn() {
        if (!isset($_SESSION['admin_id'])) {
            header('location:login.php');
            exit;
        }
    }

    public function getAdminProfile($admin_id) {
        $select_profile = $this->conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $select_profile->execute([$admin_id]);
        return $select_profile->fetch(PDO::FETCH_ASSOC);
    }

    public function render() {
        $this->checkLoggedIn();
        $admin_id = $_SESSION['admin_id'];
        $fetch_profile = $this->getAdminProfile($admin_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>admin page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<h1 class="title"> <span>admin</span> profile page </h1>

<section class="profile-container">

   <div class="profile">
      <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
      <h3><?= $fetch_profile['name']; ?></h3>
      <a href="admin_profile_update.php" class="btn">update profile</a>
      <a href="logout.php" class="delete-btn">logout</a>
      <div class="flex-btn">
         <a href="login.php" class="option-btn">login</a>
         <a href="register.php" class="option-btn">register</a>
      </div>
   </div>

</section>

</body>
</html>

<?php
    }
}

// Verwendung der Klasse
$adminPage = new AdminPage($conn);
$adminPage->render();
?>
