<?php
// contact.php - Contact form processor

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$to_email = "saide@offsecplatform.com"; // Your email address
$site_name = "HTML শিখি";
$site_url = "https://html.offsecplatform.com";

// Initialize variables
$name = $email = $subject = $message = "";
$errors = [];
$success = false;

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate name
    if (empty($_POST["name"])) {
        $errors[] = "আপনার নাম দিন";
    } else {
        $name = clean_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z\p{Bengali} ]+$/u", $name)) {
            $errors[] = "নামে শুধু অক্ষর এবং স্পেস থাকতে পারে";
        }
    }
    
    // Sanitize and validate email
    if (empty($_POST["email"])) {
        $errors[] = "আপনার ইমেইল দিন";
    } else {
        $email = clean_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "সঠিক ইমেইল ঠিকানা দিন";
        }
    }
    
    // Get subject
    $subject_options = [
        "feedback" => "মতামত",
        "question" => "প্রশ্ন",
        "suggestion" => "পরামর্শ",
        "other" => "অন্যান্য"
    ];
    
    $selected_subject = isset($_POST["subject"]) ? $_POST["subject"] : "";
    $subject_text = isset($subject_options[$selected_subject]) ? $subject_options[$selected_subject] : "যোগাযোগ";
    
    // Get message
    if (empty($_POST["message"])) {
        $errors[] = "আপনার বার্তা দিন";
    } else {
        $message = clean_input($_POST["message"]);
        if (strlen($message) < 10) {
            $errors[] = "বার্তাটি কমপক্ষে ১০ অক্ষরের হতে হবে";
        }
    }
    
    // If no errors, send email
    if (empty($errors)) {
        
        // Prepare email content
        $email_subject = "যোগাযোগ ফর্ম থেকে নতুন বার্তা: $subject_text";
        
        $email_message = "
        <html>
        <head>
            <title>$email_subject</title>
            <meta charset=\"UTF-8\">
            <style>
                body { font-family: 'SolaimanLipi', Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #3498db; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #2c3e50; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\">
                    <h2>$site_name</h2>
                    <p>নতুন যোগাযোগ ফর্ম জমা হয়েছে</p>
                </div>
                <div class=\"content\">
                    <div class=\"field\">
                        <div class=\"label\">নাম:</div>
                        <div>$name</div>
                    </div>
                    <div class=\"field\">
                        <div class=\"label\">ইমেইল:</div>
                        <div>$email</div>
                    </div>
                    <div class=\"field\">
                        <div class=\"label\">বিষয়:</div>
                        <div>$subject_text</div>
                    </div>
                    <div class=\"field\">
                        <div class=\"label\">বার্তা:</div>
                        <div style=\"white-space: pre-wrap;\">$message</div>
                    </div>
                </div>
                <div class=\"footer\">
                    <p>এই বার্তাটি $site_name ওয়েবসাইটের যোগাযোগ ফর্ম থেকে প্রেরিত হয়েছে।</p>
                    <p>সময়: " . date("Y-m-d H:i:s") . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Email headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $name <$email>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Send email
        if (mail($to_email, $email_subject, $email_message, $headers)) {
            $success = true;
            
            // Optional: Save to database or file
            save_to_file($name, $email, $subject_text, $message);
            
        } else {
            $errors[] = "বার্তা পাঠাতে ব্যর্থ হয়েছে। দয়া করে পরে আবার চেষ্টা করুন।";
        }
    }
}

// Function to clean input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to save message to a file (backup)
function save_to_file($name, $email, $subject, $message) {
    $log_file = "contact_submissions.txt";
    $timestamp = date("Y-m-d H:i:s");
    $log_entry = "========================================\n";
    $log_entry .= "সময়: $timestamp\n";
    $log_entry .= "নাম: $name\n";
    $log_entry .= "ইমেইল: $email\n";
    $log_entry .= "বিষয়: $subject\n";
    $log_entry .= "বার্তা:\n$message\n";
    $log_entry .= "========================================\n\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>যোগাযোগ | HTML শিখি</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .contact-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'SolaimanLipi', Arial, sans-serif;
        }
        .contact-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .contact-info p {
            margin: 10px 0;
        }
        .contact-info a {
            color: #3498db;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        
        /* Alert messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .close-alert {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-weight: bold;
            font-size: 20px;
        }
        .close-alert:hover {
            opacity: 0.7;
        }
        
        /* Form validation */
        .form-group.error input,
        .form-group.error textarea,
        .form-group.error select {
            border-color: #dc3545;
        }
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        /* Loading state */
        .btn.loading {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .contact-container {
                margin: 20px;
                padding: 20px;
            }
            .btn-primary, .btn-secondary {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">প্রধান কন্টেন্টে যান</a>

    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="index.html">
                    <img src="images/logo.png" alt="HTML শিখি" width="180" height="50">
                </a>
            </div>
            <nav class="main-nav">
                <button class="mobile-menu-toggle" aria-label="মেনু খুলুন">☰</button>
                <ul class="nav-menu">
                    <li><a href="index.html">হোম</a></li>
                    <li><a href="pages/introduction/">শুরু করুন</a></li>
                    <li><a href="pages/basics/">বেসিক</a></li>
                    <li><a href="pages/forms/">ফর্ম</a></li>
                    <li><a href="pages/exercises/">অনুশীলন</a></li>
                    <li><a href="about.html">আমাদের সম্পর্কে</a></li>
                    <li><a href="contact.php" class="active">যোগাযোগ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <div class="contact-container">
            <h1>যোগাযোগ করুন</h1>
            
            <!-- Display success or error messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span class="close-alert" onclick="this.parentElement.style.display='none'">&times;</span>
                    <strong>ধন্যবাদ!</strong> আপনার বার্তা সফলভাবে পাঠানো হয়েছে। আমরা শীঘ্রই আপনার সাথে যোগাযোগ করব।
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <span class="close-alert" onclick="this.parentElement.style.display='none'">&times;</span>
                    <strong>দয়া করে নিচের ত্রুটিগুলো সংশোধন করুন:</strong>
                    <ul style="margin-top: 10px; margin-bottom: 0;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="contact-info">
                <h2>আমাদের ঠিকানা</h2>
                <p><strong>ইমেইল:</strong> <a href="mailto:saide@offsecplatform.com">saide@offsecplatform.com</a></p>
                <p><strong>ফোন:</strong> <a href="tel:+8801742271463">+8801742271463</a></p>
                <p><strong>ঠিকানা:</strong> ঢাকা, বাংলাদেশ</p>
                <p><strong>ফেসবুক:</strong> <a href="https://facebook.com/offsecplatform" target="_blank">/offsecplatform</a></p>
                <p><strong>ইউটিউব:</strong> <a href="https://youtube.com/@saide-hossain" target="_blank">@saide-hossain</a></p>
            </div>

            <h2>আপনার মতামত দিন</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="contactForm">
                <div class="form-group <?php echo (isset($errors) && in_array("আপনার নাম দিন", $errors)) ? 'error' : ''; ?>">
                    <label for="name">আপনার নাম *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group <?php echo (isset($errors) && in_array("আপনার ইমেইল দিন", $errors)) ? 'error' : ''; ?>">
                    <label for="email">ইমেইল ঠিকানা *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="subject">বিষয়</label>
                    <select id="subject" name="subject">
                        <option value="">বেছে নিন</option>
                        <option value="feedback" <?php echo ($selected_subject == "feedback") ? 'selected' : ''; ?>>মতামত</option>
                        <option value="question" <?php echo ($selected_subject == "question") ? 'selected' : ''; ?>>প্রশ্ন</option>
                        <option value="suggestion" <?php echo ($selected_subject == "suggestion") ? 'selected' : ''; ?>>পরামর্শ</option>
                        <option value="other" <?php echo ($selected_subject == "other") ? 'selected' : ''; ?>>অন্যান্য</option>
                    </select>
                </div>

                <div class="form-group <?php echo (isset($errors) && in_array("আপনার বার্তা দিন", $errors)) ? 'error' : ''; ?>">
                    <label for="message">বার্তা *</label>
                    <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary" id="submitBtn">পাঠান</button>
                    <button type="reset" class="btn-secondary">মুছে ফেলুন</button>
                </div>
            </form>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; ২০২৬ HTML শিখি। সকল অধিকার সংরক্ষিত।</p>
            </div>
        </div>
    </footer>
    
    <script src="js/main.js"></script>
    <script>
        // Form validation and AJAX submission (optional)
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Client-side validation
                    const name = document.getElementById('name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const message = document.getElementById('message').value.trim();
                    let hasError = false;
                    
                    // Clear previous error messages
                    document.querySelectorAll('.error-message').forEach(el => el.remove());
                    document.querySelectorAll('.form-group').forEach(el => el.classList.remove('error'));
                    
                    if (!name) {
                        showError('name', 'আপনার নাম দিন');
                        hasError = true;
                    }
                    
                    if (!email) {
                        showError('email', 'আপনার ইমেইল দিন');
                        hasError = true;
                    } else if (!isValidEmail(email)) {
                        showError('email', 'সঠিক ইমেইল ঠিকানা দিন');
                        hasError = true;
                    }
                    
                    if (!message) {
                        showError('message', 'আপনার বার্তা দিন');
                        hasError = true;
                    } else if (message.length < 10) {
                        showError('message', 'বার্তাটি কমপক্ষে ১০ অক্ষরের হতে হবে');
                        hasError = true;
                    }
                    
                    if (hasError) {
                        e.preventDefault();
                        // Scroll to top of form
                        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        // Show loading state
                        submitBtn.classList.add('loading');
                        submitBtn.textContent = 'পাঠানো হচ্ছে...';
                    }
                });
                
                // Auto-dismiss alerts after 5 seconds
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 300);
                    }, 5000);
                });
            }
            
            function showError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const formGroup = field.closest('.form-group');
                formGroup.classList.add('error');
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                formGroup.appendChild(errorDiv);
            }
            
            function isValidEmail(email) {
                const re = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
                return re.test(email);
            }
        });
    </script>
</body>
</html>