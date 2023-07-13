<?php
include('config/db_connect.php');
$_SESSION['error_type'] = 0;
if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "select * from users where id= $id");
    $row = mysqli_fetch_assoc($result);
    $result2 = mysqli_query($conn, "select * from users where id= $id");
    $rowdb = mysqli_fetch_assoc($result2);
} else {
    header("Location: login.php");
}
$_SESSION["user_name"] = $row["username"];
if (isset($_POST["submit"])) {
    $name = $_POST["newname"];
    $sql = "UPDATE users SET name='$name' WHERE username={$_SESSION['user_name']}";
    if ($conn->query($sql) === TRUE) {
        header('Location: profile.php');
        exit();
    } else {
        echo "Ocorreu erro: " . $conn->error;
    }
}
$pubquery = $conn->query("
    SELECT publicacoes.username, publicacoes.id, publicacoes.pub, count(likes.id) AS likes,
    GROUP_CONCAT(users.username SEPARATOR '|') AS liked_by
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    LEFT JOIN users ON likes.userid= users.id
    WHERE publicacoes.userid=$id
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($row = $pubquery->fetch_object()) {
    $row->liked_by = $row->liked_by ? explode('|', $row->liked_by) : [];
    $publicacaos[] = $row;
}
$replyquery = $conn->query("
    SELECT users.name as usernamep, republicacoes.pubrep, publicacoes.username, publicacoes.pub, publicacoes.userid, republicacoes.postid
    FROM republicacoes 
    LEFT JOIN publicacoes ON publicacoes.id = republicacoes.postid 
    LEFT JOIN users ON users.id= republicacoes.userid
    where republicacoes.userid=$id");
while ($replyrow = $replyquery->fetch_object()) {
    $reply[] = $replyrow;
}

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
    <link rel="stylesheet" href="css/pro.css">
    <title>Perfil</title>
    <script>
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            localStorage.setItem("ScrollVal", scrolled);
        });
        $(document).ready(function() {
            var ScrollPos = localStorage.getItem("ScrollVal");
            $(document).scrollTop(ScrollPos);
        });
        $(document).ready(function() {

            $("#editar").click(function() {
                $("#show").fadeToggle();
                $("#show2").fadeToggle();

            });
            $("#editarimg").click(function() {

                $("#uploadimg").fadeToggle(1000);
                $("#fileToUpload").fadeToggle(1000);
                $("#error-type").fadeToggle(1000);
                $("#error-type2").fadeToggle(1000);
                $("#error-type3").fadeToggle(1000);
            });
        });
        
    </script>
</head>

<body>
    <section>
        <div id="topo"></div>
        <div class="container">
            <div class="container p-5 bg-dark text-white rounded-3">
                
                <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top justify-content-center">
                    <div class="container">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#top">Topo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#publicacoes">Publicações</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#republicacoes">Republicações</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#respostas">Respostas</a>
                            </li>
                        </ul>
                        <div class="d-flex">
                            <a class="nav-link text-primary" style="font-size: 18px;" href="index.php">Inicio</a>
                            <a class="nav-link text-primary" style="font-size: 18px; margin-left:15px;"  href="logout.php">Logout</a>
                        </div>
                    </div>
                </nav>
                <div class="container">
                    <ul class="container p-5 bg-dark text-white list-inline">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <h4 class="card-title">Informações</h4>
                                <p class="card-text"> Username :<strong> <?php echo htmlspecialchars($rowdb["username"]); ?></strong></p>
                                <div class="d-flex align-items-left justify-content-left">
                                    <p class="card-text"> Name : <strong><?php echo htmlspecialchars($rowdb["name"]); ?></strong></p>
                                    <div class="btn-group">
                                        <button class="rounded btn btn-info" id="editar" style="width:100px; height:35px; margin-left:10px; text-align: center;">Alterar</button>
                                        <div class="d-flex align-items-left justify-content-left">
                                            <form action="" method="post">
                                                <div class="container">
                                                    <input class="btn btn-light" type="text" id="show" name="newname" style="display:none;" required>
                                                    <button class="btn btn-info" name="submit" id="show2" style="display:none;">Aplicar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <p class="card-text"> Email : <?php echo htmlspecialchars($rowdb["email"]); ?> </p>

                                <img src="./utilizadores/<?php echo $rowdb["username"]; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:52px ; height:52px;">
                                <button class="rounded btn btn-info" id="editarimg" style="width:100px; height:35px; margin-left:10px; text-align: center;">Alterar</button>

                                <form action="upload.php" method="post" enctype="multipart/form-data">
                                    <input type="file" name="fileToUpload" id="fileToUpload" style="display:none;" class="form-control">
                                    <input type="submit" value="Upload de imagem" name="submit" id="uploadimg" class="btn btn-info" style="display:none;">
                                    <div class="container">
                                </form>
                                <?php $error_value = $_SESSION['error_type']; ?>
                                <?php if ($error_value == 0) { ?>
                                    <div id="error-type" style="display:none;">Introduza a imagem!</div>
                                <?php } else if ($error_value == 1) { ?>
                                    <div id="error-type2" style="display:none;">Ocorreu algum problema!</div>
                                <?php } else { ?>
                                    <div id="error-type3" style="display:none;">Bem Sucedido!</div>
                                <?php } ?>
                            </div>
                        </div>
                </div>
                </ul>
            </div>
            <div id="publicacoes"></div>
            <hr class="container-flex" color="#072A58">
            <div class="container">
                <div class="container">
                    <p class="h2" style="margin-bottom:25px;">Publicações</p>
                    <?php if (isset($publicacaos)) {
                        foreach ($publicacaos as $publicacao) {
                            $replyquery2 = $conn->query("
                                SELECT users.username as usernamep2,users.name as usernamep, republicacoes.pubrep, 
                                publicacoes.username, publicacoes.pub, publicacoes.userid, republicacoes.postid
                                FROM republicacoes 
                                LEFT JOIN publicacoes ON publicacoes.id = republicacoes.postid 
                                LEFT JOIN users ON users.id=republicacoes.userid
                                where not republicacoes.userid=$id and republicacoes.postid=$publicacao->id");
                            while ($replyrow2 = $replyquery2->fetch_object()) {
                                $reply2[] = $replyrow2;
                            }
                    ?>
                            <div class="<?php echo $publicacao->id; ?>">
                                <div class="rounded-3" style="margin-bottom:10px; border: 1px solid #3C4043;">
                                    <div class="d-flex flex-row-reverse dropstart">
                                        <button type="button" class="btn btn-primary dropdown-toggle float-left" data-bs-toggle="dropdown"></button>
                                        <ul class="dropdown-menu ">
                                            <li><a class="dropdown-item" onclick="window.location.href='like.php?type=removepost&id=<?php echo $publicacao->id; ?>'">Apagar</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="container d-flex align-items-left">
                                        <?php $path = "utilizadores/" . $publicacao->username; ?>
                                        <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                        <p class="h3" style="margin-left: 10px;"><?php echo htmlspecialchars($rowdb["name"]); ?></p>
                                    </div>
                                    <a href='postcom.php?postid=<?php echo $publicacao->id; ?>'>
                                        <div>
                                            <p class="publicacao-text-t"><?php echo htmlspecialchars($publicacao->pub); ?></p>
                                        </div>
                                    </a>
                                    <div class="d-flex align-items-left">
                                        <button class="btn btn-info" onclick="window.location.href='like.php?type=publicacao&id=<?php echo $publicacao->id; ?>'">
                                            <i class="fa-regular fa-heart"></i>
                                        </button>
                                        <div style="margin: 6px 10px 0px 10px;" class="like-ass">
                                            <p class="h4"><?php echo htmlspecialchars($publicacao->likes); ?></p>
                                        </div>
                                        <button id="dislike" class="input-group-addon btn btn-danger" onclick="window.location.href='like.php?type=remove&id=<?php echo $publicacao->id; ?>'">
                                            <i class="fa-regular fa-thumbs-down"></i>
                                        </button>
                                        <button type="button" class="btn p-2 btn-info dropdown-toggle" data-bs-toggle="dropdown" style="margin-left: 10px;" id="like-button">
                                            Likes
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if (!empty($publicacao->liked_by)) { ?>
                                                <?php foreach ($publicacao->liked_by as $user) { ?>
                                                    <li class="dropdown-item active"><?php echo htmlspecialchars($user); ?></li>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <li class="dropdown-item disabled">Nenhum Like</li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php
                                    $comcount = "SELECT count(comentarios.userid) AS comcount FROM comentarios where comentarios.postid=$publicacao->id";
                                    $resultcount = mysqli_query($conn, $comcount);
                                    $rowcount = mysqli_fetch_assoc($resultcount); ?>
                                    <div class=" d-flex align-items-left">
                                        <a href='postcom.php?postid=<?php echo $publicacao->id; ?>'>
                                            <p class="h5" style="margin: 0px 0px 10px 10px;">
                                                <?php echo $rowcount["comcount"] . ' comentarios.'; ?></p>
                                        </a>
                                    </div>
                                </div>
                                <!-- <hr color="#072A58"> -->
                            </div>
                        <?php } ?>
                    <?php } else {
                        echo '<p class="h3 align-items-center justify-content-center">Nao tem publicações!</p>';
                    } ?>
                </div>
            </div>
            <div id="republicacoes"></div>
            <hr class="container-flex" color="#072A58">
            <div class="container">
                <div class="container">
                    <p class="h2" style="margin-bottom:25px;">Republicações</p>
                    <?php if (isset($reply)) {
                        foreach ($reply as $replies) { ?>
                            <div class="rounded-3" style="margin-bottom:10px; border: 1px solid #3C4043;">
                                <div class="container d-flex align-items-left">
                                    <?php $path = "utilizadores/" . $publicacao->username; ?>
                                    <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                    <p class="h3" style="margin-left: 10px;"><?php echo htmlspecialchars($replies->usernamep); ?></p>
                                </div>
                                <div>
                                    <p class="publicacao-text-t" style="margin-left: 20px;"><?php echo htmlspecialchars($replies->pubrep); ?></p>
                                </div>

                                <div style="margin-left: 20px; border: 1px solid #3C4043;" class="rounded-3">
                                    <div class="container d-flex align-items-left">
                                        <?php $path = "utilizadores/" . $replies->username; ?>
                                        <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                        <p class="h3" style="margin-left: 10px;"><a href="userprofile.php?userid=<?php echo $replies->userid; ?>"><?php echo htmlspecialchars($replies->username); ?></a></p>
                                    </div>
                                    <div>
                                        <a href='postcom.php?postid=<?php echo $replies->postid; ?>'>
                                            <p style="margin-left: 20px;"><?php echo htmlspecialchars($replies->pub); ?></p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else {
                        echo '<p class="h3 align-items-center justify-content-center">Nao tem republicacoes!</p>';
                    } ?>

                </div>
            </div>
            <div id="respostas"></div>
            <hr class="container-flex" color="#072A58">
            <div class="container">
                <div class="container">
                    <p class="h2" style="margin-bottom:25px;">Respostas</p>
                    <?php if (isset($reply2)) {
                        foreach ($reply2 as $replies2) { ?>
                            <div class="rounded-3" style="margin-bottom:10px; border: 1px solid #3C4043;">
                                <div class="container d-flex align-items-left">
                                    <?php $path = "utilizadores/" . $replies2->usernamep2; ?>
                                    <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                    <p class="h3" style="margin-left: 10px;"><?php echo htmlspecialchars($replies2->usernamep); ?></p>
                                </div>
                                <div>
                                    <p class="publicacao-text-t" style="margin-left: 20px;"><?php echo htmlspecialchars($replies2->pubrep); ?></p>
                                </div>

                                <div style="margin-left: 20px; border: 1px solid #3C4043;" class="rounded-3">
                                    <div class="container d-flex align-items-left">
                                        <?php $path = "utilizadores/" . $replies2->username; ?>
                                        <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                        <p class="h3" style="margin-left: 10px;"><a href="userprofile.php?userid=<?php echo $replies2->userid; ?>"><?php echo htmlspecialchars($replies2->username); ?></a></p>
                                    </div>
                                    <div>
                                        <a href='postcom.php?postid=<?php echo $replies2->postid; ?>'>
                                            <p style="margin-left: 20px;"><?php echo htmlspecialchars($replies2->pub); ?></p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else {
                        echo '<p class="h3 align-items-center justify-content-center">Nao tem respostas!</p>';
                    } ?>

                </div>
            </div>
        </div>
    </section>
</body>

</html>