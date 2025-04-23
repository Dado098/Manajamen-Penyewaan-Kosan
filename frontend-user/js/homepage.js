<script>
  $(document).ready(function () {
    $('#loginBtn').click(function () {
      window.location.href = 'login.html';
    });

    $('#signupBtn, .signupRedirect').click(function () {
      window.location.href = 'register.html';
    });

    // AJAX untuk Cek Detail Kamar
    $('.cekdtlkamarRedirect').click(function () {
      const targetPage = $(this).data('target');

      $('#ajax-container').fadeOut(200, function () {
        $.ajax({
          url: targetPage,
          method: 'GET',
          success: function (response) {
            $('#ajax-container').html(response).fadeIn(300);
            $('html, body').animate({
              scrollTop: $('#ajax-container').offset().top
            }, 500);
          },
          error: function () {
            $('#ajax-container').html('<p>Gagal memuat detail kamar.</p>').fadeIn(300);
          }
        });
      });
    });
  });
</script>
