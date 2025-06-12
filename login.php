<?php
require_once 'auth.php';
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) {
        $error_message = 'Lūdzu ievadiet e-pastu un paroli.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Lūdzu ievadiet derīgu e-pasta adresi.';
    } else {
        if (login($email, $password)) {
            header("Location: index.php");
            exit();
        } else {
            $error_message = 'Nepareizs e-pasts vai parole.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ielogošanās - Noliktavas Vadības Sistēma</title>
    <link rel="stylesheet" href="styles.css?v=3.1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-warehouse"></i>
            <h1>Noliktavas Vadība</h1>
            <p>Ielogojieties sistēmā</p>
        </div>
        <?php if ($error_message): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">E-pasts</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Parole</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Ielogoties
            </button>
        </form>
        <div class="footer-note">
            Noliktavas vadības sistēma<br>
            Versija 1.0
        </div>
    </div>
</body>
</html>
