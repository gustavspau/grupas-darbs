<?php
require_once 'auth.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piekļuve liegta - Noliktavas Vadības Sistēma</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }

        .error-icon {
            font-size: 5rem;
            color: #ff6b6b;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .back-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="fas fa-ban error-icon"></i>
        <h1 class="error-title">Piekļuve liegta</h1>
        <p class="error-message">
            Jums nav atļauja piekļūt šai lapai.<br>
            Lūdzu sazinieties ar administratoru, ja uzskatāt, ka tas ir kļūda.
        </p>
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Atgriezties uz sākumu
        </a>
    </div>
</body>
</html> 