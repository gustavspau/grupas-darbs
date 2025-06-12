<?php
require_once 'auth.php';
require_once 'config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts pievienot lietotājus']);
    exit;
}
$data = json_decode(file_get_contents('php:
function validateUserData($data, $pdo, $isEdit = false, $userId = null) {
    $errors = [];
    $required_field_mapping = [
        'first_name' => ['field' => 'firstName', 'message' => 'Vārds ir obligāts'],
        'last_name' => ['field' => 'lastName', 'message' => 'Uzvārds ir obligāts'],
        'email' => ['field' => 'email', 'message' => 'E-pasts ir obligāts'],
        'password' => ['field' => 'password', 'message' => 'Parole ir obligāta'],
        'role' => ['field' => 'role', 'message' => 'Loma ir obligāta']
    ];
    foreach ($required_field_mapping as $field => $error_info) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = $error_info;
        }
    }
    if (!empty($errors)) {
        return $errors;
    }
    $first_name = trim($data['first_name']);
    if (strlen($first_name) < 2) {
        $errors[] = ['field' => 'firstName', 'message' => 'Vārdam jābūt vismaz 2 simboliem'];
    } elseif (strlen($first_name) > 50) {
        $errors[] = ['field' => 'firstName', 'message' => 'Vārds nedrīkst pārsniegt 50 simbolus'];
    } elseif (!preg_match('/^[\p{L}\s\-\']+$/u', $first_name)) {
        $errors[] = ['field' => 'firstName', 'message' => 'Vārdā drīkst būt tikai burti, atstarpes un defises'];
    } elseif (preg_match('/^\s|\s$/', $first_name)) {
        $errors[] = ['field' => 'firstName', 'message' => 'Vārds nedrīkst sākties vai beigties ar atstarpi'];
    }
    $last_name = trim($data['last_name']);
    if (strlen($last_name) < 2) {
        $errors[] = ['field' => 'lastName', 'message' => 'Uzvārdam jābūt vismaz 2 simboliem'];
    } elseif (strlen($last_name) > 50) {
        $errors[] = ['field' => 'lastName', 'message' => 'Uzvārds nedrīkst pārsniegt 50 simbolus'];
    } elseif (!preg_match('/^[\p{L}\s\-\']+$/u', $last_name)) {
        $errors[] = ['field' => 'lastName', 'message' => 'Uzvārdā drīkst būt tikai burti, atstarpes un defises'];
    } elseif (preg_match('/^\s|\s$/', $last_name)) {
        $errors[] = ['field' => 'lastName', 'message' => 'Uzvārds nedrīkst sākties vai beigties ar atstarpi'];
    }
    $email = trim(strtolower($data['email']));
    if (strlen($email) > 254) {
        $errors[] = ['field' => 'email', 'message' => 'E-pasta adrese pārāk gara (maksimums 254 simboli)'];
    } else {
        $emailRegex = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
        if (!preg_match($emailRegex, $email)) {
            $errors[] = ['field' => 'email', 'message' => 'Nederīgs e-pasta formāts'];
        } elseif (strpos($email, '..') !== false) {
            $errors[] = ['field' => 'email', 'message' => 'E-pasta adresē nedrīkst būt divi punkti pēc kārtas'];
        } elseif (strpos($email, '@.') !== false || strpos($email, '.@') !== false || strpos($email, '.') === 0) {
            $errors[] = ['field' => 'email', 'message' => 'Nederīgs e-pasta formāts'];
        } else {
            $localPart = explode('@', $email)[0];
            if (strlen($localPart) > 64) {
                $errors[] = ['field' => 'email', 'message' => 'E-pasta vārds pārāk garš (maksimums 64 simboli)'];
            }
        }
    }
    $password = $data['password'];
    if (strlen($password) < 8) {
        $errors[] = ['field' => 'password', 'message' => 'Parolei jābūt vismaz 8 simboliem'];
    } elseif (strlen($password) > 128) {
        $errors[] = ['field' => 'password', 'message' => 'Parole pārāk gara (maksimums 128 simboli)'];
    } else {
        $strengthErrors = [];
        if (!preg_match('/[a-z]/', $password)) {
            $strengthErrors[] = 'mazo burtu';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $strengthErrors[] = 'lielo burtu';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $strengthErrors[] = 'ciparu';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $strengthErrors[] = 'speciālo simbolu';
        }
        if (!empty($strengthErrors)) {
            $errors[] = ['field' => 'password', 'message' => 'Parolei jāsatur: ' . implode(', ', $strengthErrors)];
        }
        $commonPasswords = ['password', '123456', 'qwerty', 'admin', 'letmein'];
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = ['field' => 'password', 'message' => 'Parole pārāk vienkārša - izvēlieties sarežģītāku'];
        }
    }
    $validRoles = ['admin', 'warehouse', 'shelf'];
    if (!in_array($data['role'], $validRoles)) {
        $errors[] = ['field' => 'role', 'message' => 'Izvēlētā loma nav derīga'];
    }
    try {
        if ($isEdit) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        if ($stmt->rowCount() > 0) {
            $errors[] = ['field' => 'email', 'message' => 'Šāds e-pasts jau eksistē sistēmā'];
        }
    } catch (PDOException $e) {
        error_log("Database error checking email uniqueness: " . $e->getMessage());
        $errors[] = 'Kļūda pārbaudot e-pasta unikalitāti';
    }
    return $errors;
}
$validation_errors = validateUserData($data, $pdo);
if (!empty($validation_errors)) {
    $field_errors = [];
    $general_errors = [];
    foreach ($validation_errors as $error) {
        if (is_array($error) && isset($error['field']) && isset($error['message'])) {
            $field_errors[] = $error;
        } else {
            $general_errors[] = (string)$error;
        }
    }
    http_response_code(422);
    echo json_encode([
        'error' => 'Validācijas kļūdas',
        'validation_errors' => $field_errors,
        'general_errors' => $general_errors
    ]);
    exit;
}
try {
    $first_name = trim($data['first_name']);
    $last_name = trim($data['last_name']);
    $email = trim(strtolower($data['email']));
    $role = $data['role'];
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    $username = explode('@', $email)[0];
    $base_username = $username;
    $counter = 1;
    while (true) {
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->execute([$username]);
        if ($stmt_check->rowCount() == 0) {
            break;
        }
        $username = $base_username . $counter;
        $counter++;
    }
    $stmt = $pdo->prepare("
        INSERT INTO users (username, first_name, last_name, email, password, role, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $username,
        $first_name,
        $last_name,
        $email,
        $hashed_password,
        $role
    ]);
    $user_id = $pdo->lastInsertId();
    echo json_encode([
        'success' => true,
        'message' => 'Lietotājs veiksmīgi pievienots',
        'user' => [
            'id' => $user_id,
            'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'role' => $role
        ]
    ]);
} catch (PDOException $e) {
    error_log("Database error in add_user.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Sistēmas kļūda. Lūdzu mēģiniet vēlāk.']);
}
?>
