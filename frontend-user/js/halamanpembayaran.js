$('#pay-button').click(function (e) {
  e.preventDefault();

  const nama = $('input[name=nama]').val();
  const bank = $('#bank').val();

  $.ajax({
    url: '../snap/transaction.php',
    method: 'POST',
    data: {
      nama: nama,
      bank: bank,
      amount: 1500000
    },
    success: function (snapToken) {
      snap.pay(snapToken, {
        onSuccess: function (result) {
          alert("Pembayaran berhasil!");
          console.log(result);
        },
        onPending: function (result) {
          alert("Menunggu pembayaran!");
          console.log(result);
        },
        onError: function (result) {
          alert("Pembayaran gagal!");
          console.log(result);
        },
        onClose: function () {
          alert("Popup pembayaran ditutup.");
        }
      });
    }
  });
});
