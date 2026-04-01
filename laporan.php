<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$transaksi_list = [];
$total_penjualan = 0;

// Get all transactions with details
$sql = "SELECT 
    t.id, 
    t.tanggal, 
    t.total, 
    u.username as petugas,
    COUNT(td.id) as jumlah_item
FROM transaksi t
LEFT JOIN users u ON t.petugas_id = u.id
LEFT JOIN transaksi_detail td ON t.id = td.transaksi_id
GROUP BY t.id
ORDER BY t.tanggal DESC";

$result = $koneksi->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $transaksi_list[] = $row;
        $total_penjualan += $row['total'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="content-page">
    <div class="container">
        <div class="header">
            <div class="logo-box">
                <svg width="50" height="50" viewBox="0 0 100 100" style="filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));">
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
                <h2>Laporan Penjualan</h2>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-value"><?php echo count($transaksi_list); ?></div>
                <div class="stat-label">Total Transaksi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">Rp <?php echo number_format($total_penjualan); ?></div>
                <div class="stat-label">Total Penjualan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value">Rp <?php echo count($transaksi_list) > 0 ? number_format($total_penjualan / count($transaksi_list)) : 0; ?></div>
                <div class="stat-label">Rata-rata Transaksi</div>
            </div>
        </div>

        <div class="card">

    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Petugas</th>
                <th>Jumlah Item</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transaksi_list)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #999;">Belum ada transaksi</td>
            </tr>
            <?php else: ?>
                <?php foreach ($transaksi_list as $t): ?>
                <tr>
                    </div>
    </div>                    <td><?php echo date('d/m/Y H:i', strtotime($t['tanggal'])); ?></td>
                    <td><?php echo htmlspecialchars($t['petugas'] ?? 'Unknown'); ?></td>
                    <td><?php echo $t['jumlah_item']; ?> item</td>
                    <td>Rp <?php echo number_format($t['total']); ?></td>
                    <td><a href="detail_transaksi.php?id=<?php echo $t['id']; ?>" class="view-link">👁️ Lihat</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admin.php" class="back-link">← Kembali</a>
</body>
</html>
