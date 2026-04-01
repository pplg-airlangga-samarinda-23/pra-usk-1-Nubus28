<?php
include "koneksi.php";
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $koneksi->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
        
        if (!$stmt) {
            $message = "Error prepare: " . $koneksi->error;
        } else {
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'admin') {
                    header("Location: admin.php", true, 302);
                } else {
                    header("Location: petugas.php", true, 302);
                }
                exit();
            } else {
                $message = "Login gagal! Username atau password salah. (User: " . htmlspecialchars($username) . ")";
            }
            $stmt->close();
        }
    } else {
        $message = "Username dan password harus diisi!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="logo-section">
            <svg width="100" height="100" viewBox="0 0 100 100" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                <!-- Background circle -->
                <circle cx="50" cy="50" r="45" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="2"/>
                <!-- Store icon -->
                <rect x="25" y="35" width="50" height="35" rx="3" fill="white" opacity="0.9"/>
                <rect x="25" y="35" width="50" height="15" rx="3" fill="rgba(255,255,255,0.6)"/>
                <!-- Door -->
                <rect x="40" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                <circle cx="48" cy="60" r="1.5" fill="white"/>
                <rect x="52" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                <circle cx="56" cy="60" r="1.5" fill="white"/>
                <!-- Windows on top -->
                <rect x="30" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                <rect x="40" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                <rect x="50" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                <rect x="60" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
            </svg>
        </div>
        <div class="form-section">
            <h2>Login Kasir</h2>
            <p class="subtitle">Masukkan kredensial Anda</p>
            
            <?php if ($message): ?>
                <div class="message error">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login →</button>
            </form>
            
            <div class="footer">
                <p>&copy; 2026 Sistem Kasir Toko</p>
            </div>
        </div>
    </div>
</body>
</html>
