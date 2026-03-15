<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 2. Require the files you uploaded to InfinityFree
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";
$successMessage = "";

if ($_POST) {
    // Basic Server-side validation
    if (!$_POST["email"]) {
        $error .= "<li>Email is required</li>";
    } else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false) {
        $error .= "<li>Email is not valid</li>";
    }
    if (!$_POST["Subject"]) {
        $error .= "<li>Subject is required</li>";
    }
    if (!$_POST["textarea"]) {
        $error .= "<li>Message content is required</li>";
    }

    if ($error != "") {
        $error = "<div class='alert alert-danger shadow-sm' role='alert'><strong>Oops! Please fix the following:</strong><ul class='mb-0'>" . $error . "</ul></div>";
    } else {
        
        // 3. Initialize PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;   // UNCOMMENT THIS LINE IF IT FAILS SO YOU CAN SEE THE ERROR
            $mail->isSMTP();                                            
            $mail->Host       = 'smtp.gmail.com';                     
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = 'dev448230@gmail.com';                 // Your Gmail address
            $mail->Password   = getenv('SMTP_PASSWORD');    // Paste the 16-letter App Password here (no spaces)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        // Railway
            $mail->Port       = 465;                                   // InfinityFree blocks port 25, you MUST use 587

            // Recipients
            $mail->setFrom('dev448230@gmail.com', 'Portfolio Form');   // Must match your Gmail address
            $mail->addAddress('dev448230@gmail.com');                  // Where you want the email delivered
            $mail->addReplyTo($_POST['email'], 'Site Visitor');        // Allows you to hit "Reply" and email the visitor back

            // Content
            $mail->isHTML(true);                                  
            $mail->Subject = 'New Portfolio Message: ' . $_POST["Subject"];
            
            // Build the email body
            $visitorEmail = htmlspecialchars($_POST['email']);
            $visitorMessage = nl2br(htmlspecialchars($_POST['textarea'])); // nl2br keeps paragraph breaks
            
            $mail->Body = "<h3>You have a new message!</h3>
                           <p><strong>From:</strong> {$visitorEmail}</p>
                           <p><strong>Message:</strong><br>{$visitorMessage}</p>";

            // Send the email
            $mail->send();
            
            $successMessage = "<div class='alert alert-success shadow-sm' role='alert'><strong>Success!</strong> Your mail was sent successfully. We'll be in touch.</div>";
            $_POST = array(); // Clear the form
            
        } catch (Exception $e) {
            $error = "<div class='alert alert-danger shadow-sm' role='alert'>Message could not be sent. Please try again later.</div>";
            // Optional: Log $mail->ErrorInfo to a file to debug without exposing it to the user
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Get In Touch</title>
    <style>
        /* Custom UI Tweaks */
        body {
            background-color: #f8f9fa;
        }
        .contact-card {
            border-radius: 1rem;
            border: none;
        }
        .btn-primary {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
    </style>
  </head>
  <body>
      
  <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
      <div class="card contact-card shadow-lg w-100" style="max-width: 550px;">
          <div class="card-body p-4 p-md-5">
              
              <h2 class="text-center fw-bold mb-4 text-primary">Get In Touch!</h2>
              
              <div id="client-error"></div>
              
              <?php echo $error; ?>
              <?php echo $successMessage; ?>
              
              <form method="post" id="contactForm" novalidate>
                  
                  <div class="form-floating mb-3">
                      <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" 
                             value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                      <label for="email">Email address</label>
                  </div>
                  
                  <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="Subject" name="Subject" placeholder="Subject"
                             value="<?php echo isset($_POST['Subject']) ? htmlspecialchars($_POST['Subject']) : ''; ?>">
                      <label for="Subject">Subject</label>
                  </div>
                  
                  <div class="form-floating mb-4">
                      <textarea class="form-control" id="textarea" name="textarea" placeholder="Leave a message here" style="height: 150px"><?php echo isset($_POST['textarea']) ? htmlspecialchars($_POST['textarea']) : ''; ?></textarea>
                      <label for="textarea">Your Message</label>
                  </div>
                  
                  <div class="d-grid">
                      <button type="submit" class="btn btn-primary btn-lg" id="submit">Send Message</button>
                  </div>
                  
              </form>
          </div>
      </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  $(document).ready(function(){
      $("#contactForm").submit(function(e){
          let clientErrors = "";
          
          if($.trim($("#email").val()) === ""){
              clientErrors += "<li>Please fill out the email field.</li>";
          }
          if($.trim($("#Subject").val()) === ""){
              clientErrors += "<li>Please provide a subject.</li>";
          }
          if($.trim($("#textarea").val()) === ""){
              clientErrors += "<li>Don't forget to write your message!</li>";
          }

          if(clientErrors !== ""){
              e.preventDefault(); // Stop submission
              // Inject a styled Bootstrap alert instead of raw HTML
              $("#client-error").html(
                  `<div class="alert alert-danger shadow-sm alert-dismissible fade show" role="alert">
                      <strong>Almost there!</strong>
                      <ul class="mb-0">${clientErrors}</ul>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>`
              );
          } else {
              // Clear previous client errors if submission proceeds to PHP
              $("#client-error").html(""); 
          }
      });
  });
  </script>
</body>
</html>
