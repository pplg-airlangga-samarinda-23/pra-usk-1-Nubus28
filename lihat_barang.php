<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

$barang_list = [];
$result = $koneksi->query("SELECT * FROM barang ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $barang_list[] = $row;
    }
}

// Hapus barang
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $koneksi->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: lihat_barang.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Kasir</title>
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
                <h2>Data Barang</h2>
                <p>Kelola semua produk toko</p>
            </div>
        </div>

        <div class="toolbar">
            <a href="tambah_barang.php" class="btn">➕ Tambah Barang Baru</a>
            <a href="admin.php" class="btn btn-secondary">← Kembali</a>
        </div>
        
        <div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead></a> 
                    <a href="lihat_barang.php?delete=<?php echo $b['id']; ?>" class="btn-danger" onclick="return confirm('Hapus barang ini?')">🗑️
            <?php foreach ($barang_list as $b): ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><?php echo htmlspecialchars($b['kode_barang']); ?></td>
                <td><?php echo htmlspecialchars($b['nama_barang']); ?></td>
                <td>Rp <?php echo number_format($b['harga']); ?></td>
                <td>
                    <a href="edit_barang.php?id=<?php echo $b['id']; ?>">✏️ Edit</a>
                    <a href="lihat_barang.php?delete=<?php echo $b['id']; ?>" class="btn-delete" onclick="return confirm('Hapus barang ini?')">🗑️ Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($barang_list)): ?>
    <p style="text-align: center; color: #999;">Belum ada barang. <a href="tambah_barang.php">Tambah sekarang</a></p>
    <?php endif; ?>
</body>
</html>
