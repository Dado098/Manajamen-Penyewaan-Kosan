$(document).ready(function () {
    $('#login-btn').click(function () {
      const email = $('#email').val().trim();
      const password = $('#password').val().trim();
  
      if (!email || !password) {
        $('#error-message').text('Email dan password harus diisi!').removeClass('hidden');
        return;
      }
  
      $.ajax({
        url: '../php/login.php', // Ganti sesuai lokasi file login.php kamu
        method: 'POST',
        data: {
          email: email,
          password: password
        },
        success: function (response) {
          if (response === 'success') {
            window.location.href = 'dashboard.html';
          } else {
            $('#error-message').text('Login gagal. Email atau password salah!').removeClass('hidden');
          }
        },
        error: function () {
          $('#error-message').text('Terjadi kesalahan. Silakan coba lagi.').removeClass('hidden');
        }
      });
    });
  });
  