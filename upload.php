<?php
include('config/variaveis.php');
$_SESSION['error_type']=$error_value;
$target_dir = "utilizadores/$username/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if(isset($_POST["submit"])) {
    if(empty($_FILES["fileToUpload"])){
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "Ficheiro - " . $check["mime"] . ".";
            header('Location: profile.php');
            $uploadOk = 1;
        } else {
            echo "Ficheiro nao e uma imagem.";
            header('Location: profile.php');
            $uploadOk = 0;
        }
    }else{
        header('Location: profile.php');
    }
}

if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "Ficheiro muito grande.";
  header('Location: profile.php');
  $error_value=1;
  $uploadOk = 0;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
  echo "So sao permitidos ficheiros do tipo JPG, JPEG, PNG & GIF.";
  header('Location: profile.php');
  $error_value=1;
  $uploadOk = 0;
}

if ($uploadOk == 0) {
    
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    $newname="user-icon.jpg";
    rename($target_dir.$_FILES["fileToUpload"]["name"], $target_dir.$newname);
    header('Location: profile.php');
    $error_value=2;
  } else {
    header('Location: profile.php');
    $error_value=1;
  }
}
?>