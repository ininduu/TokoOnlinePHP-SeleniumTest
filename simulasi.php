<?php
// Autoloader sederhana
require_once 'src/Catalog.php';
require_once 'src/Checkout.php';

use App\Catalog;
use App\Checkout;

$fileProduk = __DIR__ . '/data/products.json';
$filePesanan = __DIR__ . '/data/orders.json';

echo "=== SIMULASI TOKO ONLINE ===\n\n";

try {
    // 1. Tampilkan Katalog
    $katalog = new Catalog($fileProduk);
    echo "Daftar Produk Tersedia:\n";
    print_r($katalog->getAllProducts());

    // 2. Simulasi Checkout (Budi membeli 2 Kemeja dan 1 Jeans)
    echo "\nMemproses Checkout...\n";
    $checkoutManager = new Checkout($fileProduk, $filePesanan);

    // Struktur keranjang: Kode Produk => Qty
    $keranjangBudi = [
        'PRD-001' => 2, // 2 x 150.000 = 300.000
        'PRD-002' => 1  // 1 x 250.000 = 250.000
        // Total = 550.000 (Seharusnya: Gratis Ongkir, Tanpa Diskon)
    ];

    // Struktur alamat
    $alamat = "Jl. Merdeka No. 10, Madiun";

    // Urutan Parameter
    $nota = $checkoutManager->prosesCheckout(
        'budi@email.com',
        $alamat,
        $keranjangBudi
    );

    echo "Checkout Berhasil!\n";
    print_r($nota);
} catch (Exception $e) {
    echo "GAGAL: " . $e->getMessage() . "\n";
}
