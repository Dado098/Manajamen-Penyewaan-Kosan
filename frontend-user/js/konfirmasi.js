$(document).ready(function() {
    $('#btnKontakPengelola').click(function() {
      // Menggunakan nomor WhatsApp dengan format internasional
      var nomorWhatsApp = "https://wa.me/6285934733097"; // Ganti dengan nomor pengelola kostan
  
      // Membuka WhatsApp di tab baru (untuk desktop)
      window.open(nomorWhatsApp, '_blank');
      
      // Alternatif jika browser tidak mendukung pop-up (gunakan window.location.href)
      // window.location.href = nomorWhatsApp; // Mengarahkan langsung ke WhatsApp
    });
  });
  