<?php
include 'config.php';

session_start();

class UserProfileDelete {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function deleteUserProfile() {
        if(isset($_POST['delete'])) {
            $delete_profile = $this->conn->prepare("DELETE FROM `users` WHERE id = ?");
            $delete_profile->execute([$this->userId]);
            
            // Optional: Hier können weitere Bereinigungsmaßnahmen durchgeführt werden, z. B. das Löschen von zugehörigen Daten in anderen Tabellen.

            session_destroy(); // Beenden der aktuellen Sitzung nach dem Löschen des Profils
            header('Location: login.php'); // Umleitung zur Login-Seite nach dem Löschen des Profils
            exit();
        }
    }
}

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
    header('location:login.php');
}

$userProfileDelete = new UserProfileDelete($conn, $user_id);
$userProfileDelete->deleteUserProfile();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User Profile</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1 class="title">Delete User Profile</h1>

<section class="delete-profile-container">

    <p>Are you sure you want to delete your profile?</p>
    <form action="" method="post">
        <div class="flex-btn">
            <input type="submit" value="Delete Profile" name="delete" class="delete-btn">
            <a href="user_page.php" class="option-btn">Cancel</a>
        </div>
    </form>

</section>

</body>
</html>
