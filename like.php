<?php
include('config/db_connect.php');
include('config/variaveis.php');
if (isset($_GET['type'], $_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    $is_admin = $_SESSION['is_admin'];
    switch ($type) {
        case 'publicacao':
            $conn->query("
                INSERT INTO likes (userid, postid)
                SELECT {$_SESSION['user_id']}, {$id} FROM publicacoes
                WHERE EXISTS (
                    SELECT id
                    FROM publicacoes
                    WHERE id = {$id})
                AND NOT EXISTS (
                    SELECT id
                    FROM likes
                    WHERE userid = {$_SESSION['user_id']}
                    AND postid = {$id}) 
                    LIMIT 1");
            $path = "./utilizadores/" . $username . "/";
            $pathfile = $path . $username . ".txt";
            $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
            $txt = "Liked: Postid " . $id . "\n";
            fwrite($log, $txt);
            fclose($log);
            break;
        case 'remove':
            $conn->query("DELETE FROM likes where userid={$_SESSION['user_id']} and postid={$id} LIMIT 1");
            $path = "./utilizadores/" . $username . "/";
            $pathfile = $path . $username . ".txt";
            $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
            $txt = "Unliked: Postid " . $id . "\n";
            fwrite($log, $txt);
            fclose($log);
            break;
        case 'removepost':
            $query = "DELETE FROM publicacoes WHERE id={$id} LIMIT 1;";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $path = "./utilizadores/" . $username . "/";
                $pathfile = $path . $username . ".txt";
                $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
                $txt = "Post Removido: Postid " . $id . "\n";
                fwrite($log, $txt);
                fclose($log);
                mysqli_close($conn);
                header("location: profile.php");
                exit();
            }

            break;
        case 'adminremovepost':

            $query = "DELETE FROM publicacoes WHERE id={$id} and $is_admin=1 LIMIT 1;";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $path = "./utilizadores/" . $username . "/";
                $pathfile = $path . $username . ".txt";
                $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
                $txt = "Admin Post Removido: Postid " . $id . "\n";
                fwrite($log, $txt);
                fclose($log);
                mysqli_close($conn);
                header("location: admin.php");
                exit();
            }

            break;
        case 'adminremovecomm':

            $query = "DELETE FROM comentarios WHERE id={$id} and $is_admin=1 LIMIT 1;";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $path = "./utilizadores/" . $username . "/";
                $pathfile = $path . $username . ".txt";
                $log = fopen($pathfile, "a") or die("Ficheiro nao criado!");
                $txt = "Admin Comentario Removido: idcomentario " . $id . "\n";
                fwrite($log, $txt);
                fclose($log);
                mysqli_close($conn);
                header("location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }

            break;
    }
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
