<?php
require_once "cfg/dbs.php";
require_once "cls/product.php";

class Transaction extends Database {
    public function getAllTransactions() {
        $sql = "SELECT t.id as id_transaksi, t.tanggal, p.nama, p.harga, d.jumlah, (d.jumlah * p.harga) AS subtotal
                FROM transaksi t
                JOIN detail_transaksi d ON t.id = d.id_transaksi
                JOIN produk p ON d.id_produk = p.id
                ORDER BY t.id DESC";
        $result = $this->cn->query($sql);
        return $result;
    }

    public function getTransactionById($id) {
        $sql = "SELECT t.id as id_transaksi, t.tanggal, p.nama, p.harga, d.jumlah, (d.jumlah * p.harga) AS subtotal
                FROM transaksi t
                JOIN detail_transaksi d ON t.id = d.id_transaksi
                JOIN produk p ON d.id_produk = p.id
                WHERE t.id = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addTransaction($data) {
        try {
            // Validasi data produk
            $productDB = new Product();
            foreach ($data['products'] as $product) {
                $id_produk = intval($product['id_produk']);
                $jumlah = intval($product['jumlah']);
                
                $product_check = $productDB->getProductById($id_produk);
                if (!$product_check) {
                    throw new Exception("Produk ID $id_produk tidak ditemukan");
                }
                if ($product_check['stok'] < $jumlah) {
                    throw new Exception("Stok tidak cukup untuk produk ID $id_produk");
                }
            }
            
            // begin_transaction malah nge-hang entah kenapa
            // $this->cn->begin_transaction();
            
            $tanggal = $data['tanggal'];
            $id_produk = intval($data['products'][0]['id_produk']);
            
            // Insert transaksi
            $sql = "INSERT INTO transaksi (id_produk, tanggal) VALUES (?, ?)";
            $stmt = $this->cn->prepare($sql);
            $stmt->bind_param("is", $id_produk, $tanggal);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $transactionId = $this->cn->insert_id;
            if ($transactionId == 0) {
                throw new Exception("Failed to get transaction ID");
            }
            
            // Insert detail transaksi
            foreach ($data['products'] as $product) {
                $id_produk = intval($product['id_produk']);
                $jumlah = intval($product['jumlah']);
                
                $sql = "INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah) VALUES (?, ?, ?)";
                $stmt = $this->cn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Prepare detail failed: " . $this->cn->error);
                }
                
                $stmt->bind_param("iii", $transactionId, $id_produk, $jumlah);
                if (!$stmt->execute()) {
                    throw new Exception("Execute detail failed: " . $stmt->error);
                }
                
                // Update stok
                if (!$productDB->updateProductStock($id_produk, ['jumlah' => $jumlah])) {
                    throw new Exception("Failed to update stock for product ID $id_produk");
                }
            }

            // $this->cn->commit();
            return true;
        } catch (Exception $e) {
            // $this->cn->rollback();
            echo "Transaction error: " . $e->getMessage();
            return false;
        }
    }
}
?>
