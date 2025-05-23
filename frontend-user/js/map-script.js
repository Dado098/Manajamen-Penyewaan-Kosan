// Inisialisasi peta
const map = L.map('map').setView([0, 0], 2);

// Tambahkan tile layer OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Buat ikon marker kost berwarna merah
const redIcon = L.icon({
  iconUrl: '../images/markerkos.png',
  iconSize: [30, 51],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
});

// Tambahkan search box dengan pembatasan Indonesia
// L.Control.geocoder({
//   defaultMarkGeocode: false,
//   geocodingQueryParams: {
//     countrycodes: 'ID'
//   }
// })
// .on('markgeocode', function(e) {
//   const center = e.geocode.center;

//   map.setView(center, 16);

//   if (map.searchMarker) {
//     map.removeLayer(map.searchMarker);
//   }

//   map.searchMarker = L.marker(center).addTo(map)
//     .bindPopup(`
//       <b>Hasil Pencarian:</b><br>
//       ${e.geocode.name}<br>
//       Latitude: ${center.lat.toFixed(5)}<br>
//       Longitude: ${center.lng.toFixed(5)}
//     `).openPopup();

//   document.getElementById('latInput').value = center.lat;
//   document.getElementById('lngInput').value = center.lng;
// }).addTo(map);

// Variabel global
let kostCenter = null;
let routingControl = null;
let isRoutingShown = false;  // status toggle rute, awalnya false (rute belum tampil)


// Ambil data lokasi kost dari API Laravel
fetch('http://127.0.0.1:8000/api/locations')
  .then(response => response.json())
  .then(locations => {
    if (locations.length === 0) return;

    kostCenter = [locations[0].latitude, locations[0].longitude];
    map.setView(kostCenter, 16);

    // Ambil data kamar
    $.ajax({
      url: 'http://localhost:8000/api/kamars',
      method: 'GET',
      success: function (res) {
        const totalKamar = res.length;
        const kamarKosong = res.filter(k => k.status === 'Kosong').length;

        // Tambahkan marker lokasi
        locations.forEach(loc => {
          const currentLoc = [loc.latitude, loc.longitude];
          const distanceKm = (map.distance(kostCenter, currentLoc) / 1000).toFixed(2);

          let popupContent = `
            <b>${loc.name}</b><br>
            ${loc.address}<br>
            <b>Jarak ke kost:</b> ${distanceKm} km<br>
          `;

          if (loc.id === 1) {
            popupContent += `
              <b>Total Kamar:</b> ${totalKamar}<br>
              <b>Kamar Kosong:</b> ${kamarKosong}
            `;
          }

          L.marker(currentLoc, loc.id === 1 ? { icon: redIcon } : {})
            .addTo(map)
            .bindPopup(popupContent);
        });
      },
      error: err => console.error("Gagal memuat data kamar:", err)
    });
  })
  .catch(error => console.error('Error fetching locations:', error));

// Menampilkan posisi user dan tambahkan marker
navigator.geolocation.getCurrentPosition(
  position => {
    const userLatLng = [position.coords.latitude, position.coords.longitude];

    L.marker(userLatLng).addTo(map)
      .bindPopup("Posisi Anda Sekarang")
      .openPopup();

    map.setView(userLatLng, 14);
  },
  error => {
    console.error("Gagal mendapatkan lokasi pengguna:", error);
  }
);

// Event klik peta – tampilkan marker dan jarak ke kostCenter
map.on('click', function (e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;
  
    // Hitung jarak ke kostCenter
    const distance = map.distance([lat, lng], kostCenter);
    const distanceKm = (distance / 1000).toFixed(2);
  
    // Cek apakah ada marker sebelumnya
    if (map.selectedMarker) {
      const prevLatLng = map.selectedMarker.getLatLng();
      const isSameLocation = prevLatLng.lat.toFixed(5) === lat.toFixed(5) && prevLatLng.lng.toFixed(5) === lng.toFixed(5);
  
      if (isSameLocation) {
        // Jika klik lokasi sama, hapus marker dan rute, lalu stop
        map.removeLayer(map.selectedMarker);
        map.selectedMarker = null;
  
        if (routingControl) {
          map.removeControl(routingControl);
          routingControl = null;
        }
        return;  // Jangan lanjut buat marker baru
      } else {
        // Jika lokasi berbeda, hapus marker lama dulu
        map.removeLayer(map.selectedMarker);
        map.selectedMarker = null;
  
        if (routingControl) {
          map.removeControl(routingControl);
          routingControl = null;
        }
      }
    }
  
    // Tambahkan marker lokasi yang dipilih
    map.selectedMarker = L.marker([lat, lng]).addTo(map)
      .bindPopup(`
        <b>Lokasi yang Anda pilih:</b><br>
        Latitude: ${lat.toFixed(5)}<br>
        Longitude: ${lng.toFixed(5)}<br>
        <b>Jarak ke kos pusat:</b> ${distanceKm} km
      `).openPopup();
  
    // Reverse geocoding untuk mendapatkan alamat
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
      .then(res => res.json())
      .then(data => {
        const address = data.display_name || "Alamat tidak ditemukan";
        map.selectedMarker.setPopupContent(`
          <b>Alamat:</b> ${address}<br>
          Latitude: ${lat.toFixed(5)}<br>
          Longitude: ${lng.toFixed(5)}<br>
          <b>Jarak ke kos pusat:</b> ${distanceKm} km
        `).openPopup();
      });
  
    // Simpan ke form (jika ada)
    document.getElementById('latInput').value = lat;
    document.getElementById('lngInput').value = lng;
  
    // Tampilkan rute dari titik yang diklik ke kostCenter
    routingControl = L.Routing.control({
      waypoints: [
        L.latLng(lat, lng),
        L.latLng(kostCenter)
      ],
      routeWhileDragging: false,
      addWaypoints: true,
      show: true,
      createMarker: function(i, waypoint, n) {
        if (i === 1) {
          // Hilangkan marker tujuan
          return null;
        }
        // Marker untuk lokasi awal
        return L.marker(waypoint.latLng);
      }
    }).addTo(map);
  });
  
  

// Tombol untuk menampilkan rute dari posisi user ke kost
document.getElementById('btnRute').addEventListener('click', function () {
    if (!kostCenter) {
      alert("Lokasi kost belum dimuat.");
      return;
    }
  
    if (isRoutingShown) {
      // Jika rute sedang ditampilkan, hapus rute dan ubah status toggle
      if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null;
      }
      isRoutingShown = false;
      this.textContent = "Tampilkan Rute"; // Optional: ubah teks tombol
    } else {
      // Jika rute belum ditampilkan, tampilkan rute
  
      // Cek apakah ada lokasi klik di peta
      let startLatLng = null;
      if (map.selectedMarker) {
        startLatLng = map.selectedMarker.getLatLng();
        showRoute(startLatLng);
      } else {
        // fallback: gunakan geolocation
        if (!navigator.geolocation) {
          alert("Geolocation tidak didukung di browser ini.");
          return;
        }
        navigator.geolocation.getCurrentPosition(
          position => {
            startLatLng = L.latLng(position.coords.latitude, position.coords.longitude);
            showRoute(startLatLng);
          },
          error => {
            console.error("Gagal mendapatkan lokasi pengguna:", error);
            alert("Gagal mendapatkan lokasi Anda.");
          }
        );
      }
    }
  
    function showRoute(start) {
      if (routingControl) {
        map.removeControl(routingControl);
      }
  
      routingControl = L.Routing.control({
        waypoints: [
          start,
          L.latLng(kostCenter)
        ],
        routeWhileDragging: false,
        show: false,
        addWaypoints: false
      }).addTo(map);
  
      isRoutingShown = true;
      document.getElementById('btnRute').textContent = "Sembunyikan Rute"; // Optional update teks tombol
    }
  });
  
