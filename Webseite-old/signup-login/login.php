<?php

namespace Authentication;

class Login {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function loginUser($email, $password) {
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        $select = $this->conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select->execute([$email]);
        $row = $select->fetch(\PDO::FETCH_ASSOC);

        if($select->rowCount() > 0) {
            if(password_verify($password, $row['password'])) {
                if($row['user_type'] == 'admin') {
                    $_SESSION['admin_id'] = $row['id'];
                    header('location:admin_page.php');
                } elseif($row['user_type'] == 'user') {
                    $_SESSION['user_id'] = $row['id'];
                    header('location:user_page.php');
                } else {
                    return 'no user found!';
                }
            } else {
                return 'incorrect email or password!';
            }
        } else {
            return 'incorrect email or password!';
        }
    }
}

// Autoloader
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

session_start();

use Authentication\Login;

include 'config.php';

if(isset($_POST['submit'])) {
    $login = new Login($conn);
    $message = $login->loginUser($_POST['email'], $_POST['pass']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
   if(isset($message)){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
?>
   
<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>login now</h3>
      <input type="email" required placeholder="enter your email" class="box" name="email">
      <input type="password" required placeholder="enter your password" class="box" name="pass">
      <p>don't have an account? <a href="register.php">register now</a></p>
      <input type="submit" value="login now" class="btn" name="submit">
   </form>

</section>

</body>
</html>
