// Saat halaman dimuat, simpan halaman sebelumnya
if (document.referrer) {
    sessionStorage.setItem("previousPage", document.referrer);
  }
  
  // Handle tombol "Keluar" (kembali ke halaman sebelumnya)
  document.querySelector(".btn-logout").addEventListener("click", function () {
    const previousPage = sessionStorage.getItem("previousPage");
  
    if (previousPage &&
      (previousPage.includes("homepage.html") ||
       previousPage.includes("explorekmr.html") ||
       previousPage.includes("kamaranda.html"))) {
      window.location.href = previousPage;
    } else {
      // Kalau tidak ada referrer, fallback ke homepage
      window.location.href = "homepage.html";
    }
  });
  
  // Handle tombol "Keluar Akun" (logout ke halaman index)
  document.querySelector(".btn-backacount").addEventListener("click", function () {
    window.location.href = "index.html";
  });
  