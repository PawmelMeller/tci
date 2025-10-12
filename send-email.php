<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

    // Walidacja
    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Proszę wypełnić wszystkie wymagane pola.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nieprawidłowy adres email.']);
        exit;
    }

    // Konfiguracja emaila
    $to = 'tomestic@gmail.com';
    $subject = 'Nowa wiadomość ze strony od: ' . $name;
    
    // Treść emaila
    $email_content = "Otrzymałeś nową wiadomość ze strony internetowej:\n\n";
    $email_content .= "Imię i nazwisko: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Telefon: $phone\n\n";
    $email_content .= "Wiadomość:\n$message\n";

    // Nagłówki emaila
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Wysłanie emaila
    if (mail($to, $subject, $email_content, $headers)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Dziękujemy! Wiadomość została wysłana.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metoda nie dozwolona.']);
}
?>
