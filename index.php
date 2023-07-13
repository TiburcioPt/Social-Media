<?php
include('config/db_connect.php');
if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id= $id");
    $row = mysqli_fetch_assoc($result);
    $result2 = mysqli_query($conn, "SELECT * FROM users WHERE id= $id");
    $rowdb = mysqli_fetch_assoc($result2);
} else {
    header("Location: login.php");
}

$_SESSION["user_id"] = $rowdb["id"];
if (isset($_POST["submit"])) {
    $pub = $_POST["pub"];
    $username = $row["username"];
    $query = "insert into publicacoes values('','$id','$username','$pub') ";
    mysqli_query($conn, $query);
    $sucesso = mysqli_affected_rows($conn);
    if ($sucesso == 1) {
        $path = "./utilizadores/" . $username . "/";
        $pathfile = $path . $username . ".txt";
        $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
        $txt = "Publicação:" . $pub . "\n";
        fwrite($log, $txt);
        fclose($log);
        header('Location: index.php');
        exit();
    }
}




$pubquery = $conn->query("
    SELECT publicacoes.username, publicacoes.id, publicacoes.pub, publicacoes.userid, count(likes.id) AS likes
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    LEFT JOIN users ON likes.userid= users.id
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($row = $pubquery->fetch_object()) {
    $publicacaos[] = $row;
}

//echo $_SESSION["user_id"];
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/8e613fedb8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/inicio.css">

    <title>Social Media</title>
    <script>
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            localStorage.setItem("ScrollVal", scrolled);
        });
        $(document).ready(function() {
            var ScrollPos = localStorage.getItem("ScrollVal");
            $(document).scrollTop(ScrollPos);
        });

    </script>
</head>

<body>
    <section>
        <div class="container p-5 bg-dark text-white rounded-3">
            <div class="nav justify-content-center">
                <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top justify-content-center">
                    <div class="container">
                        <ul class="navbar-nav">
                            <?php if ($_SESSION['is_admin'] == 1) { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="admin.php">Admin</a>
                                </li>
                            <?php } ?>
                            <li class="nav-item">
                                <a class="nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#opcao">
                                    Perfil
                                </a>
                            </li>
                        </ul>
                        <div class="nav-item">
                            <h1>Bem Vindo</h1>
                        </div>
                        <div class="d-flex">
                            <a class="nav-link" href="profile.php">
                                <i style="font-size: 2em;" class="fa-solid fa-user"></i>
                            </a>
                            <a class="nav-link" style="font-size: 18px;" href="logout.php">Logout</a>
                        </div>
                    </div>
                </nav>
                <div class="container mt-3">
                    <ul class="nav justify-content-center">
                        <li class="nav-item">
                            <h1 style="margin-top: 14px;"></h1>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="offcanvas offcanvas-start bg-dark" id="opcao">
                <div class="offcanvas-header">
                    <h1 class="offcanvas-title">Perfil</h1>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="form-check form-switch">
                        <div class="container">
                            <ul class="container p-5 text-white list-inline">
                                <div class="mx-auto card bg-secondary" style="width:150px">
                                    <img src="./utilizadores/<?php echo $rowdb["username"]; ?>/user-icon.jpg?t=<?php echo time() ?>" class="card-img-top">
                                    <div class="card-body">
                                        <p class="h3 card-title"><?php echo htmlspecialchars($rowdb["name"]); ?></p>
                                        <p class="card-text" style="margin:8px 0px 0px 4px; color:rgb(200,200,200);"> <?php echo htmlspecialchars($rowdb["username"]); ?> </p>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container d-flex align-items-center justify-content-center">
                <form action="" method="post" autocomplete="off">
                    <div class="input-group">

                        <textarea class="form-control custom-control" style="resize:none" name="pub" id="pub" cols="30" rows="3" minlength="2" placeholder="Vamos lá." required></textarea>
                        <button class="input-group-addon btn btn-primary" type="submit" name="submit">Publicar</button>
                    </div>
                </form>
            </div>
            <hr class="container-flex" color="#072A58">
            <div class="container">
                <div class="container">

                    <?php if (isset($publicacaos)) {;
                        foreach ((array) $publicacaos as $publicacao) {

                            $_SESSION["postid"] = $publicacao->id;
                            $_SESSION["userid"] = $publicacao->userid;
                    ?>

                            <div class="<?php echo $publicacao->id; ?>">
                                <div class="container d-flex align-items-left">
                                    <?php $path = "utilizadores/" . $publicacao->username; ?>
                                    <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                    <p class="h3" style="margin-left: 10px;"><a href="userprofile.php?userid=<?php echo $publicacao->userid; ?>">
                                            <?php echo htmlspecialchars($publicacao->username); ?></a></p>
                                </div>
                                <a href='postcom.php?postid=<?php echo $publicacao->id; ?>'>
                                    <div>
                                        <p class="publicacao-text-t"><?php echo htmlspecialchars($publicacao->pub); ?></p>
                                    </div>
                                </a>
                                <div class="d-flex align-items-left">
                                    <button class="btn btn-info" data-bs-toggle="tooltip" title="Like!" onclick="window.location.href='like.php?type=publicacao&id=<?php echo $publicacao->id; ?>'">
                                        <i class="fa-regular fa-heart"></i>
                                    </button>
                                    <div style="margin: 6px 10px 0px 10px;">
                                        <p class="h4"><?php echo htmlspecialchars($publicacao->likes); ?></p>
                                    </div>
                                    <button data-bs-toggle="tooltip" title="Dislike!" class=" btn btn-danger" onclick="window.location.href='like.php?type=remove&id=<?php echo $publicacao->id; ?>'">
                                        <i class="fa-regular fa-thumbs-down"></i>
                                    </button>
                                </div>
                                <?php
                                $comcount = "SELECT count(comentarios.userid) AS comcount FROM comentarios where comentarios.postid=$publicacao->id";
                                $resultcount = mysqli_query($conn, $comcount);
                                $rowcount = mysqli_fetch_assoc($resultcount); ?>
                                <div class=" d-flex align-items-left">
                                    <a href='postcom.php?postid=<?php echo $publicacao->id; ?>'>
                                        <p class="h5" style="margin: 10px 0px 0px 10px;">
                                            <?php echo $rowcount["comcount"] . ' comentarios.'; ?></p>
                                    </a>
                                </div>
                                <hr color="#072A58">
                            <?php } ?>
                        <?php } else {
                        echo '<p class="h3 align-items-center justify-content-center">Sem Publicações!</p>';
                    } ?>
                            </div>

                </div>
            </div>
        </div>
        </div>
    </section>
</body>
</html>