$(document).ready(function () {
  $('#formPembayaran').submit(function (e) {
    e.preventDefault(); // Mencegah form reload

    // Ambil data dari form
    var nama = $('input[name="nama"]').val();
    var nominal = $('input[name="nominal"]').val();
    var bank = $('select[name="bank"]').val();

    // Persiapkan data untuk dikirim
    var formData = {
      nama: nama,
      nominal: nominal,
      metode: "virtual-account",
      bank: bank
    };

    // Kirim AJAX
    $.ajax({
      url: 'proses_pembayaran.php',
      type: 'POST',
      data: formData,
      success: function (response) {
        alert("Pembayaran berhasil: " + response);
        // window.location.href = 'halaman_sukses.html';
      },
      error: function (xhr, status, error) {
        alert("Terjadi kesalahan: " + error);
      }
    });
  });
});
