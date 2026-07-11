<?php
header('Content-Type: application/json');

function sendToWhatsApp(string $mensagem, string $image_url = ''): array
{
    $token = getenv('WHATSAPP_ACCESS_TOKEN');
    $phone_number_id = getenv('WHATSAPP_PHONE_NUMBER_ID');
    $to_number = getenv('WHATSAPP_TO_NUMBER') ?: '5511983657862';

    if ($token === false || $token === '' || $phone_number_id === false || $phone_number_id === '') {
        return ['success' => false, 'message' => 'Configuração da API do WhatsApp ausente.'];
    }

    if (!function_exists('curl_init')) {
        return ['success' => false, 'message' => 'A extensão cURL não está disponível.'];
    }

    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => $to_number,
        'type' => $image_url !== '' ? 'image' : 'text',
        'text' => [
            'body' => $mensagem
        ]
    ];

    if ($image_url !== '') {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to_number,
            'type' => 'image',
            'image' => [
                'link' => $image_url,
                'caption' => $mensagem
            ]
        ];
    }

    $json_payload = json_encode($payload);
    $url = 'https://graph.facebook.com/v22.0/' . rawurlencode($phone_number_id) . '/messages';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code >= 200 && $http_code < 300) {
        return ['success' => true, 'message' => 'Mensagem enviada pelo WhatsApp.'];
    }

    return ['success' => false, 'message' => 'Falha ao enviar pela API do WhatsApp.'];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
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
$image_url = '';

if (!empty($_FILES['foto']['name']) && is_uploaded_file($_FILES['foto']['tmp_name'])) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed_extensions, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Formato de imagem não permitido.']);
        exit;
    }

    $upload_dir = dirname(__DIR__) . '/uploads/';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Não foi possível criar a pasta de upload.']);
        exit;
    }

    $file_name = uniqid('foto_', true) . '.' . $extension;
    $destination = $upload_dir . $file_name;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Não foi possível salvar a imagem.']);
        exit;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $project_root = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $project_root = $project_root === '/' ? '' : $project_root;
    $image_url = $scheme . '://' . $host . $project_root . '/uploads/' . $file_name;
}

$mensagem = "Olá! Novo orçamento recebido:\n";
$mensagem .= "Nome: " . ($nome ?: 'Não informado') . "\n";
$mensagem .= "WhatsApp: " . ($whatsapp ?: 'Não informado') . "\n";
$mensagem .= "Área: " . ($metros ?: 'Não informada') . " m²\n";
$mensagem .= "Endereço: " . ($endereco ?: 'Não informado') . "\n";
$mensagem .= "Serviço: " . ($servicos[$servico] ?? $servico) . "\n";
$mensagem .= "Valor estimado: R$ " . number_format($valor_estimado, 2, ',', '.') . "\n";
$mensagem .= "Detalhes: " . ($observacoes ?: 'Nenhum detalhe informado') . "\n";

if ($image_url !== '') {
    $mensagem .= "Foto do local: {$image_url}";
} else {
    $mensagem .= "Imagem anexada: Nenhuma";
}

$whatsapp_result = sendToWhatsApp($mensagem, $image_url);

echo json_encode([
    'success' => true,
    'message' => $mensagem,
    'image_url' => $image_url,
    'sent_via_api' => $whatsapp_result['success'],
    'whatsapp_status' => $whatsapp_result['message']
]);
exit;
