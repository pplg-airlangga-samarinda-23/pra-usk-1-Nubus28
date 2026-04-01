<?php
session_start();

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_POST['barang_id'])) {
    $barang_id = $_POST['barang_id'];
    $jumlah = $_POST['jumlah'];
    
    include "koneksi.php";
    
    $stmt = $koneksi->prepare("SELECT id, nama_barang, harga FROM barang WHERE id = ?");
    $stmt->bind_param("i", $barang_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $barang = $result->fetch_assoc();
        
        // Check if item already in cart
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
                'kode' => $barang_id,
                'nama' => $barang['nama_barang'],
                'harga' => $barang['harga'],
                'jumlah' => $jumlah
            ];
        }
    }
    $stmt->close();
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], function($item) use ($id) {
        return $item['id'] != $id;
    }));
}

// Clear cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
}

// Return cart data as JSON
header('Content-Type: application/json');
echo json_encode($_SESSION['cart']);
?>
