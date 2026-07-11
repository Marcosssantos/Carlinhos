<?php
include "../core/config.php";
if($_POST){
 if($_POST['user']=="admin" && $_POST['pass']=="1234"){
  $_SESSION['admin']=true;
  header("Location: dashboard.php");
 }
}
?>
<form method="POST">
<input name="user">
<input type="password" name="pass">
<button>Entrar</button>
</form>
