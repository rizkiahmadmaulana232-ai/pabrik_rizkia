<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
include '../config_rizkia/security_rizkia.php';
date_default_timezone_set('Asia/Jakarta');

if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] !== 'engineering'){
    die('Akses ditolak. Hanya Engineering.');
}

mysqli_query($conn_rizkia, "CREATE TABLE IF NOT EXISTS maintenance_plan (
  id_rizkia INT AUTO_INCREMENT PRIMARY KEY,
  mesin_id_rizkia INT NOT NULL,
  tanggal_pm_rizkia DATE NOT NULL,
  checklist_rizkia TEXT DEFAULT NULL,
  status_rizkia VARCHAR(50) DEFAULT 'Terjadwal'
)");

mysqli_query($conn_rizkia, "CREATE TABLE IF NOT EXISTS maintenance_log (
  id_rizkia INT AUTO_INCREMENT PRIMARY KEY,
  mesin_id_rizkia INT NOT NULL,
  mulai_rizkia DATETIME DEFAULT NULL,
  selesai_rizkia DATETIME DEFAULT NULL,
  durasi_menit_rizkia INT DEFAULT NULL,
  catatan_rizkia TEXT DEFAULT NULL
)");

$flash = '';

/* =========================
   UPDATE STATUS MESIN
========================= */
if(isset($_POST['update_status_rizkia']) && csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
    $id_mesin = (int)($_POST['id_mesin_rizkia'] ?? 0);
    $status = $_POST['status_rizkia'] ?? 'Normal';
    $allowed = ['Normal', 'Rusak', 'Perbaikan'];

    if($id_mesin > 0 && in_array($status, $allowed, true)){
        $stmt = mysqli_prepare($conn_rizkia, 'UPDATE mesin_rizkia SET status_rizkia=? WHERE id_rizkia=?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $id_mesin);
        mysqli_stmt_execute($stmt);
        $flash = 'Status mesin berhasil diperbarui.';
    }
}

/* =========================
   TAMBAH PM
========================= */
if(isset($_POST['buat_pm_rizkia']) && csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
    $mesin = (int)($_POST['mesin_id_rizkia'] ?? 0);
    $tanggal = $_POST['tanggal_pm_rizkia'] ?? '';
    $checklist = trim($_POST['checklist_rizkia'] ?? '');

    if($mesin > 0 && $tanggal !== ''){
        $stmt = mysqli_prepare($conn_rizkia, "INSERT INTO maintenance_plan (mesin_id_rizkia, tanggal_pm_rizkia, checklist_rizkia, status_rizkia) VALUES (?, ?, ?, 'Terjadwal')");
        mysqli_stmt_bind_param($stmt, 'iss', $mesin, $tanggal, $checklist);
        mysqli_stmt_execute($stmt);
        $flash = 'Jadwal preventive maintenance ditambahkan.';
    }
}

/* =========================
   EKSEKUSI PM
========================= */
if(isset($_POST['eksekusi_pm_rizkia']) && csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
    $pm_id = (int)($_POST['pm_id_rizkia'] ?? 0);
    $mulai = $_POST['mulai_rizkia'] ?? '';
    $selesai = $_POST['selesai_rizkia'] ?? '';
    $catatan = trim($_POST['catatan_rizkia'] ?? '');

    if($pm_id > 0 && $mulai !== '' && $selesai !== ''){
        $pm = mysqli_fetch_assoc(mysqli_query($conn_rizkia, "SELECT mesin_id_rizkia FROM maintenance_plan WHERE id_rizkia='{$pm_id}' LIMIT 1"));
        if($pm){
            $durasi = max(0, (int)round((strtotime($selesai) - strtotime($mulai)) / 60));
            $mesin_id = (int)$pm['mesin_id_rizkia'];

            $stmt_log = mysqli_prepare($conn_rizkia, 'INSERT INTO maintenance_log (mesin_id_rizkia, mulai_rizkia, selesai_rizkia, durasi_menit_rizkia, catatan_rizkia) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt_log, 'issis', $mesin_id, $mulai, $selesai, $durasi, $catatan);
            mysqli_stmt_execute($stmt_log);

            mysqli_query($conn_rizkia, "UPDATE maintenance_plan SET status_rizkia='Selesai' WHERE id_rizkia='{$pm_id}'");
            mysqli_query($conn_rizkia, "UPDATE mesin_rizkia SET status_rizkia='Normal' WHERE id_rizkia='{$mesin_id}'");

            $flash = 'Preventive maintenance selesai, log berhasil disimpan.';
        }
    }
}

/* =========================
   🔥 HAPUS PM (BARU)
========================= */
if(isset($_POST['hapus_pm_rizkia']) && csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
    $id_pm = (int)($_POST['id_pm_rizkia'] ?? 0);

    if($id_pm > 0){
        mysqli_query($conn_rizkia, "DELETE FROM maintenance_plan WHERE id_rizkia='{$id_pm}'");
        $flash = 'Jadwal PM berhasil dihapus.';
    }
}

/* =========================
   DATA
========================= */
$mesin_data = mysqli_query($conn_rizkia, 'SELECT * FROM mesin_rizkia ORDER BY id_rizkia ASC');
$mesin_dropdown = mysqli_query($conn_rizkia, 'SELECT id_rizkia, nama_mesin_rizkia FROM mesin_rizkia ORDER BY nama_mesin_rizkia ASC');

$pm_data = mysqli_query($conn_rizkia, "SELECT p.*, m.nama_mesin_rizkia 
FROM maintenance_plan p 
JOIN mesin_rizkia m ON p.mesin_id_rizkia=m.id_rizkia 
ORDER BY p.tanggal_pm_rizkia ASC, p.id_rizkia DESC");

$log_data = mysqli_query($conn_rizkia, "SELECT l.*, m.nama_mesin_rizkia 
FROM maintenance_log l 
JOIN mesin_rizkia m ON l.mesin_id_rizkia=m.id_rizkia 
ORDER BY l.id_rizkia DESC LIMIT 20");
?>

<!DOCTYPE html>
<html>
<head>
<title>Engineering - Mesin & Maintenance</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
body{background:#eef2f7;color:#2c3e50}
.header{height:80px;margin-left:220px;background:linear-gradient(135deg,#1f2d3a,#2c3e50);color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:bold}
.sidebar{width:220px;height:100vh;background:linear-gradient(180deg,#2c3e50,#34495e);position:fixed;top:0;left:0;display:flex;flex-direction:column}
.brand{padding:24px 20px;font-size:22px;font-weight:bold;color:white;text-align:center;border-bottom:1px solid rgba(255,255,255,.08)}
.menu{flex:1;padding:18px 12px}
.sidebar a{display:block;color:#ecf0f1;text-decoration:none;padding:12px 14px;margin-bottom:8px;border-radius:10px;font-weight:bold}
.sidebar a.active,.sidebar a:hover{background:rgba(255,255,255,.12)}
.logout{margin:12px;background:#e74c3c;color:#fff !important;text-align:center}
.content{margin-left:220px;padding:25px}
.card{background:#fff;border-radius:14px;padding:18px;margin-bottom:16px;box-shadow:0 8px 16px rgba(0,0,0,.06)}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px}
input,select,textarea{width:100%;padding:9px;border:1px solid #d5dce3;border-radius:8px;margin:6px 0 10px}
button{border:0;border-radius:8px;padding:9px 12px;color:white;background:#2c3e50;font-weight:700;cursor:pointer}
table{width:100%;border-collapse:collapse}
th{background:#2c3e50;color:#fff;padding:11px}
td{padding:10px;border-bottom:1px solid #edf1f4;text-align:center}
.badge{padding:5px 10px;border-radius:999px;font-size:12px;font-weight:bold}
.Normal{background:#d5f5e3;color:#145a32}
.Rusak{background:#fadbd8;color:#922b21}
.Perbaikan{background:#fdebd0;color:#7e5109}
.flash{background:#eafaf1;color:#1e8449;padding:10px;border-radius:8px;margin-bottom:12px}
</style>
</head>

<body>

<div class="sidebar">
    <div class="brand">MachinaFlow</div>
    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="mesin_rizkia.php" class="active">Kelola Mesin</a>
    </div>
    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Engineering Center</div>

<div class="content">

<?php if($flash){ ?>
<div class="flash"><?= htmlspecialchars($flash) ?></div>
<?php } ?>

<!-- STATUS MESIN -->
<div class="card">
<h3>Status Mesin</h3>
<table>
<tr><th>Mesin</th><th>Status</th><th>Durasi</th><th>Aksi</th></tr>

<?php while($m = mysqli_fetch_assoc($mesin_data)){ ?>
<tr>
<td><?= htmlspecialchars($m['nama_mesin_rizkia']) ?></td>
<td><span class="badge <?= $m['status_rizkia'] ?>"><?= $m['status_rizkia'] ?></span></td>
<td><?= (int)$m['durasi_produksi_rizkia'] ?></td>
<td>
<form method="POST" style="display:flex;gap:8px;justify-content:center;">
<?= csrf_input_rizkia(); ?>
<input type="hidden" name="id_mesin_rizkia" value="<?= (int)$m['id_rizkia'] ?>">
<select name="status_rizkia">
<?php foreach(['Normal','Rusak','Perbaikan'] as $s){ ?>
<option value="<?= $s ?>" <?= $m['status_rizkia']===$s?'selected':'' ?>><?= $s ?></option>
<?php } ?>
</select>
<button name="update_status_rizkia">Update</button>
</form>
</td>
</tr>
<?php } ?>

</table>
</div>

<!-- PM + EKSEKUSI -->
<div class="grid">

<div class="card">
<h3>Rencanakan PM</h3>
<form method="POST">
<?= csrf_input_rizkia(); ?>
<select name="mesin_id_rizkia">
<?php mysqli_data_seek($mesin_dropdown,0); while($mm=mysqli_fetch_assoc($mesin_dropdown)){ ?>
<option value="<?= $mm['id_rizkia'] ?>"><?= $mm['nama_mesin_rizkia'] ?></option>
<?php } ?>
</select>
<input type="date" name="tanggal_pm_rizkia">
<textarea name="checklist_rizkia"></textarea>
<button name="buat_pm_rizkia">Tambah</button>
</form>
</div>

<div class="card">
<h3>Eksekusi PM</h3>
<form method="POST">
<?= csrf_input_rizkia(); ?>
<select name="pm_id_rizkia">
<?php mysqli_data_seek($pm_data,0); while($pm=mysqli_fetch_assoc($pm_data)){ if($pm['status_rizkia']!='Terjadwal') continue; ?>
<option value="<?= $pm['id_rizkia'] ?>">
<?= $pm['nama_mesin_rizkia'] ?> - <?= $pm['tanggal_pm_rizkia'] ?>
</option>
<?php } ?>
</select>
<input type="datetime-local" name="mulai_rizkia">
<input type="datetime-local" name="selesai_rizkia">
<textarea name="catatan_rizkia"></textarea>
<button name="eksekusi_pm_rizkia">Simpan</button>
</form>
</div>

</div>

<!-- JADWAL PM -->
<div class="card">
<h3>Daftar Jadwal PM</h3>
<table>
<tr><th>ID</th><th>Mesin</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>

<?php mysqli_data_seek($pm_data,0); while($pm=mysqli_fetch_assoc($pm_data)){ ?>
<tr>
<td><?= $pm['id_rizkia'] ?></td>
<td><?= $pm['nama_mesin_rizkia'] ?></td>
<td><?= $pm['tanggal_pm_rizkia'] ?></td>
<td><?= $pm['status_rizkia'] ?></td>
<td>

<form method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
<?= csrf_input_rizkia(); ?>
<input type="hidden" name="id_pm_rizkia" value="<?= $pm['id_rizkia'] ?>">
<button name="hapus_pm_rizkia" style="background:#e74c3c;">Hapus</button>
</form>

</td>
</tr>
<?php } ?>

</table>
</div>

<!-- LOG -->
<div class="card">
<h3>Log Maintenance</h3>
<table>
<tr><th>Mesin</th><th>Mulai</th><th>Selesai</th><th>Durasi</th><th>Catatan</th></tr>

<?php while($log=mysqli_fetch_assoc($log_data)){ ?>
<tr>
<td><?= $log['nama_mesin_rizkia'] ?></td>
<td><?= $log['mulai_rizkia'] ?></td>
<td><?= $log['selesai_rizkia'] ?></td>
<td><?= $log['durasi_menit_rizkia'] ?></td>
<td><?= $log['catatan_rizkia'] ?></td>
</tr>
<?php } ?>

</table>
</div>

</div>

</body>
</html>