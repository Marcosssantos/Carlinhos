<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.html');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$metros = trim($_POST['metros'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$servico = $_POST['servico'] ?? 'manutencao';
$observacoes = trim($_POST['observacoes'] ?? '');

$servicos = [
    'manutencao' => 'Manutenção',
    'jardim' => 'Jardim novo',
    'premium' => 'Paisagismo premium'
];

$valores = [
    'manutencao' => 15,
    'jardim' => 35,
    'premium' => 70
];

$valor_estimado = (int) $metros * ($valores[$servico] ?? 15);
$numero_whatsapp = '5511983657862';

$mensagem = "Olá! Novo orçamento recebido:\n";
$mensagem .= "Nome: " . ($nome ?: 'Não informado') . "\n";
$mensagem .= "WhatsApp: " . ($whatsapp ?: 'Não informado') . "\n";
$mensagem .= "Área: " . ($metros ?: 'Não informada') . " m²\n";
$mensagem .= "Endereço: " . ($endereco ?: 'Não informado') . "\n";
$mensagem .= "Serviço: " . ($servicos[$servico] ?? $servico) . "\n";
$mensagem .= "Valor estimado: R$ " . number_format($valor_estimado, 2, ',', '.') . "\n";
$mensagem .= "Detalhes: " . ($observacoes ?: 'Nenhum detalhe informado') . "\n";
$mensagem .= "Enviado pelo formulário do site.";

$destino = "https://wa.me/{$numero_whatsapp}?text=" . urlencode($mensagem);
header("Location: {$destino}");
exit;
