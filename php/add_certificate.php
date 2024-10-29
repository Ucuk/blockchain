<?php
require_once 'config.php';

// Fungsi untuk membersihkan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Inisialisasi variabel pesan
$message = '';

// Cek apakah form telah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty($_POST["certificate_name"]) || !isset($_FILES["certificate_file"])) {
        $message = "Semua field harus diisi!";
    } else {
        $certificate_name = clean_input($_POST["certificate_name"]);
        $certificate_file = $_FILES["certificate_file"];

        // Validasi file
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($certificate_file['type'], $allowed_types)) {
            $message = "Tipe file tidak diizinkan. Hanya PDF, JPEG, dan PNG yang diperbolehkan.";
        } elseif ($certificate_file['size'] > $max_size) {
            $message = "Ukuran file terlalu besar. Maksimum 5MB.";
        } else {
            // Hitung MD5 hash dari file
            $md5_hash = md5_file($certificate_file['tmp_name']);

            // Cek apakah hash sudah ada di database
            $stmt = $conn->prepare("SELECT * FROM valid_certificates WHERE md5_hash = ?");
            $stmt->bind_param("s", $md5_hash);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "Sertifikat dengan hash yang sama sudah ada di database.";
            } else {
                // Tambahkan sertifikat ke database
                $stmt = $conn->prepare("INSERT INTO valid_certificates (certificate_name, md5_hash) VALUES (?, ?)");
                $stmt->bind_param("ss", $certificate_name, $md5_hash);

                if ($stmt->execute()) {
                    $message = "Sertifikat berhasil ditambahkan ke database.";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sertifikat</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Sertifikat Baru</h2>
        <?php
        if (!empty($message)) {
            echo "<p class='message'>$message</p>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div>
                <label for="certificate_name">Nama Sertifikat:</label>
                <input type="text" id="certificate_name" name="certificate_name" required>
            </div>
            <div>
                <label for="certificate_file">File Sertifikat:</label>
                <input type="file" id="certificate_file" name="certificate_file" required>
            </div>
            <div>
                <button type="submit">Tambah Sertifikat</button>
            </div>
        </form>
        <p><a href="../index.html">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>