<?php
require './sendgrid-php-main/lib/SendGrid.php';

use SendGrid\Mail\Mail;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $name = filter_input(INPUT_POST, 'name');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject');
    $message = filter_input(INPUT_POST, 'message');

    // Check required fields
    if (!$name || !$email || !$subject || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, preencha todos os campos corretamente.']);
        exit;
    }

    // SendGrid configuration
    $sendgrid_api_key = 'YOUR_SENDGRID_API_KEY'; // Replace with your SendGrid API key
    $to_email = "jorgeveloso233@gmail.com";       // Replace with your email address

    // Create email object
    $emailObj = new Mail();
    $emailObj->setFrom($email, $name);
    $emailObj->setSubject($subject);
    $emailObj->addTo($to_email, "Your Name");
    $emailObj->addContent(
        "text/plain",
        "Nome: $name\nEmail: $email\nMensagem:\n$message"
    );
    $emailObj->addContent(
        "text/html",
        "<strong>Nome:</strong> $name<br><strong>Email:</strong> $email<br><strong>Mensagem:</strong><br>$message"
    );

    // Send email using SendGrid
    $sendgrid = new \SendGrid($sendgrid_api_key);

    try {
        $response = $sendgrid->send($emailObj);
        if ($response->statusCode() == 202) {
            echo json_encode(['status' => 'success', 'message' => 'A sua mensagem foi enviada. Obrigado!']);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Houve um erro ao enviar a mensagem. Por favor, tente novamente mais tarde.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
