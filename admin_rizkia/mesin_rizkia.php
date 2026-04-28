<?php 

include '../config_rizkia/koneksi_rizkia.php';
session_start();

/* ROLE CHECK ADMIN */
if(!isset($_SESSION['user_rizkia']['role_rizkia']) || $_SESSION['user_rizkia']['role_rizkia'] != 'admin'){
    die("Akses ditolak. Hanya Admin.");
}

/* TAMBAH MESIN */
if(isset($_POST['tambah_rizkia'])){
    $nama = mysqli_real_escape_string($conn_rizkia, $_POST['nama']);
    $durasi = mysqli_real_escape_string($conn_rizkia, $_POST['durasi']);

    mysqli_query($conn_rizkia,"INSERT INTO mesin_rizkia 
    VALUES(NULL,'$nama','Normal','$durasi')");
}

/* EDIT MESIN */
if(isset($_POST['edit_rizkia'])){
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn_rizkia, $_POST['nama']);
    $durasi = mysqli_real_escape_string($conn_rizkia, $_POST['durasi']);

    mysqli_query($conn_rizkia,"
        UPDATE mesin_rizkia 
        SET nama_mesin_rizkia='$nama',
            durasi_produksi_rizkia='$durasi'
        WHERE id_rizkia='$id'
    ");
}

/* HAPUS MESIN */
if(isset($_POST['hapus_rizkia'])){
    $id = $_POST['id'];

    mysqli_query($conn_rizkia,"
        DELETE FROM mesin_rizkia WHERE id_rizkia='$id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mesin - MachinaFlow</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#eef2f7;
    color:#2c3e50;
}

/* ===== ANIMASI BARU (DARI DASHBOARD STYLE) ===== */
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
    background:linear-gradient(135deg,#1f2d3a,#2c3e50);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    padding-left:220px;
    font-size:24px;
    font-weight:bold;
    letter-spacing:1px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

/* SIDEBAR */
.sidebar{
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#2c3e50,#34495e);
    position:fixed;
    top:0;
    left:0;
    display:flex;
    flex-direction:column;
    box-shadow:4px 0 15px rgba(0,0,0,0.08);
}

.sidebar .logo{
    padding:24px 20px;
    font-size:22px;
    font-weight:bold;
    color:white;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.08);
    letter-spacing:1px;
}

.menu{
    flex:1;
    padding:18px 12px;
    overflow-y:auto;
}

.sidebar a{
    display:block;
    color:#ecf0f1;
    text-decoration:none;
    padding:12px 14px;
    margin-bottom:8px;
    border-radius:10px;
    transition:0.3s;
    font-size:14px;
    font-weight:bold;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.08);
    transform:translateX(4px);
}

.sidebar a.active{
    background:rgba(255,255,255,0.12);
}

.logout{
    margin:12px;
    background:#e74c3c !important;
    text-align:center;
    border-radius:10px;
}

/* CONTENT */
.content{
    margin-left:220px;
    padding:25px;
}

/* ===== CARD + ANIMASI ===== */
.card{
    background:white;
    border-radius:16px;
    padding:22px;
    margin-bottom:22px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);

    /* ANIMASI MASUK */
    animation:fadeUp 0.6s ease;
}

.card h3{
    margin-bottom:18px;
    font-size:20px;
    color:#2c3e50;
}

/* FORM */
.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr auto;
    gap:12px;
    align-items:end;
}

.input-group{
    display:flex;
    flex-direction:column;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #dcdde1;
    border-radius:10px;
    font-size:14px;
}

button{
    padding:12px 18px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#2c3e50,#34495e);
    color:white;
    font-weight:bold;
    cursor:pointer;
}

/* TABLE */
.table-wrap{overflow-x:auto;}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2c3e50;
    color:white;
    padding:14px;
}

td{
    padding:13px;
    text-align:center;
    border-bottom:1px solid #ecf0f1;

    /* ANIMASI ROW MASUK */
    animation:fadeUp 0.5s ease;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    background:#eafaf1;
    color:#27ae60;
}

/* BUTTON */
.btn{
    padding:6px 10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:12px;
    margin:2px;
}

.edit{background:#3498db;color:white;}
.delete{background:#e74c3c;color:white;}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;left:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}

.modal-content{
    background:white;
    padding:20px;
    border-radius:12px;
    width:320px;
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logo">MachinaFlow</div>

    <div class="menu">
        <a href="dashboard_rizkia.php">Dashboard</a>
        <a href="jobs_rizkia.php">Jobs</a>
        <a href="sparepart_rizkia.php">Sparepart</a>
        <a href="scheduling_rizkia.php">Scheduling</a>
        <a href="mesin_rizkia.php" class="active">Mesin</a>
        <a href="users_rizkia.php">Users</a>
        <a href="laporan_rizkia.php">Laporan</a>
        <a href="monitoring_rizkia.php">Monitoring</a>
    </div>

    <a class="logout" href="../auth_rizkia/logout_rizkia.php">Logout</a>
</div>

<div class="header">Machine Management</div>

<div class="content">

<div class="card">
<h3>Tambah Mesin</h3>

<form method="POST" class="form-grid">
    <div class="input-group">
        <label>Nama Mesin</label>
        <input type="text" name="nama" required>
    </div>

    <div class="input-group">
        <label>Durasi</label>
        <input type="number" name="durasi" required>
    </div>

    <button name="tambah_rizkia">Tambah</button>
</form>
</div>

<div class="card">
<h3>Data Mesin</h3>

<div class="table-wrap">
<table>
<tr>
    <th>ID</th>
    <th>Nama</th>
    <th>Durasi</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php
$data = mysqli_query($conn_rizkia,"SELECT * FROM mesin_rizkia");
while($d=mysqli_fetch_array($data)){
?>
<tr>
    <td><?= $d['id_rizkia'] ?></td>
    <td><?= $d['nama_mesin_rizkia'] ?></td>
    <td><?= $d['durasi_produksi_rizkia'] ?></td>
    <td><span class="status"><?= $d['status_rizkia'] ?></span></td>
    <td>
        <button class="btn edit"
        onclick="editData('<?= $d['id_rizkia'] ?>','<?= $d['nama_mesin_rizkia'] ?>','<?= $d['durasi_produksi_rizkia'] ?>')">
        Edit</button>

        <button class="btn delete"
        onclick="hapusData('<?= $d['id_rizkia'] ?>')">
        Hapus</button>
    </td>
</tr>
<?php } ?>
</table>
</div>
</div>

</div>

<!-- MODAL EDIT -->
<div class="modal" id="editModal">
<div class="modal-content">
<h3>Edit Mesin</h3>
<form method="POST">
    <input type="hidden" name="id" id="edit_id">
    <input type="text" name="nama" id="edit_nama">
    <input type="number" name="durasi" id="edit_durasi">
    <button name="edit_rizkia">Simpan</button>
</form>
</div>
</div>

<!-- MODAL HAPUS -->
<div class="modal" id="deleteModal">
<div class="modal-content">
<h3>Hapus Data?</h3>
<form method="POST">
    <input type="hidden" name="id" id="delete_id">
    <button name="hapus_rizkia">Hapus</button>
</form>
</div>
</div>

<script>
function editData(id,nama,durasi){
    document.getElementById('edit_id').value=id;
    document.getElementById('edit_nama').value=nama;
    document.getElementById('edit_durasi').value=durasi;
    document.getElementById('editModal').style.display='flex';
}

function hapusData(id){
    document.getElementById('delete_id').value=id;
    document.getElementById('deleteModal').style.display='flex';
}

window.onclick=function(e){
    if(e.target.className=='modal'){
        e.target.style.display='none';
    }
}
</script>

</body>
</html> 