$(document).ready(function () {
    const kamarData = {
      "1": [
        { no: 1, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 2, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 3, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 4, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 5, img: "../images/Kamar.png", harga: "Rp600.000/bulan" }
      ],
      "2": [
        { no: 6, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 7, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 8, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 9, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
        { no: 10, img: "../images/Kamar.png", harga: "Rp600.000/bulan" },
      ]
    };
  
    function renderKamar(lantai) {
      const container = $("#card-container");
      container.empty();
      kamarData[lantai].forEach(kamar => {
        container.append(`
          <div class="card">
            <img src="${kamar.img}" alt="Kamar No ${kamar.no}">
            <h4>Kamar No ${kamar.no}</h4>
            <p> ${kamar.harga}</p>
            <button>Cek Detail Kamar</button>
          </div>
        `);
      });
    }
  
    renderKamar(1);
  
    $(".tablinks").click(function () {
      $(".tablinks").removeClass("active");
      $(this).addClass("active");
      const lantai = $(this).data("lantai");
      renderKamar(lantai);
    });
  });
  // Event delegation untuk tombol cek detail
$(document).on("click", "button:contains('Cek Detail Kamar')", function () {
  const kamarNo = $(this).siblings("h4").text().replace("Kamar No ", "");
  window.location.href = `detailkamar${kamarNo}.html`;
});

  