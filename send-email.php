<?php


// CONFIGURAÇÕES

$destinatario = 'teleg1507@gmail.com'; 
$nome_site = 'Northern Dock Systems';


// HEADERS DE SEGURANÇA
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


// VERIFICAR SE É POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido. Use POST.'
    ]);
    exit;
}

// RECEBER E LIMPAR DADOS
function limpar_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$name = isset($_POST['name']) ? limpar_input($_POST['name']) : '';
$company = isset($_POST['company']) ? limpar_input($_POST['company']) : '';
$email = isset($_POST['email']) ? limpar_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? limpar_input($_POST['phone']) : '';
$service = isset($_POST['service']) ? limpar_input($_POST['service']) : '';
$subject = isset($_POST['subject']) ? limpar_input($_POST['subject']) : '';
$message = isset($_POST['message']) ? limpar_input($_POST['message']) : '';

// VALIDAÇÕES
$errors = [];

// Nome obrigatório
if (empty($name)) {
    $errors[] = 'Nome é obrigatório';
}

// E-mail obrigatório e válido
if (empty($email)) {
    $errors[] = 'E-mail é obrigatório';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'E-mail inválido';
}

// Telefone obrigatório
if (empty($phone)) {
    $errors[] = 'Telefone é obrigatório';
}

// Assunto obrigatório
if (empty($subject)) {
    $errors[] = 'Assunto é obrigatório';
}

// Mensagem obrigatória
if (empty($message)) {
    $errors[] = 'Mensagem é obrigatória';
}

// Se houver erros, retornar
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// TRADUZIR TIPO DE SERVIÇO
$service_labels = [
    'emergency' => 'Serviço de Emergência',
    'maintenance' => 'Manutenção Preventiva',
    'installation' => 'Instalação de Equipamentos',
    'construction' => 'Construção / Novos Projetos',
    'quote' => 'Solicitar Orçamento',
    'other' => 'Outro'
];

$service_text = isset($service_labels[$service]) ? $service_labels[$service] : 'Não especificado';

// ASSUNTO DO E-MAIL
$email_subject = "Novo contato do site: " . $subject;

// CORPO DO E-MAIL (HTML)
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
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
    <div class='container'>
        <div class='header'>
            <h1>Novo Contato do Site</h1>
        </div>
        
        <div class='content'>
            <div class='field'>
                <span class='label'>Nome Completo:</span>
                <span class='value'>" . $name . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>Empresa:</span>
                <span class='value'>" . ($company ?: 'Não informado') . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>E-mail:</span>
                <span class='value'><a href='mailto:" . $email . "' style='color: #dc2626; text-decoration: none;'>" . $email . "</a></span>
            </div>
            
            <div class='field'>
                <span class='label'>Telefone:</span>
                <span class='value'>" . $phone . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>Tipo de Serviço:</span>
                <span class='value'>" . $service_text . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>Assunto:</span>
                <span class='value'>" . $subject . "</span>
            </div>
            
            <div class='field'>
                <span class='label'>Mensagem:</span>
                <div class='message-box'>" . nl2br($message) . "</div>
            </div>
        </div>
        
        <div class='footer'>
            <p>Este e-mail foi enviado através do formulário de contato do site " . $nome_site . "</p>
            <p>Data/Hora: " . date('d/m/Y H:i:s') . "</p>
        </div>
    </div>
</body>
</html>
";

// HEADERS DO E-MAIL
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: " . $nome_site . " <noreply@" . $_SERVER['HTTP_HOST'] . ">" . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "X-Priority: 1" . "\r\n";

// ENVIAR E-MAIL
try {
    $enviado = mail($destinatario, $email_subject, $email_body, $headers);
    
    if ($enviado) {
        // Sucesso
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso! Entraremos em contato em breve.'
        ]);
        
        // LOG
        $log = date('Y-m-d H:i:s') . " - Email enviado para: $destinatario - De: $name ($email)\n";
        file_put_contents('emails.log', $log, FILE_APPEND);
        
    } else {
        // Falha no envio
        throw new Exception('Falha ao enviar e-mail');
    }
    
} catch (Exception $e) {
    // Erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar mensagem. Por favor, tente novamente ou entre em contato por telefone.'
    ]);
    
    // LOG de erro
    error_log("Erro ao enviar email: " . $e->getMessage());
}
?>