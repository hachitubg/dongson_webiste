<?php
// admin/login.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Sai username hoặc password';
    }
}
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/_head.php'; ?>
  <title>Đăng Nhập - Admin</title>
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }
    .login-wrapper {
      width: 100%;
      max-width: 420px;
      padding: 20px;
    }
    .login-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      padding: 48px 40px;
      animation: slideUp 0.4s ease;
    }
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .login-header {
      text-align: center;
      margin-bottom: 32px;
    }
    .login-header .logo {
      max-width: 200px;
      height: auto;
      margin: 0 auto 16px auto;
      display: block;
    }
    .login-header p {
      margin: 0;
      color: #666;
      font-size: 14px;
      font-weight: 500;
    }
    .error-message {
      background: #fee;
      border: 1px solid #fcc;
      color: #c33;
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .error-message::before {
      content: '⚠️';
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
      font-size: 14px;
    }
    .form-group input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    .form-group input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .btn-login {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 8px;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    .btn-login:active {
      transform: translateY(0);
    }
    .login-footer {
      text-align: center;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 1px solid #e0e0e0;
    }
    .login-footer p {
      margin: 0;
      color: #888;
      font-size: 13px;
    }
    .login-footer strong {
      color: #667eea;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-header">
        <img src="../admin/images/logo.png" alt="ĐÔNG SƠN LOGO" class="logo">
        <p>Hệ Thống Quản Trị</p>
      </div>
      
      <?php if ($error): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <form method="post">
        <div class="form-group">
          <label>Tên Đăng Nhập</label>
          <input name="username" type="text" placeholder="Nhập tên đăng nhập" required autocomplete="username">
        </div>
        
        <div class="form-group">
          <label>Mật Khẩu</label>
          <input name="password" type="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
        </div>
        
        <button type="submit" class="btn-login">Đăng Nhập</button>
      </form>
    </div>
  </div>
</body>
</html>
