<?php
include 'config.php';

if (isset($_POST['kelas_id'])) {
    $kelas_id = $_POST['kelas_id'];

    $stmt = $conn->prepare("SELECT id, nama FROM siswa WHERE kelas_id = :kelas_id");
    $stmt->execute(['kelas_id' => $kelas_id]);
    $siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($siswa) {
        foreach ($siswa as $s) {
            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="checkbox" name="siswa_ids[]" value="' . $s['id'] . '" id="siswa_' . $s['id'] . '">';
            echo '<label class="form-check-label" for="siswa_' . $s['id'] . '">' . $s['nama'] . '</label>';
            echo '</div>';
        }
    } else {
        echo '<div class="form-check">Tidak ada siswa di kelas ini</div>';
    }
} else {
    echo '<div class="form-check">Kelas tidak valid</div>';
}
?>
