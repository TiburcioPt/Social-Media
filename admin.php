<?php
include('config/db_connect.php');
if (!empty($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id= $id");
    $row = mysqli_fetch_assoc($result);
} else {
    header("Location: index.php");
}
if ($row["is_admin"] == 0) {
    header("Location: index.php");
} else {
}

$pubquery = $conn->query("
    SELECT publicacoes.username, publicacoes.id, publicacoes.pub, publicacoes.userid, count(likes.id) AS likes,
    GROUP_CONCAT(users.username SEPARATOR '|') AS liked_by
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    LEFT JOIN users ON likes.userid= users.id
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($row = $pubquery->fetch_object()) {
    $row->liked_by = $row->liked_by ? explode('|', $row->liked_by) : [];
    $publicacaos[] = $row;
}
$reportquery = $conn->query("
    SELECT publicacoes.username, publicacoes.id, publicacoes.pub, publicacoes.userid, count(likes.id) AS likes,
    GROUP_CONCAT(users.username SEPARATOR '|') AS liked_by
    FROM publicacoes 
    LEFT JOIN likes ON publicacoes.id = likes.postid 
    LEFT JOIN users ON likes.userid= users.id
    GROUP BY publicacoes.id
    ORDER BY id DESC");
while ($rowreport = $reportquery->fetch_object()) {
    $report[] = $rowreport;
}
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
    <title>AdminPage</title>
    <link rel="stylesheet" href="css/admin.css">
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
                            <h1><a class="nav-link" href="profile.php">
                                    <i style="font-size: 53px;" class="fa-solid fa-user"></i>
                                </a></h1>
                        </li>
                        <li class="nav-item">
                            <h1 style="margin-top: 5px;"><a class="nav-link" href="index.php">Inicio</a></h1>
                        </li>
                        <li class="nav-item">
                            <h1 style="margin-top: 14px;">Bem Vindo</h1>

                        </li>
                        <li class="nav-item">
                            <h1 style="margin-top: 5px;"><a class="nav-link" href="logout.php">Logout</a></h1>
                        </li>
                </div>
            </div>


            <form class="form-inline" action="" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search anything..." name="searchname">
                    <button type="submit" class="btn btn-primary">Procurar</button>
                </div>
            </form>


            <?php
            if (isset($_GET['searchname'])) {
                $querysearch = $_GET['searchname'];
                $min_length = 1;
                if (strlen($querysearch) >= $min_length) {
                    $querysearch = htmlspecialchars($querysearch);
                    $querysearch = $conn->real_escape_string($querysearch);
                    $raw_results = mysqli_query($conn, "SELECT * FROM publicacoes WHERE (`pub` LIKE '%" . $querysearch . "%')");
                    if (mysqli_num_rows($raw_results) > 0) {
                        while ($results = mysqli_fetch_array($raw_results)) { ?>
                            <div class="mt-4 p-5 bg-primary text-white rounded">
                                <p>ID: <?php echo $results['id']; ?></p>
                                <p class="publicacao-text-t"><?php echo $results['pub'] ?></p>
                                <p><a class="input-group-addon btn btn-danger" onclick="window.location.href='like.php?type=adminremovepost&id=<?php echo $results['id']; ?>'">Apagar</a>
                                </p>
                            </div>
            <?php }
                    } else {
                        echo "Sem resultados";
                    }
                } else {
                    echo "Digite alguma letra.";
                }
            }
            ?>
            <hr class="container-flex" color="#072A58">

            <div class="container">
                <div class="container">

                    <?php
                    if (isset($publicacaos)) {;
                        foreach ((array) $publicacaos as $publicacao) {
                            $_SESSION["postid"] = $publicacao->id; ?>

                            <div class="<?php echo $publicacao->id; ?>">
                                <div class="d-flex flex-row-reverse dropstart">

                                    <button type="button" class="btn btn-primary dropdown-toggle float-left" data-bs-toggle="dropdown"></button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" onclick="window.location.href='like.php?type=adminremovepost&id=<?php echo $publicacao->id; ?>'">Apagar</a>
                                        </li>
                                    </ul>
                                </div>
                                <?php
                                $report = mysqli_query($conn, "SELECT motivo,count(postid) AS Quant_Reports FROM reports where postid=$publicacao->id");
                                $reportrow = mysqli_fetch_assoc($report);
                                $queryreport = mysqli_query($conn, "SELECT * FROM reports where postid=$publicacao->id");;
                                ?>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <p class="h4" style="margin-top: 15px;"><?php echo $reportrow["Quant_Reports"]; ?></p>
                                    <button class="btn btn-warning" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-triangle-exclamation"></i></button>
                                    <ul class="dropdown-menu">
                                        <?php
                                        while ($row_users = mysqli_fetch_array($queryreport)) {
                                            echo '<hr class="dropdown-divider"></hr>';
                                            echo '<li><p class="h5" style="margin-left:5px;">' .
                                             $row_users['username'] . '</p><a class="dropdown-item" href="#">' . $row_users['motivo'] . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
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
                                    <button class="input-group-addon btn btn-info" onclick="window.location.href='like.php?type=publicacao&id=<?php echo $publicacao->id; ?>'">
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
    </section>
</body>

</html>