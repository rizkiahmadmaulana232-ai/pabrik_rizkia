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

/* ===== ANIMASI ===== */
?>

<!DOCTYPE html>
<html>
<head>
<title>Sparepart - MachinaFlow</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
body{background:#eef2f7;color:#2c3e50}

/* ===== ANIMASI MASUK (DARI DASHBOARD STYLE) ===== */
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

/* HEADER */
.header{
    height:80px;
    margin-left:220px;
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    font-weight:bold;
}

/* SIDEBAR */
.sidebar{
    width:220px;height:100vh;
    background:linear-gradient(180deg,#2c3e50,#34495e);
    position:fixed;top:0;left:0;
    display:flex;flex-direction:column;
}

.logo{
    padding:24px 20px;
    font-size:22px;
    font-weight:bold;
    color:white;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.menu{flex:1;padding:18px 12px;overflow-y:auto}

.sidebar a{
    display:block;
    color:#ecf0f1;
    text-decoration:none;
    padding:12px 14px;
    margin-bottom:8px;
    border-radius:10px;
    font-size:14px;
    font-weight:bold;
}

.sidebar a:hover{background:rgba(255,255,255,0.08)}
.sidebar a.active{background:rgba(255,255,255,0.12)}
.logout{margin:12px;background:#e74c3c!important;text-align:center;border-radius:10px}

/* CONTENT */
.content{
    margin-left:220px;
    padding:25px;
}

/* CARD + ANIMASI */
.card{
    background:white;
    border-radius:16px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
    animation:fadeUp 0.6s ease;
}

.card h3{
    margin-bottom:16px;
}

/* INPUT */
input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #dcdde1;
    border-radius:10px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2c3e50;
    color:white;
    padding:12px;
    text-align:center;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ecf0f1;

    /* ANIMASI ROW MASUK */
    animation:fadeUp 0.5s ease;
}

/* BUTTON */
button{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    color:white;
}

.btn-update{background:#2c3e50}
.btn-edit{background:#3498db}
.btn-hapus{background:#e74c3c}
.btn-batal{background:#7f8c8d}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    justify-content:center;
    align-items:center;
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
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php" class="active">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>
    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Master Sparepart</div>

<div class="content">

    <div class="card">
        <h3>Tambah Sparepart</h3>
        <form method="POST">
            <input type="text" name="kode_part_rizkia" placeholder="Kode Part" required>
            <input type="text" name="nama_part_rizkia" placeholder="Nama Part" required>
            <input type="text" name="kategori_rizkia" placeholder="Kategori">
            <input type="text" name="tipe_motor_rizkia" placeholder="Tipe Motor">
            <input type="text" name="satuan_rizkia" placeholder="Satuan (pcs/set)" value="pcs">
            <input type="text" name="grade_kualitas_rizkia" placeholder="Grade Kualitas">
            <button class="btn-update" name="tambah_rizkia">Tambah</button>
        </form>
    </div>

    <div class="card">
        <h3>Data Sparepart</h3>

        <table>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Satuan</th>
                <th>Grade</th>
                <th>Aksi</th>
            </tr>

            <?php $data = mysqli_query($conn_rizkia, "SELECT * FROM sparepart_master ORDER BY id_rizkia DESC"); while($d=mysqli_fetch_array($data)){ ?>
            <tr>
                <td><?= htmlspecialchars($d['kode_part_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['nama_part_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['kategori_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['tipe_motor_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['satuan_rizkia']) ?></td>
                <td><?= htmlspecialchars($d['grade_kualitas_rizkia']) ?></td>
                <td>
                    <button class="btn-edit"
                    onclick="openEdit('<?= $d['id_rizkia'] ?>','<?= htmlspecialchars($d['kode_part_rizkia'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['nama_part_rizkia'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['kategori_rizkia'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['tipe_motor_rizkia'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['satuan_rizkia'],ENT_QUOTES) ?>','<?= htmlspecialchars($d['grade_kualitas_rizkia'],ENT_QUOTES) ?>')">
                    Edit</button>

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
        <h3>Edit Sparepart</h3>
        <form method="POST">
            <input type="hidden" name="id" id="id">
            <input type="text" name="kode_part_rizkia" id="kode" required>
            <input type="text" name="nama_part_rizkia" id="nama" required>
            <input type="text" name="kategori_rizkia" id="kategori">
            <input type="text" name="tipe_motor_rizkia" id="tipe">
            <input type="text" name="satuan_rizkia" id="satuan">
            <input type="text" name="grade_kualitas_rizkia" id="grade">

            <button class="btn-update" name="update_rizkia">Update</button>
            <button type="button" class="btn-batal" onclick="closeModal()">Batal</button>
        </form>
    </div>
</div>

<script>
function openEdit(id,kode,nama,kategori,tipe,satuan,grade){
    document.getElementById('modal').style.display='flex';
    document.getElementById('id').value=id;
    document.getElementById('kode').value=kode;
    document.getElementById('nama').value=nama;
    document.getElementById('kategori').value=kategori;
    document.getElementById('tipe').value=tipe;
    document.getElementById('satuan').value=satuan;
    document.getElementById('grade').value=grade;
}
function closeModal(){
    document.getElementById('modal').style.display='none';
}
</script>

</body>
</html>