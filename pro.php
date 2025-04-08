<?php 
session_start();
require"config.php";

if(!isset($_SESSION[utilisateur_name]))
header("location : login.php");
exit();

$userid = $_SESSION[utilisateur_id];
$sql = "SELECT motPasse, mailUser , prenomUser , nomUser , idUser = $userid";
$stmt = $pdo -> prepare ($sql);
$stmt -> execute ([':userid' => $userid]);  
$user = $stmt => fetch(PDO::FETCH_ASSOC);
if (!$user){
die ("not found")
}
?>