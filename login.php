<?php
require 'config/db_connect.php';

$login = true;
if (!empty($_SESSION["id"])) {
    header("Location: index.php");
}
if (isset($_POST["submit"])) {
    $useremail = $_POST["useremail"];
    $password = $_POST["password"];
    $result = mysqli_query($conn, "select * from users where username= '$useremail' or email = '$useremail'");
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        $login = true;
        if (md5($password) == $row["password"]) {
            // $_SESSION["login"]=true;
            $_SESSION["id"] = $row["id"];
            $_SESSION["is_admin"] = $row["is_admin"];
            header("Location: index.php");
            $login = true;
        } else {
            $login = false;
        }
    } else {
        $login = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>

<body>

    <section>
        <div class="caixa-login">
            <div class="caixa-intro">
                <form action="" method="post" autocomplete="off">
                    <h2>Login</h2>
                    <div class="intro-dados">
                        <!-- Introducao email ou nome utilizador -->
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" name="useremail" id="useremail" required value="">
                        <label for="useremail">Nome de utilizador ou email : </label>
                    </div>
                    <div class="intro-dados">
                        <!-- Introducao senha -->
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" id="password" required value="">
                        <label for="password">Senha : </label>
                    </div>
                    <button type="submit" name="submit">Login</button>
                    <?php if ($login == false) { ?>
                        <p>Senha ou utilizador nao encontrado!</p>
                    <?php } ?>

                </form>
                <br>
                <div class="register">
                    <a href="register.php">Registrar</a>
                </div>
            </div>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>