<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
    <?php
    require_once "cls/product.php";
    require_once "cls/transact.php";
    
    $productDB = new Product();
    $transactionDB = new Transaction();
    
    $message = "";
    $error = "";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_produk = $_POST['id_produk'] ?? '';
        $jumlah = $_POST['jumlah'] ?? '';
        
        if (!empty($_POST['tanggal'])) {
            $tanggal = str_replace('T', ' ', $_POST['tanggal']) . ':00';
        } else {
            $tanggal = date('Y-m-d H:i:s');
        }
        
        if (empty($id_produk) || empty($jumlah)) {
            $error = "Produk dan jumlah harus diisi!";
        } elseif ($jumlah <= 0) {
            $error = "Jumlah harus lebih dari 0!";
        } else {
            // Cek stok produk
            $product = $productDB->getProductById($id_produk);
            if (!$product) {
                $error = "Produk tidak ditemukan!";
            } elseif ($product['stok'] < $jumlah) {
                $error = "Stok tidak cukup! Stok tersedia: " . $product['stok'];
            } else {
                $data = array(
                    'tanggal' => $tanggal,
                    'products' => array(
                        array(
                            'id_produk' => $id_produk,
                            'jumlah' => $jumlah
                        )
                    )
                );
                
                if ($transactionDB->addTransaction($data)) {
                    $message = "Transaksi berhasil ditambahkan!";
                    echo "<script>setTimeout(function(){ window.location.href = 'dash.php'; }, 2000);</script>";
                    $_POST = array();
                } else {
                    $error = "Gagal menambahkan transaksi!";
                }
            }
        }
    }
    
    $products = $productDB->getAllProducts();
    ?>
</head>
<body>
    <h1>Transaksi</h1>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <h2>Tambah Transaksi</h2>
    <form method="POST">
        <label>Pilih Produk:</label><br>
        <select name="id_produk" required>
            <option value="">-- Pilih Produk --</option>
            <?php
            if ($products && $products->num_rows > 0) {
                while ($product = $products->fetch_assoc()) {
                    $selected = (($_POST['id_produk'] ?? '') == $product['id']) ? 'selected' : '';
                    echo "<option value='" . $product['id'] . "' " . $selected . ">";
                    echo htmlspecialchars($product['nama']) . " (Stok: " . $product['stok'] . " pcs)";
                    echo "</option>";
                }
            }
            ?>
        </select><br><br>
        
        <label>Jumlah:</label><br>
        <input type="number" name="jumlah" min="1" value="<?php echo htmlspecialchars($_POST['jumlah'] ?? ''); ?>" required><br><br>
        
        <label>Tanggal (opsional):</label><br>
        <input type="datetime-local" name="tanggal" value="<?php echo htmlspecialchars($_POST['tanggal'] ?? ''); ?>"><br><br>
        
        <button type="submit">Simpan Transaksi</button>
    </form>
    
    <br>
    <a href="dash.php">Kembali ke Dashboard</a>
</body>
</html>
