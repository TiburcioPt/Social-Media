<?php
include('config/db_connect.php');
$_SESSION['postid'] = $_GET['postid'];
$postid = $_SESSION['postid'];
$is_admin = $_SESSION['is_admin'];
if (empty($_SESSION["postid"])) {
    $postid = $_SESSION['postid'];
}

if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "select * from users where id= $id");
    $row = mysqli_fetch_assoc($result);
} else {
    header("Location: login.php");
}
$pubquery = $conn->query("SELECT publicacoes.username, publicacoes.id, publicacoes.pub, count(likes.id) AS likes
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    LEFT JOIN users ON likes.userid= users.id
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($row2 = $pubquery->fetch_object()) {
    $publicacaos[] = $row2;
}
$comquery = $conn->query("SELECT comentarios.id, comentarios.username, comentarios.comentario, comentarios.userid
    FROM comentarios 
    LEFT JOIN users ON comentarios.userid = users.id 
    WHERE comentarios.postid=$postid
    GROUP BY comentarios.id
    ORDER BY comentarios.id DESC");
while ($rowcom = $comquery->fetch_object()) {
    $comentario[] = $rowcom;
}
$post = "SELECT id,username, pub, userid FROM publicacoes WHERE id=$postid";
$result3 = mysqli_query($conn, $post);
$row3 = mysqli_fetch_assoc($result3);
$likes = "SELECT count(likes.id) AS likes FROM publicacoes LEFT JOIN likes ON publicacoes.id = likes.postid LEFT JOIN users ON likes.userid= users.id WHERE publicacoes.id=$postid";
$result4 = mysqli_query($conn, $likes);
$row4 = mysqli_fetch_assoc($result4);
$comcount = "SELECT count(comentarios.userid) AS comcount FROM comentarios where comentarios.postid=$postid";
$resultcount = mysqli_query($conn, $comcount);
$rowcount = mysqli_fetch_assoc($resultcount);

if (isset($_POST["submit"])) {
    $userid = $_SESSION["id"];
    $username = $row['username'];
    $com = $_POST["com"];
    $query = "insert into comentarios values('','$userid','$username','$postid','$com') ";
    mysqli_query($conn, $query);
    $sucesso = mysqli_affected_rows($conn);
    if ($sucesso == 1) {
        $path = "./utilizadores/" . $username . "/";
        $pathfile = $path . $username . ".txt";
        $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
        $txt = "Comentou:" . $com . " | Publicacao: " . $postid . "\n";
        fwrite($log, $txt);
        fclose($log);
        header("Location: postcom.php?postid=$postid");
    }
}
$reporterror = 0;
$reportusername = $row['username'];
if (isset($_POST['reportsubmit'])) {
    if (!empty($_POST['Motivo'])) {
        $motivo = $_POST['Motivo'];
        $query = "insert into reports values('','$id','$reportusername','$postid','$motivo') ";
        mysqli_query($conn, $query);
        $sucesso = mysqli_affected_rows($conn);
        if ($sucesso == 1) {
            header("Location: postcom.php?postid=$postid");
            $reporterror = 0;
            $path = "./utilizadores/" . $reportusername . "/";
            $pathfile = $path . $reportusername . ".txt";
            $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
            $txt = "Reportou a Publicacao: " . $postid . " Motivo: " . $motivo . "\n";
            fwrite($log, $txt);
            fclose($log);
            header("Location: postcom.php?postid=$postid");
        }
    } else {
        $reporterror = 1;
    }
}
$sucesso2 = 0;
if (isset($_POST["resubmit"])) {
    $repub = $_POST["repub"];
    $userid = $_SESSION["user_id"];
    $query = "insert into republicacoes values('','$userid','$postid','$repub')";
    mysqli_query($conn, $query);
    $sucesso2 = mysqli_affected_rows($conn);
    if ($sucesso2 == 1) {
    }
}

//print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/8e613fedb8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/comm.css">
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
                <div class="container mt-3">
                    <ul class="nav justify-content-center">
                        <li class="nav-item">
                            <h2><a class="nav-link" href="index.php">Inicio</a></h2>
                        </li>

                        <li class="nav-item">
                            <h1>Publicação</h1>

                        </li>
                        <li class="nav-item">
                            <h2><a class="nav-link" href="logout.php">Logout</a></h2>
                        </li>
                </div>
            </div>


            <div class="container">
                <div class="container">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#report">
                            <i class="fa-solid fa-triangle-exclamation fa-fade"></i></button>
                        <div id="report" class="collapse">
                            <form action="" method="post">
                                <select class="form-select" name="Motivo">
                                    <option value="" disabled selected>Choose option</option>
                                    <option value="Ofensivo">Ofensivo</option>
                                    <option value="Bullying">Bullying</option>
                                    <option value="Outro">Outro</option>
                                </select>
                                <input type="submit" name="reportsubmit" vlaue="Choose options" class="btn btn-primary mt-3">
                                <?php if ($reporterror == 1) {
                                    echo '<p>Selecione um motivo!</p>';
                                } ?>
                            </form>
                        </div>

                    </div>
                    <div class="container d-flex align-items-left">
                        <?php $path = "utilizadores/" . $row3['username']; ?>
                        <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                        <p class="h3" style="margin-left: 10px;"><a href="userprofile.php?userid=<?php echo htmlspecialchars($row3['userid']); ?>"><?php echo htmlspecialchars($row3['username']); ?></a></p>
                    </div>


                    <div class="container">
                        <p class="publicacao-text-t"><?php echo htmlspecialchars($row3['pub']); ?></p>
                    </div>

                    <?php foreach ((array) $publicacaos as $publicacao) {
                        $_SESSION["postid"] = $publicacao->id;
                    } ?>

                    <div class="container d-flex align-items-left">
                        <button class="input-group-addon btn btn-info" onclick="window.location.href='like.php?type=publicacao&id=<?php echo $row3['id']; ?>'">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                        <div style="margin: 6px 10px 0px 10px;" class="like-ass">
                            <p class="h4"><?php echo $row4['likes']; ?></p>
                        </div>

                        <button id="dislike" class="input-group-addon btn btn-danger" onclick="window.location.href='like.php?type=remove&id=<?php echo $row3['id'] ?>'">
                            <i class="fa-regular fa-thumbs-down"></i>
                        </button>
                        <button type="button" class="btn btn-primary" style="margin-left:10px;" onclick="$('#textorepub<?php echo $publicacao->id; ?>').fadeToggle(1000); 
                                    $('#clickrepub<?php echo $publicacao->id; ?>').fadeToggle(1000);">
                            Republicar
                        </button>
                        <div class="d-flex align-items-left justify-content-left">
                            <form action="" method="post" autocomplete="off">
                                <div class="container">
                                    <input type="text" class="btn btn-light" style="display:none;" name="repub" id="textorepub<?php echo $publicacao->id; ?>" placeholder="O que estas a pensar.." required></input>

                                    <button class="btn btn-primary" type="submit" name="resubmit" id="clickrepub<?php echo $publicacao->id; ?>" style="display:none;" onclick="<?php $_SESSION["postid"] = $publicacao->id; ?>">Republicar</button>
                                </div>
                            </form>

                        </div>

                    </div>
                    <?php
                    if (!empty($sucesso2 == 1)) {

                    ?>
                        <script>
                            $(document).ready(function() {
                                $(".alert-dismissible").slideDown(500);
                                setTimeout(function() {
                                    $(".alert-dismissible").slideUp(500);
                                }, 2000);
                            });
                        </script>
                        <div class="alert alert-success alert-dismissible fade show p-1" id="success-alert" style="margin-top: 5px;">
                            <strong>Republicado!</strong>
                        </div>
                    <?php $sucesso2 = 0;
                    } ?>
                    <div style="margin: 10px 0px 0px 10px; ">
                        <form action="" method="post">
                            <div class="input-group mb-3">
                                <input type="text" name="com" id="com" class="form-control">
                                <button type="submit" name="submit" class="btn btn-info">Comentar</button>
                            </div>
                        </form>
                    </div>
                    <?php echo $rowcount["comcount"] . ' comentarios.'; ?>
                    <hr class="container-flex" color="#072A58">
                    <div class="container">

                        <?php if (isset($comentario)) {
                            foreach ((array) $comentario as $comentarios) {
                                $path2 = "utilizadores/" . $comentarios->username; ?>
                                <?php if ($_SESSION['is_admin'] == 1) { ?>
                                    <div class="float-end dropstart">
                                        <button type="button" class="btn btn-primary dropdown-toggle float-left" data-bs-toggle="dropdown"></button>
                                        <ul class="dropdown-menu ">
                                            <li><a class="dropdown-item" onclick="window.location.href='like.php?type=adminremovecomm&id=<?php echo $comentarios->id; ?>'">Apagar</a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <div class="container mt-3">
                                    <div class="container d-flex align-items-left">

                                        <img src="./<?php echo $path2; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                        <p class="h3" style="margin-left: 10px;"><a href="userprofile.php?userid=<?php echo $comentarios->userid; ?>">
                                                <?php echo htmlspecialchars($comentarios->username); ?></a></p>
                                    </div>
                                    <div>
                                        <p class="publicacao-text-t" style="margin-left: 10px;">
                                            <?php echo htmlspecialchars($comentarios->comentario) . '<br>'; ?></p>
                                    </div>

                                </div>

                                <hr color="#072A58">
                            <?php } ?>
                        <?php } else {
                            echo '<p class="h3 align-items-center justify-content-center">Sem Comentarios!</p>';
                        } ?>
                    </div>

                </div>
            </div>
            <div class="d-flex flex-row-reverse">
                <a class="btn btn-info" href="javascript:history.go(-1)">Voltar</a>
            </div>
        </div>

    </section>
</body>

</html>