// Simulasi data awal jika localStorage kosong
const defaultUser = {
    email: "user@example.com",
    nama: "Contoh Nama",
    hp: "08123456789",
    password: "password123"
  };
  
  // Cek apakah ada data tersimpan
  let userData = JSON.parse(localStorage.getItem("userData"));
  if (!userData) {
    userData = defaultUser;
    localStorage.setItem("userData", JSON.stringify(userData));
  }
  
  // Isi data ke form saat halaman dibuka
  document.getElementById("email").value = userData.email;
  document.getElementById("nama").value = userData.nama;
  document.getElementById("hp").value = userData.hp;
  document.getElementById("password").value = userData.password;
  
  // Event ketika tombol Edit diklik
  document.getElementById("edit").addEventListener("click", function () {
    const updatedData = {
      email: document.getElementById("email").value,
      nama: document.getElementById("nama").value,
      hp: document.getElementById("hp").value,
      password: document.getElementById("password").value
    };
  
    // Simpan ke localStorage
    localStorage.setItem("userData", JSON.stringify(updatedData));
    alert("Data berhasil diperbarui!");
  });
  