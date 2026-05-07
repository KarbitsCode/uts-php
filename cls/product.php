<?php
require_once "cfg/dbs.php";

class Product extends Database {
    public function getAllProducts() {
        $sql = "SELECT * FROM produk";
        $result = $this->cn->query($sql);
        return $result;
    }
    
    public function getProductById($id) {
        $sql = "SELECT * FROM produk WHERE id = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addProduct($data) {
        $name = $data['nama'];
        $price = $data['harga'];
        $kind = $data['jenis'];
        $stock = $data['stok'];
        $sql = "INSERT INTO produk (nama, harga, jenis, stok) VALUES (?, ?, ?, ?)";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("sdsi", $name, $price, $kind, $stock);
        return $stmt->execute();
    }

    public function updateProductStock($id, $data) {
        $qty = $data['jumlah'];
        if (!is_numeric($qty) || $qty <= 0) {
            return false; // CPMK093
        }
        $sql = "UPDATE produk SET stok = stok - ? WHERE id = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("ii", $qty, $id);
        return $stmt->execute();
    }

    public function updateProduct($id, $data) {
        $name = $data['nama'];
        $price = $data['harga'];
        $kind = $data['jenis'];
        $sql = "UPDATE produk SET nama = ?, harga = ?, jenis = ? WHERE id = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("sddi", $name, $price, $kind, $id);
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $sql = "DELETE FROM produk WHERE id = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
