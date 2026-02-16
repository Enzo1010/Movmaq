<?php

declare(strict_types=1);

// Configuração
$destinatario = 'teleg1507@gmail.com';
$nomeSite = 'Northern Dock Systems';

// Headers de segurança
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

function jsonResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function limparInput(?string $data): string
{
    return htmlspecialchars(trim((string) $data), ENT_QUOTES, 'UTF-8');
}

// Verificar método HTTP
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    jsonResponse(405, [
        'success' => false,
        'message' => 'Método não permitido. Use POST.',
    ]);
}

// Receber e limpar dados
$name = limparInput($_POST['name'] ?? '');
$company = limparInput($_POST['company'] ?? '');
$email = limparInput($_POST['email'] ?? '');
$phone = limparInput($_POST['phone'] ?? '');
$service = limparInput($_POST['service'] ?? '');
$subject = limparInput($_POST['subject'] ?? '');
$message = limparInput($_POST['message'] ?? '');

// Validações
$errors = [];

if ($name === '') {
    $errors[] = 'Nome é obrigatório.';
}

if ($email === '') {
    $errors[] = 'E-mail é obrigatório.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail inválido.';
}

if ($phone === '') {
    $errors[] = 'Telefone é obrigatório.';
}

if ($subject === '') {
    $errors[] = 'Assunto é obrigatório.';
}

if ($message === '') {
    $errors[] = 'Mensagem é obrigatória.';
}

if ($errors !== []) {
    jsonResponse(400, [
        'success' => false,
        'message' => implode(' ', $errors),
    ]);
}

// Tradução do tipo de serviço
$serviceLabels = [
    'emergency' => 'Serviço de Emergência',
    'maintenance' => 'Manutenção Preventiva',
    'installation' => 'Instalação de Equipamentos',
    'construction' => 'Construção / Novos Projetos',
    'quote' => 'Solicitar Orçamento',
    'other' => 'Outro',
];

$serviceText = $serviceLabels[$service] ?? 'Não especificado';
$companyText = $company !== '' ? $company : 'Não informado';
$messageWithBreaks = nl2br($message);
$submittedAt = date('d/m/Y H:i:s');

// Assunto e corpo do e-mail
$emailSubject = "Novo contato do site: {$subject}";
$emailBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f5f5f5;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: white;
            padding: 30px;
            margin: 0;
        }
        .field {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .field:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #7f1d1d;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .value {
            color: #333;
            font-size: 15px;
        }
        .message-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Novo Contato do Site</h1>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Nome Completo:</span>
                <span class="value">{$name}</span>
            </div>
            <div class="field">
                <span class="label">Empresa:</span>
                <span class="value">{$companyText}</span>
            </div>
            <div class="field">
                <span class="label">E-mail:</span>
                <span class="value"><a href="mailto:{$email}" style="color: #dc2626; text-decoration: none;">{$email}</a></span>
            </div>
            <div class="field">
                <span class="label">Telefone:</span>
                <span class="value">{$phone}</span>
            </div>
            <div class="field">
                <span class="label">Tipo de Serviço:</span>
                <span class="value">{$serviceText}</span>
            </div>
            <div class="field">
                <span class="label">Assunto:</span>
                <span class="value">{$subject}</span>
            </div>
            <div class="field">
                <span class="label">Mensagem:</span>
                <div class="message-box">{$messageWithBreaks}</div>
            </div>
        </div>
        <div class="footer">
            <p>Este e-mail foi enviado através do formulário de contato do site {$nomeSite}</p>
            <p>Data/Hora: {$submittedAt}</p>
        </div>
    </div>
</body>
</html>
HTML;

// Headers do e-mail
$host = preg_replace('/[^a-z0-9.-]/i', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
$host = $host !== '' ? $host : 'localhost';

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    "From: {$nomeSite} <noreply@{$host}>",
    "Reply-To: {$email}",
    'X-Mailer: PHP/' . phpversion(),
    'X-Priority: 1',
];

try {
    $enviado = mail($destinatario, $emailSubject, $emailBody, implode("\r\n", $headers));

    if (!$enviado) {
        throw new RuntimeException('Falha ao enviar e-mail.');
    }

    $log = sprintf(
        "%s - E-mail enviado para: %s - De: %s (%s)%s",
        date('Y-m-d H:i:s'),
        $destinatario,
        $name,
        $email,
        PHP_EOL
    );
    @file_put_contents(__DIR__ . '/emails.log', $log, FILE_APPEND);

    jsonResponse(200, [
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.',
    ]);
} catch (Throwable $e) {
    error_log('Erro ao enviar email: ' . $e->getMessage());

    jsonResponse(500, [
        'success' => false,
        'message' => 'Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato por telefone.',
    ]);
}
