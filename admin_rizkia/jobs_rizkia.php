<?php
include '../config_rizkia/koneksi_rizkia.php';
session_start();

if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'admin'){
    die("Akses ditolak. Hanya Admin.");
}

mysqli_query($conn_rizkia, "CREATE TABLE IF NOT EXISTS sparepart_master (
    id_rizkia INT AUTO_INCREMENT PRIMARY KEY,
    kode_part_rizkia VARCHAR(50) NOT NULL UNIQUE,
    nama_part_rizkia VARCHAR(150) NOT NULL,
    kategori_rizkia VARCHAR(100) DEFAULT NULL,
    tipe_motor_rizkia VARCHAR(100) DEFAULT NULL,
    satuan_rizkia VARCHAR(20) DEFAULT 'pcs',
    grade_kualitas_rizkia VARCHAR(50) DEFAULT NULL,
    aktif_rizkia TINYINT(1) DEFAULT 1
)");

mysqli_query($conn_rizkia, "ALTER TABLE jobs_rizkia ADD COLUMN IF NOT EXISTS sparepart_id_rizkia INT NULL");

function resolve_nama_job($conn_rizkia, $sparepart_id, $nama_input){
    $nama = mysqli_real_escape_string($conn_rizkia, trim($nama_input));
    $sparepart_id = (int)$sparepart_id;

    if($sparepart_id > 0){
        $sparepart = mysqli_fetch_assoc(mysqli_query($conn_rizkia, "SELECT nama_part_rizkia FROM sparepart_master WHERE id_rizkia='$sparepart_id'"));
        if($sparepart){
            $nama = mysqli_real_escape_string($conn_rizkia, "Produksi " . $sparepart['nama_part_rizkia']);
        }
    }

    if($nama == ''){
        $nama = 'Job Produksi';
    }

    return $nama;
}

if(isset($_POST['tambah_rizkia'])){
    $sparepart_id = isset($_POST['sparepart_id_rizkia']) ? (int)$_POST['sparepart_id_rizkia'] : 0;
    $nama = resolve_nama_job($conn_rizkia, $sparepart_id, $_POST['nama_rizkia'] ?? '');
    $jumlah = (int)$_POST['jumlah_rizkia'];
    $deadline = mysqli_real_escape_string($conn_rizkia, $_POST['deadline_rizkia']);

    mysqli_query($conn_rizkia,"INSERT INTO jobs_rizkia
    (nama_job_rizkia, jumlah_rizkia, deadline_rizkia, status_rizkia, sparepart_id_rizkia)
    VALUES('$nama','$jumlah','$deadline','Menunggu'," . ($sparepart_id > 0 ? "'$sparepart_id'" : "NULL") . ")");
}

if(isset($_POST['update_rizkia'])){
    $id = (int)$_POST['id'];
    $sparepart_id = isset($_POST['sparepart_id_rizkia']) ? (int)$_POST['sparepart_id_rizkia'] : 0;
    $nama = resolve_nama_job($conn_rizkia, $sparepart_id, $_POST['nama_rizkia'] ?? '');
    $jumlah = (int)$_POST['jumlah_rizkia'];
    $deadline = mysqli_real_escape_string($conn_rizkia, $_POST['deadline_rizkia']);

    mysqli_query($conn_rizkia,"UPDATE jobs_rizkia SET
    nama_job_rizkia='$nama',
    jumlah_rizkia='$jumlah',
    deadline_rizkia='$deadline',
    sparepart_id_rizkia=" . ($sparepart_id > 0 ? "'$sparepart_id'" : "NULL") . "
    WHERE id_rizkia='$id'");
}

if(isset($_POST['hapus_rizkia'])){
    $id = (int)$_POST['id'];
    mysqli_query($conn_rizkia,"DELETE FROM jobs_rizkia WHERE id_rizkia='$id'");
}

$spareparts = mysqli_query($conn_rizkia, "SELECT id_rizkia, kode_part_rizkia, nama_part_rizkia FROM sparepart_master ORDER BY nama_part_rizkia ASC");
$data = mysqli_query($conn_rizkia,"SELECT j.*, s.kode_part_rizkia, s.nama_part_rizkia
FROM jobs_rizkia j
LEFT JOIN sparepart_master s ON j.sparepart_id_rizkia = s.id_rizkia
ORDER BY j.id_rizkia DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Jobs - MachinaFlow</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
body{background:#eef2f7;color:#2c3e50}

/* ===== ANIMASI BARU ===== */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(18px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.header{
    height:80px;
    margin-left:220px;
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:bold
}

.sidebar{
    width:220px;height:100vh;
    background:linear-gradient(180deg,#2c3e50,#34495e);
    position:fixed;top:0;left:0;
    display:flex;flex-direction:column
}

.logo{
    padding:24px 20px;
    font-size:22px;
    font-weight:bold;
    color:white;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.08)
}

.menu{flex:1;padding:18px 12px;overflow-y:auto}

.sidebar a{
    display:block;color:#ecf0f1;
    text-decoration:none;
    padding:12px 14px;
    margin-bottom:8px;
    border-radius:10px;
    font-size:14px;
    font-weight:bold
}

.sidebar a:hover{background:rgba(255,255,255,0.08)}
.sidebar a.active{background:rgba(255,255,255,0.12)}
.logout{margin:12px;background:#e74c3c !important;text-align:center;border-radius:10px}

.content{margin-left:220px;padding:25px}

/* ===== CARD ANIMASI ===== */
.card{
    background:white;
    border-radius:16px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    animation:fadeUp 0.6s ease;
}

.card h3{margin-bottom:16px}

/* INPUT */
input,select{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #dcdde1;
    border-radius:10px
}

table{
    width:100%;
    border-collapse:collapse
}

th{
    background:#2c3e50;
    color:white;
    padding:12px;
    text-align:center
}

/* ===== ROW ANIMASI ===== */
td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ecf0f1;
    animation:fadeUp 0.5s ease;
}

button{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    color:white
}

.btn-update{background:#2c3e50}
.btn-edit{background:#3498db}
.btn-hapus{background:#e74c3c}
.btn-batal{background:#7f8c8d}

.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    justify-content:center;
    align-items:center
}

.modal-box{
    background:white;
    width:420px;
    padding:20px;
    border-radius:16px;
    animation:fadeUp 0.4s ease;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>
    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="jobs_rizkia.php" class="active">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>
    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Jobs Management</div>

<div class="content">
    <div class="card">
        <h3>Tambah Job Produksi</h3>
        <form method="POST">
            <label>Sparepart</label>
            <select name="sparepart_id_rizkia">
                <option value="0">-- Manual / Belum ditentukan --</option>
                <?php mysqli_data_seek($spareparts, 0); while($s=mysqli_fetch_assoc($spareparts)){ ?>
                    <option value="<?= $s['id_rizkia'] ?>">
                        <?= htmlspecialchars($s['kode_part_rizkia'].' - '.$s['nama_part_rizkia']) ?>
                    </option>
                <?php } ?>
            </select>

            <input type="text" name="nama_rizkia" placeholder="Nama Job">
            <input type="number" name="jumlah_rizkia" placeholder="Jumlah" required>
            <input type="date" name="deadline_rizkia" required>

            <button class="btn-update" name="tambah_rizkia">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h3>Data Jobs</h3>
        <table>
            <tr>
                <th>Sparepart</th>
                <th>Nama Job</th>
                <th>Jumlah</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php while($d=mysqli_fetch_assoc($data)){ ?>
            <tr>
                <td><?= $d['kode_part_rizkia'] ? htmlspecialchars($d['kode_part_rizkia'].' - '.$d['nama_part_rizkia']) : '-' ?></td>
                <td><?= htmlspecialchars($d['nama_job_rizkia']) ?></td>
                <td><?= (int)$d['jumlah_rizkia'] ?></td>
                <td><?= htmlspecialchars($d['deadline_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['status_rizkia']) ?></td>
                <td>
                    <button class="btn-edit" onclick="openEdit('<?= $d['id_rizkia'] ?>','<?= (int)$d['sparepart_id_rizkia'] ?>','<?= htmlspecialchars($d['nama_job_rizkia'], ENT_QUOTES) ?>','<?= (int)$d['jumlah_rizkia'] ?>','<?= $d['deadline_rizkia'] ?>')">Edit</button>

                    <form method="POST" style="display:inline">
                        <input type="hidden" name="id" value="<?= $d['id_rizkia'] ?>">
                        <button class="btn-hapus" name="hapus_rizkia">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<div id="modal" class="modal">
    <div class="modal-box">
        <h3>Edit Job</h3>
        <form method="POST">
            <input type="hidden" name="id" id="id">
            <select name="sparepart_id_rizkia" id="sparepart_id">
                <option value="0">-- Manual --</option>
                <?php mysqli_data_seek($spareparts, 0); while($s=mysqli_fetch_assoc($spareparts)){ ?>
                    <option value="<?= $s['id_rizkia'] ?>">
                        <?= htmlspecialchars($s['kode_part_rizkia'].' - '.$s['nama_part_rizkia']) ?>
                    </option>
                <?php } ?>
            </select>

            <input type="text" name="nama_rizkia" id="nama">
            <input type="number" name="jumlah_rizkia" id="jumlah">
            <input type="date" name="deadline_rizkia" id="deadline">

            <button class="btn-update" name="update_rizkia">Update</button>
            <button type="button" class="btn-batal" onclick="closeModal()">Batal</button>
        </form>
    </div>
</div>

<script>
function openEdit(id, sparepartId, nama, jumlah, deadline){
    document.getElementById('modal').style.display='flex';
    document.getElementById('id').value=id;
    document.getElementById('sparepart_id').value=sparepartId;
    document.getElementById('nama').value=nama;
    document.getElementById('jumlah').value=jumlah;
    document.getElementById('deadline').value=deadline;
}
function closeModal(){
    document.getElementById('modal').style.display='none';
}
</script>

</body>
</html>