<?php
include 'config.php';
include 'includes/header.php';

// Ambil data kelas dari database
$stmt = $conn->query("SELECT * FROM kelas");
$kelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses input absensi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_ids = $_POST['siswa_ids'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];

    foreach ($siswa_ids as $siswa_id) {
        // Cek apakah siswa sudah diinput pada tanggal yang sama
        $checkSql = "SELECT COUNT(*) FROM absensi WHERE siswa_id = :siswa_id AND tanggal = :tanggal";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute(['siswa_id' => $siswa_id, 'tanggal' => $tanggal]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            echo '<div class="alert alert-warning" role="alert">Data untuk siswa dengan ID ' . $siswa_id . ' sudah diinput pada tanggal ini.</div>';
        } else {
            $sql = "INSERT INTO absensi (siswa_id, tanggal, keterangan) VALUES (:siswa_id, :tanggal, :keterangan)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['siswa_id' => $siswa_id, 'tanggal' => $tanggal, 'keterangan' => $keterangan]);
        }
    }
    echo '<div class="alert alert-success" role="alert">Absensi berhasil disimpan!</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi Siswa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container form-container">
    <h1 class="text-center mb-4">Input Absensi Siswa</h1>
    <form method="post" action="input_absensi.php">
        <div class="mb-3">
            <label for="kelas" class="form-label">Pilih Kelas:</label>
            <select name="kelas_id" id="kelas" class="form-select" required>
                <option value="">Pilih Kelas</option>
                <?php foreach ($kelas as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan:</label>
        <select name="keterangan" id="keterangan" class="form-select" required>
            <option value="" selected disabled>Pilih keterangan</option>
            <option value="alpha">Alpha</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="nihil">Nihil</option>
        </select>
    </div>


        <div class="mb-3">
            <label for="siswa" class="form-label">Nama Siswa:</label>
            <div id="siswa">
                <!-- Nama siswa akan dimuat sebagai checkbox berdasarkan kelas yang dipilih -->
            </div>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
    $('#kelas').change(function() {
        var kelas_id = $(this).val();
        console.log('Kelas ID:', kelas_id);

        if (kelas_id !== "") {
            $.post('get_siswa.php', { kelas_id: kelas_id }, function(data) {
                console.log('Data diterima:', data);
                $('#siswa').html(data);
            }).fail(function(xhr, status, error) {
                console.error('Error:', error);
            });
        } else {
            $('#siswa').html('<option value="">Pilih Siswa</option>');
        }
    });

    $('form').submit(function(e) {
        var keterangan = $('#keterangan').val();
        if (!keterangan) {
            alert('Keterangan wajib diisi!');
            e.preventDefault();
        }
    });
});

</script>

</body>
</html>
