<?php
include('config/db_connect.php');
$_SESSION['userid'] = $_GET['userid'];
$userid = $_SESSION['userid'];
if (!empty($_SESSION["userid"])) {
    $userid = $_SESSION['userid'];
} else {
    header('Location: javascript:history.go(-1)');
}
if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id= $id");
    $row = mysqli_fetch_assoc($result);
    $result2 = mysqli_query($conn, "select * from users where id= $userid");
    $rowdb = mysqli_fetch_assoc($result2);
    if (mysqli_num_rows($result2) == 0) {
        header('Location: javascript:history.go(-1)');
    } else {
        // Your normal code
    }
} else {
    header("Location: index.php");
}

$pubquery = $conn->query("SELECT publicacoes.username, publicacoes.id, publicacoes.pub, count(likes.id) AS likes
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    WHERE publicacoes.userid=$userid
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($row = $pubquery->fetch_object()) {
    $publicacaos[] = $row;
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
    <link rel="stylesheet" href="css/userprofile.css">
    <title>Profile</title>
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
                                <a class="nav-link" href="#respostas">Respostas</a>
                            </li>
                        </ul>
                        <div class="nav-item">
                            <h1>Perfil</h1>
                        </div>
                        <div class="d-flex">
                            <a class="nav-link text-primary" href="profile.php">
                                <i style="font-size: 2em; margin-right:1em;" class="fa-solid fa-user"></i>
                            </a>
                            <a class="nav-link text-primary" style="font-size: 18px;" href="index.php">Inicio</a>
                            <a class="nav-link text-primary" style="font-size: 18px; margin-left:15px;" href="logout.php">Logout</a>
                        </div>
                    </div>
                </nav>

                <div class="container">
                    <ul class="container p-5 bg-dark text-white list-inline">
                        <div class=" card bg-primary" style="margin-left: -115px; width: 400px;">
                            <div class="row no-gutters">
                                <div class="col-sm-5">
                                    <img src="./utilizadores/<?php echo $rowdb["username"]; ?>/user-icon.jpg?t=<?php echo time() ?>" class="card-img-top">
                                </div>
                                <div class="col-sm-7">
                                    <div class="card-body">
                                        <p class="h3 card-title"><?php echo htmlspecialchars($rowdb["name"]); ?></p>
                                        <p class="card-text" style="margin:8px 0px 0px 4px; color:rgb(200,200,200);"> <?php echo htmlspecialchars($rowdb["username"]); ?> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </ul>
                    <div class="d-flex flex-row-reverse">
                        <a class="btn btn-info" href="javascript:history.go(-1)">Voltar</a>
                    </div>
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
                                } ?>
                                <div class="rounded-3" style="margin-bottom:10px; border: 1px solid #3C4043;">
                                    <div class="<?php echo $publicacao->id; ?>">
                                        <div class="container d-flex align-items-left">
                                            <?php $path = "utilizadores/" . $publicacao->username; ?>
                                            <img src="./<?php echo $path; ?>/user-icon.jpg?t=<?php echo time() ?>" class="rounded-circle float-left" style="width:32px ; height:32px;">
                                            <p class="h3" style="margin-left: 10px;"><?php echo $publicacao->username; ?></p>
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
                                        $rowcount = mysqli_fetch_assoc($resultcount);
                                        ?>
                                        <a href='postcom.php?postid=<?php echo $publicacao->id; ?>'>
                                            <p class="h5" style="margin: 10px 0 0 10px;"><?php echo $rowcount["comcount"] . ' comentarios.'; ?></p>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else {
                            echo '<p class="h3 align-items-center justify-content-center">Nao tem publicações!</p>';
                        } ?>
                    </div>
                    <div id="respostas"></div>
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