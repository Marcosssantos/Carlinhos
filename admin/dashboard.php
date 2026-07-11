<?php
include "../core/config.php";
include "../core/auth.php";
include "../core/helpers.php";

$t=$conn->query("SELECT COUNT(*) c FROM orcamentos")->fetch_assoc()['c'];
$r=$conn->query("SELECT SUM(valor_final) s FROM orcamentos WHERE status='aprovado'")->fetch_assoc()['s'];
?>
<h1>Dashboard</h1>
<p>Orçamentos: <?=$t?></p>
<p>Receita: <?=dinheiro($r)?></p>
<a href="orcamentos.php">Ver orçamentos</a>
