<?php

namespace App;

use Exception;

class AdminManager
{
    private $filePesanan;

    public function __construct($filePesanan)
    {
        $this->filePesanan = $filePesanan;
    }

    // Mendapatkan semua data pesanan
    public function getAllOrders()
    {
        if (!file_exists($this->filePesanan)) return [];
        $data = file_get_contents($this->filePesanan);
        return json_decode($data, true) ?? [];
    }

    // SKPL-F05.2: Update Status Pesanan
    public function updateStatusPesanan($idPesanan, $statusBaru)
    {
        $orders = $this->getAllOrders();
        $pesananDitemukan = false;

        // Validasi status yang diizinkan (Cegah input sembarangan)
        $statusValid = ['Menunggu Pembayaran', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
        if (!in_array($statusBaru, $statusValid)) {
            throw new Exception("Status '$statusBaru' tidak valid.");
        }

        // Cari pesanan dan update statusnya
        foreach ($orders as $index => $order) {
            if ($order['id_pesanan'] === $idPesanan) {
                $orders[$index]['status'] = $statusBaru;
                $pesananDitemukan = true;
                break;
            }
        }

        if (!$pesananDitemukan) {
            throw new Exception("Pesanan dengan ID $idPesanan tidak ditemukan.");
        }

        // Simpan kembali ke file JSON
        file_put_contents($this->filePesanan, json_encode($orders, JSON_PRETTY_PRINT));
        return true;
    }
}
