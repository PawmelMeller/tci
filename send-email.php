<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Funkcja do czyszczenia danych wejściowych
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot - ochrona przed botami
    if (!empty($_POST['website'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Wykryto spam.']);
        exit;
    }
    
    // Pobierz dane z formularza
    $subject = isset($_POST['subject']) ? clean_input($_POST['subject']) : '';
    $name = isset($_POST['name']) ? clean_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? clean_input($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? clean_input($_POST['phone']) : 'Nie podano';
    $message = isset($_POST['message']) ? clean_input($_POST['message']) : '';

    // Walidacja
    if (empty($subject)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Proszę wybrać temat zapytania.']);
        exit;
    }
    
    if (empty($name) || strlen($name) < 3) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Imię i nazwisko musi mieć minimum 3 znaki.']);
        exit;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Proszę podać prawidłowy adres email.']);
        exit;
    }
    
    if (empty($message) || strlen($message) < 10) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Wiadomość musi mieć minimum 10 znaków.']);
        exit;
    }
    
    if (strlen($message) > 1000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Wiadomość nie może przekraczać 1000 znaków.']);
        exit;
    }

    // Mapowanie tematów
    $subject_map = [
        'wycena' => 'Wycena kuchni na wymiar',
        'loft' => 'Meble loftowe',
        'modernclassic' => 'Modern Classic',
        'konsultacja' => 'Konsultacja projektowa',
        'inne' => 'Inne'
    ];
    
    $subject_text = isset($subject_map[$subject]) ? $subject_map[$subject] : 'Nowe zapytanie';

    // Konfiguracja emaila
    $to = 'tomestic@gmail.com';
    $email_subject = "[$subject_text] Wiadomość od: $name";
    
    // Treść emaila w formacie HTML
    $email_content = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #c0a965; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { color: #333; padding: 5px 0; }
            .message-box { background-color: white; padding: 15px; border-left: 4px solid #c0a965; margin-top: 10px; }
            .footer { text-align: center; padding: 20px; color: #888; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Tomestic Creative Interior</h2>
                <p>Nowa wiadomość ze strony</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Temat zapytania:</div>
                    <div class='value'>$subject_text</div>
                </div>
                <div class='field'>
                    <div class='label'>Imię i nazwisko:</div>
                    <div class='value'>$name</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'><a href='mailto:$email'>$email</a></div>
                </div>
                <div class='field'>
                    <div class='label'>Telefon:</div>
                    <div class='value'><a href='tel:$phone'>$phone</a></div>
                </div>
                <div class='field'>
                    <div class='label'>Wiadomość:</div>
                    <div class='message-box'>$message</div>
                </div>
                <div class='field'>
                    <div class='label'>Data wysłania:</div>
                    <div class='value'>" . date('Y-m-d H:i:s') . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>Ta wiadomość została wysłana automatycznie z formularza kontaktowego na stronie Tcistolarstwowozniak.pl</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Nagłówki emaila
    $headers = "From: Tomestic Contact Form <noreply@tcistolarstwowozniak.pl>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    // Wysłanie emaila
    if (mail($to, $email_subject, $email_content, $headers)) {
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Dziękujemy! Twoja wiadomość została wysłana. Odpowiemy najszybciej jak to możliwe.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Wystąpił błąd podczas wysyłania wiadomości. Prosimy o kontakt telefoniczny: +48 600 820 415'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metoda nie dozwolona.']);
}
?>
