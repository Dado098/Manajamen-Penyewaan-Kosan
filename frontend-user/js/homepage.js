$(document).ready(function () {
    // Ketika tombol "Lihat Kamar" diklik
    $('.lihat-kamar').click(function () {
      $.ajax({
        url: 'http://localhost:3000/data', // Endpoint server
        method: 'GET', // Metode permintaan
        success: function (response) {
          // Menampilkan data yang diterima dari server di dalam #result
          alert(response.message); // Menampilkan pesan dalam bentuk alert
        },
        error: function (err) {
          // Menangani error jika permintaan gagal
          console.log('Error:', err);
        }
      });
    });
  });
  