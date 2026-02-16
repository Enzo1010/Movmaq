<?php

declare(strict_types=1);

// Configuracao
$destinatario = 'teleg1507@gmail.com';
$nomeSite = 'Northern Dock Systems';

// Headers de seguranca
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

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

function allowedOrigin(?string $origin): ?string
{
    if ($origin === null || $origin === '') {
        return null;
    }

    $originHost = parse_url($origin, PHP_URL_HOST);
    $requestHost = $_SERVER['HTTP_HOST'] ?? '';

    if (!$originHost || $requestHost === '') {
        return null;
    }

    return strcasecmp($originHost, $requestHost) === 0 ? $origin : null;
}

function isRateLimited(string $clientKey, int $maxRequests = 5, int $windowSeconds = 300): bool
{
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'movmaq-rate-limit';

    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        return false;
    }

    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $clientKey) . '.log';
    $now = time();
    $validTimestamps = [];

    if (is_file($file)) {
        $savedRows = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($savedRows as $row) {
            $timestamp = (int) $row;
            if ($timestamp >= ($now - $windowSeconds)) {
                $validTimestamps[] = $timestamp;
            }
        }
    }

    if (count($validTimestamps) >= $maxRequests) {
        @file_put_contents($file, implode(PHP_EOL, $validTimestamps) . PHP_EOL, LOCK_EX);
        return true;
    }

    $validTimestamps[] = $now;
    @file_put_contents($file, implode(PHP_EOL, $validTimestamps) . PHP_EOL, LOCK_EX);

    return false;
}

$origin = allowedOrigin($_SERVER['HTTP_ORIGIN'] ?? null);
if ($origin !== null) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}

// Verificar metodo HTTP
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    jsonResponse(405, [
        'success' => false,
        'message' => 'Metodo nao permitido. Use POST.',
    ]);
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 160);
$clientKey = $clientIp . '|' . $userAgent;

if (isRateLimited($clientKey)) {
    jsonResponse(429, [
        'success' => false,
        'message' => 'Muitas tentativas. Aguarde alguns minutos e tente novamente.',
    ]);
}

// Honeypot (bots costumam preencher esse campo invisivel)
$honeypot = limparInput($_POST['website'] ?? '');
if ($honeypot !== '') {
    error_log('Formulario bloqueado por honeypot: ' . $clientIp);
    jsonResponse(200, [
        'success' => true,
        'message' => 'Mensagem recebida.',
    ]);
}

// Tempo minimo de preenchimento (impede submit instantaneo de bot)
$formStartedAt = (int) ($_POST['form_started_at'] ?? 0);
if ($formStartedAt > 0 && (time() - $formStartedAt) < 3) {
    jsonResponse(400, [
        'success' => false,
        'message' => 'Formulario enviado muito rapido. Tente novamente.',
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

// Validacoes
$errors = [];

if ($name === '') {
    $errors[] = 'Nome e obrigatorio.';
}

if ($email === '') {
    $errors[] = 'E-mail e obrigatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail invalido.';
}

if ($phone === '') {
    $errors[] = 'Telefone e obrigatorio.';
}

if ($subject === '') {
    $errors[] = 'Assunto e obrigatorio.';
}

if ($message === '') {
    $errors[] = 'Mensagem e obrigatoria.';
}

if ($errors !== []) {
    jsonResponse(400, [
        'success' => false,
        'message' => implode(' ', $errors),
    ]);
}

// Traducao do tipo de servico
$serviceLabels = [
    'emergency' => 'Servico de Emergencia',
    'maintenance' => 'Manutencao Preventiva',
    'installation' => 'Instalacao de Equipamentos',
    'construction' => 'Construcao / Novos Projetos',
    'quote' => 'Solicitar Orcamento',
    'other' => 'Outro',
];

$serviceText = $serviceLabels[$service] ?? 'Nao especificado';
$companyText = $company !== '' ? $company : 'Nao informado';
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
                <span class="label">Tipo de Servico:</span>
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
            <p>Este e-mail foi enviado pelo formulario de contato do site {$nomeSite}</p>
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
