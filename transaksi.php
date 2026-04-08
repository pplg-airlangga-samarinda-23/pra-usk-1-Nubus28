<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    die("Akses ditolak!");
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = "";
$barang_list = [];

// Add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $barang_id = $_POST['barang_id'] ?? '';
    $jumlah = intval($_POST['jumlah'] ?? 0);

    if (!empty($barang_id) && $jumlah > 0) {
        $stmt = $koneksi->prepare("SELECT id, nama_barang, harga, stok FROM barang WHERE id = ?");
        $stmt->bind_param("i", $barang_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $barang = $result->fetch_assoc();
            $currentCartQty = 0;
            foreach ($_SESSION['cart'] as $item) {
                if ($item['id'] == $barang_id) {
                    $currentCartQty = $item['jumlah'];
                    break;
                }
            }

            if ($currentCartQty + $jumlah > $barang['stok']) {
                $message = "❌ Stok tidak cukup. Tersedia: " . $barang['stok'] . ".";
            } else {
                $found = false;
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $barang_id) {
                        $item['jumlah'] += $jumlah;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $_SESSION['cart'][] = [
                        'id' => $barang['id'],
                        'nama' => $barang['nama_barang'],
                        'harga' => $barang['harga'],
                        'jumlah' => $jumlah
                    ];
                }
                $message = "✅ " . $barang['nama_barang'] . " ditambahkan ke keranjang!";
            }
        } else {
            $message = "❌ Barang tidak ditemukan!";
        }
        $stmt->close();
    }
}

// Remove from cart
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function($item) use ($id) {
        return $item['id'] != $id;
    }));
}

// Checkout
if (isset($_POST['action']) && $_POST['action'] == 'checkout') {
    if (!empty($_SESSION['cart'])) {
        $petugas_id = $_SESSION['id'] ?? 0;
        $total = 0;

        // Calculate total
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['harga'] * $item['jumlah'];
        }

        $koneksi->begin_transaction();
        $validStock = true;

        $stmt = $koneksi->prepare("INSERT INTO transaksi (total, petugas_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $total, $petugas_id);

        if ($stmt->execute()) {
            $transaksi_id = $stmt->insert_id;

            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['harga'] * $item['jumlah'];

                $stmtUpdate = $koneksi->prepare("UPDATE barang SET stok = stok - ? WHERE id = ? AND stok >= ?");
                $stmtUpdate->bind_param("iii", $item['jumlah'], $item['id'], $item['jumlah']);
                $stmtUpdate->execute();

                if ($stmtUpdate->affected_rows === 0) {
                    $validStock = false;
                    $stmtUpdate->close();
                    break;
                }
                $stmtUpdate->close();

                $stmtDetail = $koneksi->prepare("INSERT INTO transaksi_detail (transaksi_id, barang_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
                $stmtDetail->bind_param("iiii", $transaksi_id, $item['id'], $item['jumlah'], $subtotal);
                $stmtDetail->execute();
                $stmtDetail->close();
            }

            if ($validStock) {
                $koneksi->commit();
                $_SESSION['cart'] = [];
                $message = "✅ Transaksi berhasil! Total: Rp" . number_format($total);
            } else {
                $koneksi->rollback();
                $message = "❌ Stok tidak cukup untuk melakukan checkout. Silakan periksa kembali keranjang.";
            }
        } else {
            $koneksi->rollback();
            $message = "❌ Gagal membuat transaksi. Silakan coba lagi.";
        }

        $stmt->close();
    } else {
        $message = "❌ Keranjang kosong!";
    }
}

// Get barang list
$resultBarang = $koneksi->query("SELECT id, nama_barang, harga, stok FROM barang ORDER BY nama_barang");
if ($resultBarang) {
    while ($row = $resultBarang->fetch_assoc()) {
        $barang_list[] = $row;
    }
}

// Calculate cart total
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['harga'] * $item['jumlah'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Penjualan - Kasir</title>
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
                <h2>Transaksi Penjualan</h2>
            </div>
            <a href="petugas.php" class="back-header">← Kembali</a>
        </div>
        <!-- Form Tambah Item -->
        <div class="card">
            <h3>💳 Pilih Barang</h3>
            
            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Pilih Barang:</label>
                    <select name="barang_id" required>
                        <option value="">-- Pilih Barang --</option>
                        <?php foreach ($barang_list as $b): ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo $b['stok'] <= 0 ? 'disabled' : ''; ?> >
                                <?php echo $b['nama_barang'] . " | Rp" . number_format($b['harga']) . " | Stok: " . intval($b['stok']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Jumlah:</label>
                    <input type="number" name="jumlah" min="1" value="1" required>
                </div>
                
                <button type="submit">➕ Tambah ke Keranjang</button>
            </form>
        </div>

        <!-- Keranjang Belanja -->
        <div class="card">
            <h3>🛒 Keranjang Belanja</h3>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="cart-empty">Keranjang kosong</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nama']); ?></td>
                            <td>Rp <?php echo number_format($item['harga']); ?></td>
                            <td><?php echo $item['jumlah']; ?></td>
                            <td>Rp <?php echo number_format($item['harga'] * $item['jumlah']); ?></td>
                            <td><a href="transaksi.php?remove=<?php echo $item['id']; ?>" onclick="return confirm('Hapus item ini?')">❌</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-total">
                    Total: Rp <?php echo number_format($cart_total); ?>
                </div>

                <div class="cart-actions">
                    <form method="post" style="flex: 1;">
                        <input type="hidden" name="action" value="checkout">
                        <button type="submit" style="width: 100%;">✅ Checkout</button>
                    </form>
                    <a href="transaksi.php?clear=1" class="warning" style="flex: 1; padding: 10px;">🔄 Bersihkan</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
