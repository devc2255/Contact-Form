<?php
// 1. Error Reporting (Keep this on for testing, off for production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = "";
$successMessage = "";

if ($_POST) {
    // Basic Validation
    if (!$_POST["email"] || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $error .= "<li>Valid email is required</li>";
    }
    if (!$_POST["Subject"]) { $error .= "<li>Subject is required</li>"; }
    if (!$_POST["textarea"]) { $error .= "<li>Message is required</li>"; }

    if ($error == "") {
        // 2. Prepare Data for the API
        // We use your Railway Environment Variable for the API Key
        $apiKey = getenv('re_c9uCpC2y_9GYxB2XdAiDnxM9518cQxKBK'); 
        $apiKey = getenv('RESEND_API_KEY');
// TEMPORARY DEBUG LINE: 
// echo "Key length is: " . strlen($apiKey);
        
        $data = [
            "from" => "onboarding@resend.dev", // Resend requires this for free accounts
            "to" => ["dev448230@gmail.com"],   // Your receiving email
            "subject" => "Portfolio: " . $_POST['Subject'],
            "html" => "<strong>From:</strong> " . htmlspecialchars($_POST['email']) . "<br><p>" . nl2br(htmlspecialchars($_POST['textarea'])) . "</p>"
        ];

        // 3. The PHP cURL Request (The "Pro" way to send data)
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status == 200 || $status == 201) {
            $successMessage = "<div class='alert alert-success'>Message sent successfully via PHP API!</div>";
            $_POST = array(); // Clear form
        } else {
            $error = "<div class='alert alert-danger'>API Error ($status). Please try again.</div>";
        }
    } else {
        $error = "<div class='alert alert-danger'><ul>$error</ul></div>";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>PHP API Contact Form</title>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg w-100" style="max-width: 500px;">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Contact Me (PHP)</h2>
                <?php echo $error; echo $successMessage; ?>
                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="email">
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="Subject" id="Subject" placeholder="subject">
                        <label for="Subject">Subject</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="textarea" id="textarea" style="height: 150px" placeholder="message"></textarea>
                        <label for="textarea">Message</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3">Send via PHP Backend</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
