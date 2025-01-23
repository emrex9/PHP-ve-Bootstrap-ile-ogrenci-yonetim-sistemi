<?php
// Veritabanı bağlantısı
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'ogrenci_sistemi';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Sayfada gösterilecek öğrenci sayısı
$ogrenci_sayisi_sayfa = 10;

// Mevcut sayfa numarasını
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$sayfa = max(1, $sayfa); // Sayfa numarası min. 1 olmalı

// Toplam öğrenci sayısını
$ogrenci_sayisi = $conn->query("SELECT COUNT(*) as toplam FROM ogrenciler")->fetch_assoc()['toplam'];
$toplam_sayfa = ceil($ogrenci_sayisi / $ogrenci_sayisi_sayfa);

$sayfa = min($sayfa, $toplam_sayfa);

$baslangic = ($sayfa - 1) * $ogrenci_sayisi_sayfa;

// Default sıralama
$siralama_kriteri = 'yas ASC';

// Sıralama kriterleri
if (isset($_GET['siralama'])) {
    $siralama_kriteri = match ($_GET['siralama']) {
        'yas_asc' => 'yas ASC',
        'yas_desc' => 'yas DESC',
        'eklenme_tarihi' => 'eklenme_tarihi DESC',
        'eklenme_tarihi_asc' => 'eklenme_tarihi ASC',
        'ad_asc' => 'ad ASC',
        'ad_desc' => 'ad DESC',
        'soyad_asc' => 'soyad ASC',
        'soyad_desc' => 'soyad DESC',
        'id_asc' => 'id ASC',
        'id_desc' => 'id DESC',
        default => 'yas ASC',
    };
}

// Kritere göre listeyi güncelleme
$ogrenciler = $conn->query("SELECT * FROM ogrenciler ORDER BY $siralama_kriteri LIMIT $baslangic, $ogrenci_sayisi_sayfa");

// Öğrenci ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $id = $_POST['id'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $yas = $_POST['yas'];

    // ID girilmemişse, otomatik olarak ata
    if (empty($id)) {
        $id_result = $conn->query("SELECT MAX(id) as max_id FROM ogrenciler");
        $max_id = $id_result->fetch_assoc()['max_id'];
        $id = $max_id + 1;
    }

    if ($yas >= 1 && $yas <= 99) {
        $conn->query("INSERT INTO ogrenciler (id, ad, soyad, yas, eklenme_tarihi) VALUES ('$id', '$ad', '$soyad', '$yas', NOW())");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Yaş 1 ile 99 arasında olmalıdır!');</script>";
    }
}

// Öğrenci güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $new_id = $_POST['new_id'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $yas = $_POST['yas'];

    if ($yas >= 1 && $yas <= 99) {
        $conn->query("UPDATE ogrenciler SET id='$new_id', ad='$ad', soyad='$soyad', yas='$yas' WHERE id='$id'");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<script>alert('Yaş 1 ile 99 arasında olmalıdır!');</script>";
    }
}

// Öğrenci silme
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM ogrenciler WHERE id='$delete_id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Öğrenci seçiö kontrol
$selected_student = null;
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $selected_student = $conn->query("SELECT * FROM ogrenciler WHERE id=$student_id")->fetch_assoc();
}
?>





<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Öğrenci Yönetim Sistemi</title>
    <style>
        .total-student-badge {
            background-color: transparent;
            font-weight: bold;
            color: black;
        }
        .dropdown-menu-hover .dropdown-item:hover .submenu {
            display: block;
        }
        .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            margin-top: -1px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .dropdown-item {
            color: #212529;
        }
        .dropdown-item:hover {
            background-color: #e9ecef;
        }
        .submenu .dropdown-item {
            color: #212529;
        }
        .submenu .dropdown-item:hover {
            background-color: #e9ecef;
        }
        .dropdown-menu-hover .dropdown-item {
            position: relative;
        }
        .dropdown-item:focus,
        .dropdown-item:active {
            color: #212529;
            background-color: #e9ecef;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-start">Öğrenci Yönetim Sistemi</h2>
        <span class="badge total-student-badge fs-5">Toplam Öğrenci: <?= $ogrenci_sayisi ?></span>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">Öğrenci Ekle</button>
    </div>

    <div class="dropdown mb-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            Sıralama Kriteri
        </button>
        <ul class="dropdown-menu dropdown-menu-hover" aria-labelledby="dropdownMenuButton">
            <li class="dropdown-item">
                <a>ID</a>
                <ul class="dropdown-menu submenu">
                    <li><a class="dropdown-item" href="?siralama=id_asc">Artan</a></li>
                    <li><a class="dropdown-item" href="?siralama=id_desc">Azalan</a></li>
                </ul>
            </li>
            <li class="dropdown-item">
                <a>Ad</a>
                <ul class="dropdown-menu submenu">
                    <li><a class="dropdown-item" href="?siralama=ad_asc">Artan</a></li>
                    <li><a class="dropdown-item" href="?siralama=ad_desc">Azalan</a></li>
                </ul>
            </li>
            <li class="dropdown-item">
                <a>Soyad</a>
                <ul class="dropdown-menu submenu">
                    <li><a class="dropdown-item" href="?siralama=soyad_asc">Artan</a></li>
                    <li><a class="dropdown-item" href="?siralama=soyad_desc">Azalan</a></li>
                </ul>
            </li>
            <li class="dropdown-item">
                <a>Yaş</a>
                <ul class="dropdown-menu submenu">
                    <li><a class="dropdown-item" href="?siralama=yas_asc">Artan</a></li>
                    <li><a class="dropdown-item" href="?siralama=yas_desc">Azalan</a></li>
                </ul>
            </li>
            <li class="dropdown-item">
                <a>Eklenme Tarihi</a>
                <ul class="dropdown-menu submenu">
                    <li><a class="dropdown-item" href="?siralama=eklenme_tarihi_asc">Artan</a></li>
                    <li><a class="dropdown-item" href="?siralama=eklenme_tarihi_desc">Azalan</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Yaş</th>
                <th>Eklenme Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ogrenci = $ogrenciler->fetch_assoc()): ?>
                <tr>
                    <td><?= $ogrenci['id'] ?></td>
                    <td><?= $ogrenci['ad'] ?></td>
                    <td><?= $ogrenci['soyad'] ?></td>
                    <td><?= $ogrenci['yas'] ?></td>
                    <td><?= $ogrenci['eklenme_tarihi'] ?></td>
                    <td>
                        <a href="?delete_id=<?= $ogrenci['id'] ?>" class="btn btn-danger btn-sm">Sil</a>
                        <button type="button" class="btn btn-primary btn-sm select-button" data-id="<?= $ogrenci['id'] ?>" data-ad="<?= $ogrenci['ad'] ?>" data-soyad="<?= $ogrenci['soyad'] ?>" data-yas="<?= $ogrenci['yas'] ?>">Seç</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $toplam_sayfa; $i++): ?>
                <li class="page-item <?= $i == $sayfa ? 'active' : '' ?>">
                    <a class="page-link" href="?sayfa=<?= $i ?><?= isset($_GET['siralama']) ? '&siralama=' . $_GET['siralama'] : '' ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Öğrenci ekleme kartı -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Öğrenci Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="number" class="form-control" id="id" name="id" placeholder="Otomatik atansın istiyorsanız boş bırakın">
                    </div>
                    <div class="mb-3">
                        <label for="ad" class="form-label">Ad</label>
                        <input type="text" class="form-control" id="ad" name="ad" required>
                    </div>
                    <div class="mb-3">
                        <label for="soyad" class="form-label">Soyad</label>
                        <input type="text" class="form-control" id="soyad" name="soyad" required>
                    </div>
                    <div class="mb-3">
                        <label for="yas" class="form-label">Yaş</label>
                        <input type="number" class="form-control" id="yas" name="yas" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add">Ekle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Seçilen öğrenciyi düzenleme kartı -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStudentModalLabel">Öğrenci Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" id="update_id" name="id">
                    <div class="mb-3">
                        <label for="update_new_id" class="form-label">Yeni ID</label>
                        <input type="number" class="form-control" id="update_new_id" name="new_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_ad" class="form-label">Ad</label>
                        <input type="text" class="form-control" id="update_ad" name="ad" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_soyad" class="form-label">Soyad</label>
                        <input type="text" class="form-control" id="update_soyad" name="soyad" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_yas" class="form-label">Yaş</label>
                        <input type="number" class="form-control" id="update_yas" name="yas" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.select-button').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const ad = button.getAttribute('data-ad');
            const soyad = button.getAttribute('data-soyad');
            const yas = button.getAttribute('data-yas');

            document.getElementById('update_id').value = id;
            document.getElementById('update_new_id').value = id;
            document.getElementById('update_ad').value = ad;
            document.getElementById('update_soyad').value = soyad;
            document.getElementById('update_yas').value = yas;

            new bootstrap.Modal(document.getElementById('editStudentModal')).show();
        });
    });
</script>
</body>
</html>