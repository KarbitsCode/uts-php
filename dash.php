<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    
    $products = $productDB->getAllProducts();
    
    $transResult = $transactionDB->getAllTransactions();
    $totalTransactions = 0;
    $transactionIds = array();
    
    if ($transResult) {
        while ($row = $transResult->fetch_assoc()) {
            if (!in_array($row['id_transaksi'], $transactionIds)) {
                $transactionIds[] = $row['id_transaksi'];
                $totalTransactions++;
            }
        }
    }
    ?>
</head>
<body>
    <h1>Dashboard</h1>
    
    <h2>Statistik</h2>
    <p>
        <span>Total Produk: <?php echo $products->num_rows; ?></span><br>
        <span>Total Transaksi: <?php echo $totalTransactions; ?></span>
    </p>
    
    <h2>Daftar Produk</h2>
    <table border="1">
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jenis</th>
            <th>Harga</th>
            <th>Stok</th>
        </tr>
        <?php
        if ($products && $products->num_rows > 0) {
            $products->data_seek(0);
            $no = 1;
            while ($product = $products->fetch_assoc()) {
                $rowStyle = ($product['stok'] < 5) ? 'style="background-color: #ffcccc;"' : '';
                echo "<tr " . $rowStyle . ">";
                echo "<td>" . $no . "</td>";
                echo "<td>" . htmlspecialchars($product['nama']) . "</td>";
                echo "<td>" . $product['jenis'] . "</td>";
                echo "<td>Rp " . number_format($product['harga'], 0, ',', '.') . "</td>";
                
                if ($product['stok'] < 5) {
                    echo "<td>" . $product['stok'] . " pcs (STOK MENIPIS)</td>";
                } else {
                    echo "<td>" . $product['stok'] . " pcs</td>";
                }
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='5'>Belum ada produk</td></tr>";
        }
        ?>
    </table>
    
    <h2>Daftar Transaksi</h2>
    <table border="1">
        <tr>
            <th>No</th>
            <th>ID Transaksi</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Tanggal</th>
        </tr>
        <?php
        if ($transResult) {
            $transResult->data_seek(0);
            $no = 1;
            while ($trans = $transResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $trans['id_transaksi'] . "</td>";
                echo "<td>" . htmlspecialchars($trans['nama']) . "</td>";
                echo "<td>" . $trans['jumlah'] . " pcs</td>";
                echo "<td>Rp " . number_format($trans['harga'], 0, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($trans['subtotal'], 0, ',', '.') . "</td>";
                echo "<td>" . $trans['tanggal'] . "</td>";
                echo "</tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='7'>Belum ada transaksi</td></tr>";
        }
        ?>
    </table>

    <hr>
    <button onclick="window.location.href='form.php'">Tambah Produk</button> | <button onclick="window.location.href='transaksi.php'">Tambah Transaksi</button>
</body>
</html>
