document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Mencegah reload halaman

    var inputFile = document.getElementById('sertifikatInput').files[0]; // Mendapatkan file
    if (!inputFile) {
        alert("Tidak ada file yang dipilih!");
        return;
    }

    var fileReader = new FileReader(); 

    // Membaca file sebagai array buffer untuk hashing
    fileReader.onload = function(e) {
        var arrayBuffer = e.target.result; // Buffer untuk hash
        var spark = new SparkMD5.ArrayBuffer(); // Inisialisasi SparkMD5
        
        spark.append(arrayBuffer); // Tambahkan buffer untuk di-hash
        var hashHex = spark.end(); // Dapatkan hasil hash MD5

        document.getElementById('hashResult').textContent = hashHex; // Tampilkan hasil hash
    };

    // Jika file adalah gambar, tampilkan di halaman
    var imgElement = document.getElementById('sertifikatImg');
    if (inputFile.type.startsWith("image/")) {
        var imgReader = new FileReader();
        imgReader.onload = function(e) {
            imgElement.src = e.target.result;
        };
        imgReader.readAsDataURL(inputFile); // Baca gambar sebagai DataURL
    } else {
        imgElement.src = "sert2.jpg"; // Jika bukan gambar, tampilkan gambar default
    }

    fileReader.readAsArrayBuffer(inputFile); // Memulai pembacaan file untuk hashing
});

// Fungsi untuk menambahkan sertifikat
function addCertificate() {
    var certificateName = document.getElementById('certificateName').value;
    var certificateFile = document.getElementById('certificateFile').files[0];

    if (!certificateName || !certificateFile) {
        alert("Semua field harus diisi!");
        return;
    }

    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    var maxSize = 5 * 1024 * 1024; // 5MB

    if (!allowedTypes.includes(certificateFile.type)) {
        alert("Tipe file tidak diizinkan. Hanya PDF, JPEG, dan PNG yang diperbolehkan.");
        return;
    }

    if (certificateFile.size > maxSize) {
        alert("Ukuran file terlalu besar. Maksimum 5MB.");
        return;
    }

    // Lakukan AJAX untuk mengupload sertifikat ke server
    var formData = new FormData();
    formData.append('certificate_name', certificateName);
    formData.append('certificate_file', certificateFile);

    fetch('add_certificate.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Tampilkan pesan dari server
        document.getElementById('uploadForm').reset(); // Reset form setelah upload
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Terjadi kesalahan saat mengupload sertifikat.");
    });
}

// Event listener untuk tombol tambah sertifikat
document.getElementById('addCertificateBtn').addEventListener('click', addCertificate);