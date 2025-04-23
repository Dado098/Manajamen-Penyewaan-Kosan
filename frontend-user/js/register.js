$(document).ready(function () {
    $('#btnRegister').click(function () {
      const email = $('#email').val();
      const nama = $('#nama').val();
      const hp = $('#hp').val();
      const password = $('#password').val();
  
      $.ajax({
        url: 'http://localhost:8000/api/register', // Ganti sesuai endpoint Laravel kamu
        method: 'POST',
        data: {
          email: email,
          nama: nama,
          hp: hp,
          password: password,
        },
        success: function (res) {
          alert('Registrasi berhasil!');
          console.log(res);
        },
        error: function (err) {
          alert('Terjadi kesalahan saat mendaftar.');
          console.log(err);
        },
      });
    });
  });
  