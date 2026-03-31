<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Success</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
    }

    .success-container {
      text-align: center;
    }

    .check-icon {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: #4CAF50;
      color: white;
      font-size: 60px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto 20px;
    }

    .message {
      font-size: 22px;
      color: #333;
      margin-bottom: 30px;
    }

    .home-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      font-size: 16px;
      color: white;
      background-color: #4CAF50;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.2s ease;
    }

    .home-btn:hover {
      background-color: #43a047;
    }

    .home-icon {
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="success-container">
    <div class="check-icon">✓</div>
    <div class="message">Congratulations! your application is submitted.</div>

    <a href="<?= base_url() ?>" class="home-btn">
      Back to Home
    </a>
  </div>
</body>
</html>
