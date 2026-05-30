<?php

namespace App;

class Catalog
{
    private $fileProduk;

    public function __construct($fileProduk)
    {
        $this->fileProduk = $fileProduk;
    }

    public function getAllProducts()
    {
        if (!file_exists($this->fileProduk)) return [];
        return json_decode(file_get_contents($this->fileProduk), true) ?? [];
    }

    // SKPL-F02.3: Pencarian Produk
    public function searchProduct($keyword)
    {
        $allProducts = $this->getAllProducts();
        if (empty($keyword)) return $allProducts;

        $results = [];
        foreach ($allProducts as $kode => $item) {
            if (stripos($item['nama'], $keyword) !== false) {
                $results[$kode] = $item;
            }
        }
        return $results;
    }

    // SKPL-F05.1: Create & Update Produk
    public function saveProduct($kode, $nama, $harga, $stok)
    {
        $products = $this->getAllProducts();
        $products[$kode] = [
            'nama' => htmlspecialchars($nama),
            'harga' => (float)$harga,
            'stok' => (int)$stok
        ];
        file_put_contents($this->fileProduk, json_encode($products, JSON_PRETTY_PRINT));
        return true;
    }

    // SKPL-F05.1: Delete Produk
    public function deleteProduct($kode)
    {
        $products = $this->getAllProducts();
        if (isset($products[$kode])) {
            unset($products[$kode]);
            file_put_contents($this->fileProduk, json_encode($products, JSON_PRETTY_PRINT));
        }
        return true;
    }
}
