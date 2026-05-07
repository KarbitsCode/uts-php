<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <?php
    require_once "cls/product.php";
    
    $productDB = new Product();
    $message = "";
    $error = "";
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = array(
            'nama' => $_POST['nama'] ?? '',
            'jenis' => $_POST['jenis'] ?? '',
            'harga' => $_POST['harga'] ?? '',
            'stok' => $_POST['stok'] ?? ''
        );
        
        if (empty($data['nama']) || empty($data['jenis']) || empty($data['harga']) || empty($data['stok'])) {
            $error = "Semua field harus diisi!";
        } elseif ($data['harga'] <= 0 || $data['stok'] < 0) {
            $error = "Harga harus lebih dari 0 dan stok tidak boleh negatif!";
        } else {
            if ($productDB->addProduct($data)) {
                $message = "Produk berhasil ditambahkan!";
                echo "<script>setTimeout(function(){ window.location.href = 'dash.php'; }, 2000);</script>";
                $_POST = array();
            } else {
                $error = "Gagal menambahkan produk!";
            }
        }
    }
    ?>
</head>
<body>
    <h1>Tambah Produk</h1>
    
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Nama Produk:</label><br>
        <input type="text" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required><br><br>
        
        <label>Jenis:</label><br>
        <select name="jenis" required>
            <option value="">-- Pilih Jenis --</option>
            <option value="Laptop" <?php echo (($_POST['jenis'] ?? '') == 'Laptop') ? 'selected' : ''; ?>>Laptop</option>
            <option value="Smartphone" <?php echo (($_POST['jenis'] ?? '') == 'Smartphone') ? 'selected' : ''; ?>>Smartphone</option>
        </select><br><br>
        
        <label>Harga:</label><br>
        <input type="number" name="harga" step="0.01" value="<?php echo htmlspecialchars($_POST['harga'] ?? ''); ?>" required><br><br>
        
        <label>Stok:</label><br>
        <input type="number" name="stok" min="0" value="<?php echo htmlspecialchars($_POST['stok'] ?? ''); ?>" required><br><br>
        
        <button type="submit">Simpan Produk</button>
        <button type="button" onclick="window.location.href='dash.php'">Kembali</button>
    </form>
</body>
</html>
