<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

// Get stats
$statsBarang = $koneksi->query("SELECT COUNT(*) as total FROM barang");
$rowBarang = $statsBarang->fetch_assoc();

$statsTransaksi = $koneksi->query("SELECT COUNT(*) as total, SUM(total) as jumlah FROM transaksi");
$rowTransaksi = $statsTransaksi->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-page">
    <div class="container">
        <div class="header">
            <div class="logo-box">
                <svg width="60" height="60" viewBox="0 0 100 100" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));">
                    <circle cx="50" cy="50" r="45" fill="rgba(255,255,255,0.2)" stroke="white" stroke-width="2"/>
                    <rect x="25" y="35" width="50" height="35" rx="3" fill="white" opacity="0.9"/>
                    <rect x="25" y="35" width="50" height="15" rx="3" fill="rgba(255,255,255,0.6)"/>
                    <rect x="40" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                    <circle cx="48" cy="60" r="1.5" fill="white"/>
                    <rect x="52" y="50" width="8" height="20" fill="rgba(168,213,186,0.8)"/>
                    <circle cx="56" cy="60" r="1.5" fill="white"/>
                    <rect x="30" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="40" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="50" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                    <rect x="60" y="38" width="6" height="6" fill="rgba(200,230,215,0.7)"/>
                </svg>
            </div>
            <div class="header-content">
                <div>
                    <h1>Dashboard Admin</h1>
                    <p>Selamat datang, Admin</p>
                </div>
                <a href="login.php" class="logout">🚪 Logout</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?php echo $rowBarang['total']; ?></div>
                <div class="stat-label">Total Barang</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">Rp <?php echo number_format($rowTransaksi['jumlah'] ?? 0); ?></div>
                <div class="stat-label">Total Penjualan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo $rowTransaksi['total'] ?? 0; ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>

        <div class="menu-section">
            <div class="menu-card">
                <h3>📋 Kelola Barang</h3>
                <a href="tambah_barang.php" class="menu-link">➕ Tambah Barang Baru</a>
                <a href="lihat_barang.php" class="menu-link">📋 Lihat Semua Barang</a>
            </div>

            <div class="menu-card">
                <h3>📊 Laporan</h3>
                <a href="laporan.php" class="menu-link">📈 Laporan Penjualan</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2026 Sistem Kasir Toko - Versi 1.0</p>
        </div>
    </div>
</body>
</html>
