<?php

include 'config.php';

class FileUploader {
    private $conn;
    private $uploadPath = 'uploaded_img/';
    private $allowedTypes = ['jpg', 'jpeg', 'png'];
    private $maxFileSize = 2000000; // 2 MB

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function uploadFile($file, $name, $email, $pass, $cpass) {
        $errors = array();

        // Validate alternative text
        if(empty($name) || empty($email) || empty($pass) || empty($cpass)) {
            $errors[] = 'All fields are required!';
        } else {
            $name = htmlspecialchars($name);
            $email = htmlspecialchars($email);
        }

        // Validate file type
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($fileExtension, $this->allowedTypes)) {
            $errors[] = 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.';
        }

        // Validate file size
        if($file['size'] > $this->maxFileSize) {
            $errors[] = 'File size exceeds the limit (2MB).';
        }

        // Validate password confirmation
        if($pass !== $cpass) {
            $errors[] = 'Confirm password not matched!';
        }

        // Check if email already exists
        $checkEmail = $this->conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $checkEmail->execute([$email]);
        if($checkEmail->rowCount() > 0) {
            $errors[] = 'User with this email already exists!';
        }

        // If no errors, proceed with uploading
        if(empty($errors)) {
            // Generate date-coded path
            $datePath = date('Y/m/d');
            $targetPath = $this->uploadPath . $datePath . '/';
            if(!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true); // Create directory if not exist
            }

            // Rename file to avoid duplicates
            $fileName = time() . '_' . $file['name'];
            $targetFile = $targetPath . $fileName;

            // Upload file
            if(move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Hash the password
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

                // Insert data into database
                $insert = $this->conn->prepare("INSERT INTO `users` (name, email, password, image) VALUES (?, ?, ?, ?)");
                $insert->execute([$name, $email, $hashedPass, $targetFile]);

                // Return JSON response on success
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Registered successfully!', 'file_path' => $targetFile]);
                exit();
            } else {
                $errors[] = 'Error occurred while uploading the file.';
            }
        }

        // Return JSON response on failure
        http_response_code(422);
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit();
    }
}

$fileUploader = new FileUploader($conn);

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $file = $_FILES['image'];

    $fileUploader->uploadFile($file, $name, $email, $pass, $cpass);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <input type="text" required placeholder="enter your username" class="box" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : '' ?>">
      <input type="email" required placeholder="enter your email" class="box" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : '' ?>">
      <input type="password" required placeholder="enter your password" class="box" name="pass">
      <input type="password" required placeholder="confirm your password" class="box" name="cpass">
      <input type="file" name="image" required class="box" accept="image/jpg, image/png, image/jpeg">
      <p>already have an account? <a href="login.php">login now</a></p>
      <input type="submit" value="register now" class="btn" name="submit">
   </form>
</section>

</body>
</html>
