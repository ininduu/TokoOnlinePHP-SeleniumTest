describe("E2E Toko Online System Test", () => {
  it("Berhasil menampilkan produk dan keranjang di halaman utama", () => {
    // Kunjungi server lokal (Pastikan 'php -S localhost:8000' sedang berjalan)
    cy.visit("http://localhost:8000");

    // Validasi antarmuka memuat dengan benar
    cy.contains("Toko Online").should("be.visible");
    cy.get('input[name="cari"]').should("be.visible");

    // Simulasi pengguna mencari barang
    cy.get('input[name="cari"]').type("Kemeja{enter}");
    cy.contains("Kemeja Flanel").should("be.visible");
  });
});
