<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$detail_list = [];
$transaksi = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $koneksi->prepare("SELECT t.*, u.username as petugas FROM transaksi t LEFT JOIN users u ON t.petugas_id = u.id WHERE t.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transaksi = $result->fetch_assoc();
        
        $stmt2 = $koneksi->prepare("SELECT td.*, b.nama_barang FROM transaksi_detail td LEFT JOIN barang b ON td.barang_id = b.id WHERE td.transaksi_id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        while ($row = $result2->fetch_assoc()) {
            $detail_list[] = $row;
        }
        $stmt2->close();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Kasir</title>
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
                <h2>Detail Transaksi</h2>
            </div>
        </div>

        <?php if ($transaksi): ?>
        <div class="card">
            <div class="info">
                <div class="info-item">
                    <div class="info-label">ID Transaksi</div>
                    <div class="info-value">#<?php echo $transaksi['id']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($transaksi['tanggal'])); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Petugas</div>
                    <div class="info-value"><?php echo htmlspecialchars($transaksi['petugas'] ?? 'Unknown'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">✅ Selesai</div>
                </div>
            </div>

            <h3>Daftar Item</h3>
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detail_list as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['nama_barang'] ?? 'Unknown'); ?></td>
                        <td><?php echo $d['jumlah']; ?></td>
                        <td>Rp <?php echo number_format($d['subtotal'] / $d['jumlah']); ?></td>
                        <td>Rp <?php echo number_format($d['subtotal']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-section">
                Total: Rp <?php echo number_format($transaksi['total']); ?>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <p style="color: red;">Transaksi tidak ditemukan!</p>
        </div>
        <?php endif; ?>
    </div>

    <a href="laporan.php" class="back-link">← Kembali</a>
</body>
</html>
