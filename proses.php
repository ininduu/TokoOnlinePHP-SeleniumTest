<?php
session_start();
require_once 'src/Checkout.php';

use App\Checkout;

$fileProduk = __DIR__ . '/data/products.json';
$filePesanan = __DIR__ . '/data/orders.json';

// Inisialisasi status dan pesan
$statusSukses = false;
$pesan = "";
$nota = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $alamat = $_POST['alamat'] ?? ''; // Menangkap input alamat dari form
    $inputQty = $_POST['qty'] ?? [];

    // Filter array: Hanya ambil barang yang Qty-nya lebih dari 0
    $keranjang = array_filter($inputQty, function ($qty) {
        return (int)$qty > 0;
    });

    try {
        $checkoutManager = new Checkout($fileProduk, $filePesanan);

        // Memasukkan ke-3 parameter yang dibutuhkan: Email, Alamat, dan Keranjang
        $nota = $checkoutManager->prosesCheckout($email, $alamat, $keranjang);

        $statusSukses = true;
        $pesan = "Checkout berhasil diproses!";
    } catch (Exception $e) {
        $statusSukses = false;
        $pesan = $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if ($statusSukses): ?>
                    <div class="card border-success shadow">
                        <div class="card-header bg-success text-white text-center">
                            <h4 class="mb-0">Transaksi Sukses!</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-center text-muted mb-4">ID Pesanan: <strong><?= $nota['id_pesanan'] ?></strong></p>

                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Email Pelanggan
                                    <span><?= htmlspecialchars($nota['email']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start flex-column">
                                    <span class="mb-1">Alamat Pengiriman</span>
                                    <small class="text-muted"><?= nl2br(htmlspecialchars($nota['alamat'])) ?></small>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Total Item Dibeli
                                    <span><?= array_sum($nota['items']) ?> pcs</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status
                                    <span class="badge bg-warning text-dark"><?= $nota['status'] ?></span>
                                </li>
                            </ul>

                            <div class="p-3 bg-light rounded text-center">
                                <h5 class="mb-1">Total Pembayaran</h5>
                                <h2 class="text-success mb-0">Rp <?= number_format($nota['total_bayar'], 0, ',', '.') ?></h2>
                                <small class="text-muted">(Termasuk perhitungan ongkir & diskon jika ada)</small>
                            </div>
                        </div>
                        <div class="card-footer text-center bg-white">
                            <a href="index.php" class="btn btn-outline-primary">Kembali ke Katalog</a>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="card border-danger shadow">
                        <div class="card-header bg-danger text-white text-center">
                            <h4 class="mb-0">Transaksi Gagal</h4>
                        </div>
                        <div class="card-body text-center py-5">
                            <h5 class="text-danger mb-3">Terjadi Kesalahan:</h5>
                            <p class="fs-5 bg-light p-3 border rounded text-danger">
                                "<?= htmlspecialchars($pesan) ?>"
                            </p>
                        </div>
                        <div class="card-footer text-center bg-white">
                            <a href="index.php" class="btn btn-outline-secondary">Coba Lagi</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>