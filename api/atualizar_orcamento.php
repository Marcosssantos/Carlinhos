<?php
include "../core/config.php";

$id=$_POST['id'];
$status=$_POST['status'];
$final=$_POST['valor_final'];

$conn->query("UPDATE orcamentos SET status='$status',valor_final='$final' WHERE id='$id'");
header("Location: ../admin/orcamentos.php");
