$(document).ready(function () {
    const kamarData = {
        "1": [
            { no: 1, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 2, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 3, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 4, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 5, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" }
        ],
        "2": [
            { no: 6, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 7, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 8, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 9, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" },
            { no: 10, img: "../images/kamar.jpg", harga: "Rp600.000/bulan" }
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
                    <!-- Tombol Cek Detail Kamar -->
                    <button class="cek-detail-btn" data-kamar="${kamar.no}">Cek Detail Kamar</button>
                </div>
            `);
        });
    }

    // Menambahkan event listener untuk tombol "Cek Detail Kamar"
    $(document).on("click", ".cek-detail-btn", function () {
        const kamarNo = $(this).data("kamar");
        console.log(`Mengarah ke detailkamar${kamarNo}.html`);
        window.location.href = `detailkamar${kamarNo}.html`;
    });

    renderKamar(1); // Menampilkan kamar lantai 1 secara default
});
