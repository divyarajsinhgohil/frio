<?php
/**
 * FRIO Front API - Customer Inquiries & Catalogue Downloads Endpoint
 * Receives public storefront submissions and securely saves them to the database.
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only POST requests are accepted.']);
    exit;
}

try {
    // Read input (handles both raw JSON and normal URL-encoded/FormData POSTs)
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) {
        $input = $_POST;
    }

    $type       = isset($input['type']) ? trim($input['type']) : '';
    $first_name = isset($input['first_name']) ? trim($input['first_name']) : '';
    $last_name  = isset($input['last_name']) ? trim($input['last_name']) : '';
    $email      = isset($input['email']) ? trim($input['email']) : '';
    $phone      = isset($input['phone']) ? trim($input['phone']) : '';
    $message    = isset($input['message']) ? trim($input['message']) : null;

    // ── Input Validation ──────────────────────────────────────────────────────
    if ($type !== 'catalogue' && $type !== 'contact') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid submission type. Must be either catalogue or contact.']);
        exit;
    }

    if (empty($first_name)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'First name is required.']);
        exit;
    }

    if (empty($email) || strpos($email, '@') === false) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'A valid email address is required.']);
        exit;
    }

    // Clean phone number (strip whitespace or dashes just in case, but validate 10 digits)
    $phone_clean = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone_clean) !== 10) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Mobile number must be a valid 10-digit number.']);
        exit;
    }

    // ── Insert Lead Record ────────────────────────────────────────────────────
    $stmt = $pdo->prepare("
        INSERT INTO `inquiries` (`type`, `first_name`, `last_name`, `email`, `phone`, `message`)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $type,
        $first_name,
        $last_name !== '' ? $last_name : null,
        $email,
        $phone_clean,
        $message
    ]);

    // ── Send Email Notifications (Dynamic & Environment-Aware) ────────────────
    try {
        // Fetch configurations from settings
        $settings_stmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
        $settings_stmt->execute();
        $settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);

        $recipient_email = isset($settings['notification_email']) && !empty($settings['notification_email']) 
            ? trim($settings['notification_email']) 
            : 'divyarajgohil6299@gmail.com';
        
        // Environment Details
        $is_local = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || $_SERVER['HTTP_HOST'] === '127.0.0.1');
        $env_label = $is_local ? 'DEVELOPMENT / LOCALHOST' : 'PRODUCTION / LIVE WEBSITE';
        
        // Subject line
        $subject_type = ($type === 'catalogue') ? 'Catalogue Request' : 'Contact Inquiry';
        $email_subject = "🔔 New $subject_type from $first_name $last_name ($env_label)";
        
        // HTML Body
        $display_type = ($type === 'catalogue') ? 'Catalogue Download Request' : 'Customer Contact message';
        $message_section = '';
        if ($type === 'catalogue') {
            $filename = basename($message);
            $message_section = "
                <div style='margin-top: 15px; padding: 12px; background-color: #f0f7ff; border: 1px solid #cce3ff; border-radius: 8px; font-weight: bold; color: #0c4b86;'>
                    📄 PDF Booklet: $filename
                </div>";
        } else {
            $message_section = "
                <div style='margin-top: 15px; padding: 15px; background-color: #fcfcfc; border-left: 4px solid #d4af37; border-radius: 4px; color: #4a4a4a; white-space: pre-wrap; font-family: monospace; line-height: 1.5;'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>";
        }
        
        $current_year = date("Y");
        $admin_inquiries_url = "http://" . $_SERVER['HTTP_HOST'] . "/FrioAdmin/inquiries.php";
        
        $email_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>New Frio Inquiry</title>
        </head>
        <body style='margin: 0; padding: 20px; background-color: #f4f6f8; font-family: \"Segoe UI\", Helvetica, Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e1e8ed; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <!-- Header -->
                <div style='background-color: #0c4b86; padding: 25px 30px; text-align: center; color: #ffffff;'>
                    <h1 style='margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase;'>FRIO INDUSTRIAL</h1>
                    <p style='margin: 5px 0 0 0; font-size: 11px; opacity: 0.75; font-weight: bold; letter-spacing: 1px;'>$env_label</p>
                </div>
                
                <!-- Content -->
                <div style='padding: 30px;'>
                    <h2 style='margin-top: 0; color: #0c4b86; font-size: 16px; font-weight: 800; border-bottom: 2px solid #f4f6f8; padding-bottom: 10px; text-transform: uppercase;'>🔔 NEW INQUIRY REGISTERED</h2>
                    
                    <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                        <tr>
                            <td style='padding: 8px 0; font-size: 12px; color: #8a9ca5; font-weight: bold; width: 30%;'>INQUIRY TYPE</td>
                            <td style='padding: 8px 0; font-size: 13px; color: #1c2a38; font-weight: bold; text-transform: uppercase;'>$display_type</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-size: 12px; color: #8a9ca5; font-weight: bold;'>CLIENT NAME</td>
                            <td style='padding: 8px 0; font-size: 13px; color: #1c2a38; font-weight: bold;'>$first_name $last_name</td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-size: 12px; color: #8a9ca5; font-weight: bold;'>EMAIL ID</td>
                            <td style='padding: 8px 0; font-size: 13px; color: #1c2a38;'><a href='mailto:$email' style='color: #0c4b86; font-weight: bold; text-decoration: none;'>$email</a></td>
                        </tr>
                        <tr>
                            <td style='padding: 8px 0; font-size: 12px; color: #8a9ca5; font-weight: bold;'>MOBILE PHONE</td>
                            <td style='padding: 8px 0; font-size: 13px; color: #1c2a38;'><a href='tel:$phone_clean' style='color: #0c4b86; font-weight: bold; text-decoration: none;'>+91 $phone_clean</a></td>
                        </tr>
                    </table>
                    
                    $message_section
                    
                    <div style='margin-top: 30px; text-align: center;'>
                        <a href='$admin_inquiries_url' style='display: inline-block; background-color: #d4af37; color: #ffffff; font-weight: bold; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;'>
                            Open Admin Console
                        </a>
                    </div>
                </div>
                
                <!-- Footer -->
                <div style='background-color: #f8fafc; padding: 20px 30px; text-align: center; font-size: 10px; color: #8a9ca5;'>
                    This is an automated notification from the FRIO Admin Portal.<br>
                    © $current_year FRIO Industrial. Precision Brass Solutions.
                </div>
            </div>
        </body>
        </html>";

        send_notification_email($recipient_email, $email_subject, $email_body, $settings);
    } catch (Exception $e) {
        error_log("Frio Email Notification Failure: " . $e->getMessage());
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Inquiry registered successfully.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

// ── Secure Pure-PHP SMTP Client & Fallback ────────────────────────────────────
function send_notification_email($to, $subject, $body, $settings) {
    $method = isset($settings['email_method']) ? $settings['email_method'] : 'mail';
    $host   = isset($settings['smtp_host']) ? $settings['smtp_host'] : '';
    $port   = isset($settings['smtp_port']) ? intval($settings['smtp_port']) : 587;
    $user   = isset($settings['smtp_user']) ? $settings['smtp_user'] : '';
    $pass   = isset($settings['smtp_pass']) ? $settings['smtp_pass'] : '';
    $secure = isset($settings['smtp_secure']) ? $settings['smtp_secure'] : 'tls';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: FRIO Notifications <" . (!empty($user) ? $user : "noreply@frio.co") . ">\r\n";

    if ($method === 'smtp' && !empty($host)) {
        return send_smtp_email($to, $subject, $body, $host, $port, $user, $pass, $secure, $headers);
    } else {
        return @mail($to, $subject, $body, $headers);
    }
}

function send_smtp_email($to, $subject, $body, $host, $port, $user, $pass, $secure, $headers) {
    try {
        $server = $host;
        if ($secure === 'ssl') {
            $server = 'ssl://' . $host;
        }
        
        $socket = @fsockopen($server, $port, $errno, $errstr, 15);
        if (!$socket) {
            throw new Exception("Socket connection failed: $errstr ($errno)");
        }
        
        $read = function($socket) {
            $res = '';
            while ($str = fgets($socket, 515)) {
                $res .= $str;
                if (substr($str, 3, 1) === ' ') {
                    break;
                }
            }
            return $res;
        };
        
        $read($socket);
        
        fwrite($socket, "EHLO localhost\r\n");
        $read($socket);
        
        if ($secure === 'tls') {
            fwrite($socket, "STARTTLS\r\n");
            $read($socket);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception("TLS encryption handshake failed.");
            }
            fwrite($socket, "EHLO localhost\r\n");
            $read($socket);
        }
        
        if (!empty($user) && !empty($pass)) {
            fwrite($socket, "AUTH LOGIN\r\n");
            $read($socket);
            fwrite($socket, base64_encode($user) . "\r\n");
            $read($socket);
            fwrite($socket, base64_encode($pass) . "\r\n");
            $auth_res = $read($socket);
            if (strpos($auth_res, '235') === false) {
                throw new Exception("SMTP Authentication failed: " . $auth_res);
            }
        }
        
        fwrite($socket, "MAIL FROM:<" . (!empty($user) ? $user : "noreply@frio.co") . ">\r\n");
        $read($socket);
        
        fwrite($socket, "RCPT TO:<$to>\r\n");
        $read($socket);
        
        fwrite($socket, "DATA\r\n");
        $read($socket);
        
        $msg = "To: <$to>\r\n";
        $msg .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $msg .= $headers;
        $msg .= "\r\n";
        $msg .= $body;
        $msg .= "\r\n.\r\n";
        
        fwrite($socket, $msg);
        $data_res = $read($socket);
        
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return (strpos($data_res, '250') !== false);
    } catch (Exception $e) {
        error_log("Frio SMTP socket error: " . $e->getMessage());
        return false;
    }
}
?>
