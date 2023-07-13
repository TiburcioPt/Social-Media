<?php
require 'config/db_connect.php';
$registro = 0;
if (!empty($_SESSION["id"])) {
    header("Location: index.php");
}
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $_SESSION['name'] = $_POST['name'];
    $username = $_POST["username"];
    $_SESSION['username'] = $_POST['username'];
    $email = $_POST["email"];
    $_SESSION['email'] = $_POST['email'];
    $password = $_POST["password"];
    $confirmpassword = $_POST["confirmpassword"];
    $duplicate = mysqli_query($conn, "select * from users where username = '$username' or email = '$email'");
    if (mysqli_num_rows($duplicate) > 0) {
        $registro = 2;
    } else {
        if (empty($name) and empty($username)) {
            $registro = 3;
        } else {
            if (empty($password) and empty($confirmpassword)) {
                $registro = 2;
            } else {
                if ($password == $confirmpassword) {
                    $passwordhash = md5($password);
                    $query = "insert into users values('','$name','$username','$email','$passwordhash','')";
                    mysqli_query($conn, $query);
                    $registro = 1;
                    $path = "./utilizadores/" . $username . "/";
                    mkdir($path, 0777);
                    $pathfile = $path . $username . ".txt";
                    $myfile = fopen($pathfile, "w");

                    $file = "./img/user-icon.jpg";
                    $dest_file = $path . 'user-icon.jpg';
                    if (!copy($file, $dest_file)) {
                        $file . " failed to copy";
                    } else {
                        $file . " copied into " . $dest_file;
                    }
                    header('Location: login.php');
                    session_destroy();
                } else {
                    $registro = 2;
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/register.css">
    <script>
    </script>
</head>

<body>
    <section>
        <div class="caixa-login">
            <div class="caixa-intro">
                <form action="" method="post" autocomplete="off">
                    <h2>Registrar</h2>
                    <div class="intro-dados">
                        <!-- Introducao do nome -->
                        <ion-icon name="person-circle-outline"></ion-icon>
                        <input type="text" name="name" id="name" maxlength="50" required value="<?php if (isset($_SESSION['name'])) {
                                                                                        echo $_SESSION['name'];
                                                                                    } ?>">
                        <label for="name">Nome : </label>
                    </div>
                    <div class="intro-dados">
                        <!-- Introducao do Username -->
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" name="username" id="username" maxlength="25" required value="<?php if (isset($_SESSION['username'])) {
                                                                                                echo $_SESSION['username'];
                                                                                            } ?>">
                        <label for="username">Nome de utilizador : </label>
                    </div>
                    <div class="intro-dados">
                        <!-- Introducao do email -->
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" name="email" id="email" required value="<?php if (isset($_SESSION['email'])) {
                                                                                        echo $_SESSION['email'];
                                                                                    } ?>">
                        <label for="email">Email : </label>
                    </div>
                    <div class="intro-dados">
                        <!-- Introducao do password -->
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" id="password" required value="">
                        <label for="password">Senha : </label>
                    </div>
                    <div class="intro-dados">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="confirmpassword" id="confirmpassword" required value="">
                        <label for="confirmpassword">Confirmar senha : </label>
                    </div>
                    <button type="submit" name="submit">Registrar</button>
                    <?php if ($registro == 2) { ?>
                        <p>Senha incorreta ou utilizador ja encontrado!</p>
                    <?php } elseif ($registro == 1) { ?>
                        <p>Registro concluido.</p>
                    <?php } elseif ($registro == 3) { ?>
                        <p>Nome ou username invalido!</p>
                    <?php } ?>
                </form>
                <br>
                <div class="register">
                    <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </section>
</body>

</html>