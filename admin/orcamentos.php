<?php
include "../core/config.php";
include "../core/auth.php";

$q=$conn->query("SELECT * FROM orcamentos ORDER BY id DESC");
while($o=$q->fetch_assoc()):
?>
<div style="border:1px solid #ccc;margin:10px;padding:10px">
<b><?=$o['nome']?></b><br>
<?=$o['servico']?> | <?=$o['metros']?> m²<br>
<img src="../uploads/<?=$o['foto']?>" width="200">

<form action="../api/atualizar_orcamento.php" method="POST">
<input type="hidden" name="id" value="<?=$o['id']?>">
<input name="valor_final" placeholder="Valor final">
<button name="status" value="aprovado">Aprovar</button>
<button name="status" value="rejeitado">Rejeitar</button>
</form>
</div>
<?php endwhile; ?>
