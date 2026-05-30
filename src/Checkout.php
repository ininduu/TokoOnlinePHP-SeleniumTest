<?php

namespace App;

use Exception;

class Checkout
{
    private $fileProduk;
    private $filePesanan;

    public function __construct($fileProduk, $filePesanan)
    {
        $this->fileProduk = $fileProduk;
        $this->filePesanan = $filePesanan;
    }

    public function prosesCheckout($emailPelanggan, $alamat, $keranjang)
    {
        // VALIDASI AWAL
        if (empty($keranjang)) {
            throw new Exception("Keranjang belanja kosong.");
        }

        if (empty($alamat)) {
            throw new Exception("Alamat pengiriman wajib diisi.");
        }

        // AMBIL DATA PRODUK
        $products = json_decode(file_get_contents($this->fileProduk), true);

        // VALIDASI FILE PRODUK
        if (!is_array($products)) {
            throw new Exception("Data produk rusak.");
        }

        $totalHargaBarang = 0;

        // PROSES KERANJANG
        foreach ($keranjang as $kodeProduk => $qty) {

            if ($qty <= 0) {
                throw new Exception("Kuantitas harus lebih dari 0.");
            }

            if (!isset($products[$kodeProduk])) {
                throw new Exception("Produk tidak valid.");
            }

            if ($products[$kodeProduk]['stok'] < $qty) {
                throw new Exception(
                    "Stok " . $products[$kodeProduk]['nama'] . " tidak mencukupi."
                );
            }

            $subtotal = $products[$kodeProduk]['harga'] * $qty;

            $totalHargaBarang += $subtotal;

            $products[$kodeProduk]['stok'] -= $qty;
        }

        // ONGKIR DAN DISKON
        $ongkosKirim = 20000;
        $diskon = 0;

        if ($totalHargaBarang > 500000) {

            $ongkosKirim = 0;

            if ($totalHargaBarang > 1000000) {
                $diskon = $totalHargaBarang * 0.10;
            }
        }

        $totalBayar = ($totalHargaBarang - $diskon) + $ongkosKirim;

        // DATA PESANAN
        $pesananBaru = [
            'id_pesanan' => uniqid('ORD-'),
            'email' => $emailPelanggan,
            'alamat' => htmlspecialchars($alamat),
            'items' => $keranjang,
            'total_bayar' => $totalBayar,
            'status' => 'Menunggu Pembayaran',
            'tanggal' => date('Y-m-d H:i:s')
        ];

        // UPDATE STOK
        file_put_contents(
            $this->fileProduk,
            json_encode($products, JSON_PRETTY_PRINT)
        );

        // AMBIL DATA PESANAN
        $orders = json_decode(
            file_get_contents($this->filePesanan),
            true
        );

        // JIKA FILE RUSAK / NULL
        if (!is_array($orders)) {
            $orders = [];
        }

        $orders[] = $pesananBaru;

        // SIMPAN PESANAN
        file_put_contents(
            $this->filePesanan,
            json_encode($orders, JSON_PRETTY_PRINT)
        );

        return $pesananBaru;
    }
}
