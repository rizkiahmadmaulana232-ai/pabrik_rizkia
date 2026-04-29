<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';
include '../config_rizkia/security_rizkia.php';

if(isset($_POST['daftar_rizkia'])){
    if(!csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
        $popup_rizkia = "Token keamanan tidak valid. Coba refresh halaman.";
    } else {
        $username_rizkia = trim($_POST['username_rizkia'] ?? '');
        $password_input_rizkia = $_POST['password_rizkia'] ?? '';
        $role_rizkia = $_POST['role_rizkia'] ?? '';

        $role_valid_rizkia = ['admin', 'operator', 'engineering'];
        if($username_rizkia === '' || $password_input_rizkia === '' || !in_array($role_rizkia, $role_valid_rizkia, true)){
            $popup_rizkia = "Data registrasi tidak valid.";
        } else {
            $password_rizkia = password_hash($password_input_rizkia, PASSWORD_DEFAULT);
            $stmt_rizkia = mysqli_prepare($conn_rizkia, "INSERT INTO users_rizkia (username_rizkia, password_rizkia, role_rizkia, created_at_rizkia) VALUES(?,?,?,NOW())");
            mysqli_stmt_bind_param($stmt_rizkia, "sss", $username_rizkia, $password_rizkia, $role_rizkia);
            if(mysqli_stmt_execute($stmt_rizkia)){
                $popup_rizkia = "Registrasi berhasil! Silakan login.";
            } else {
                $popup_rizkia = "Registrasi gagal. Username mungkin sudah dipakai.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - MachinaFlow</title>
    <style>
        body{
            margin:0;
            padding:0;
            font-family:Arial, sans-serif;
            background:linear-gradient(135deg,#1f2d3a,#34495e,#2c3e50);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            overflow:hidden;
            position:relative;
        }

        /* BACKGROUND ANIMATION */
        .background-circle1,
        .background-circle2,
        .background-circle3{
            position:absolute;
            border-radius:50%;
            background:rgba(255,255,255,0.05);
            z-index:0;
            animation:floatCircle 10s ease-in-out infinite;
        }

        .background-circle1{
            width:320px;
            height:320px;
            top:-100px;
            left:-100px;
        }

        .background-circle2{
            width:250px;
            height:250px;
            bottom:-80px;
            right:-80px;
            animation-delay:2s;
        }

        .background-circle3{
            width:180px;
            height:180px;
            top:120px;
            right:180px;
            animation-delay:4s;
        }

        @keyframes floatCircle{
            0%,100%{
                transform:translateY(0px);
            }
            50%{
                transform:translateY(-20px);
            }
        }

        /* BRAND OUTSIDE */
        .brand-side{
            position:absolute;
            left:60px;
            color:white;
            z-index:2;
            width:420px;
            animation:fadeLeft 1.2s ease;
        }

        .brand-side h1{
            margin:0;
            font-size:56px;
            letter-spacing:3px;
            font-weight:bold;
            animation:glowText 3s ease-in-out infinite;
        }

        .brand-side p{
            margin-top:12px;
            font-size:16px;
            color:#dfe6e9;
            letter-spacing:2px;
            text-transform:uppercase;
        }

        @keyframes fadeLeft{
            from{
                opacity:0;
                transform:translateX(-40px);
            }
            to{
                opacity:1;
                transform:translateX(0);
            }
        }

        @keyframes glowText{
            0%,100%{
                text-shadow:0 0 10px rgba(255,255,255,0.15);
            }
            50%{
                text-shadow:0 0 20px rgba(255,255,255,0.35);
            }
        }

        /* REGISTER BOX */
        .register-box{
            width:420px;
            background:rgba(255,255,255,0.98);
            padding:40px;
            border-radius:18px;
            box-shadow:0 15px 40px rgba(0,0,0,0.30);
            position:relative;
            z-index:2;
            margin-left:520px;
            animation:fadeIn 0.7s ease-in-out;
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(20px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        .register-box h2{
            margin:0 0 8px;
            text-align:center;
            color:#2c3e50;
            font-size:30px;
        }

        .register-box p{
            text-align:center;
            color:#7f8c8d;
            margin-bottom:28px;
            font-size:14px;
        }

        .success{
            background:#eafaf1;
            color:#27ae60;
            padding:12px;
            border-radius:8px;
            margin-bottom:18px;
            text-align:center;
            font-size:14px;
            border:1px solid #b7e4c7;
        }

        .input-group{
            margin-bottom:16px;
        }

        .input-group label{
            display:block;
            margin-bottom:6px;
            color:#2c3e50;
            font-size:14px;
            font-weight:bold;
        }

        input, select{
            width:100%;
            padding:12px;
            border:1px solid #dcdde1;
            border-radius:10px;
            font-size:14px;
            box-sizing:border-box;
            background:#fdfdfd;
            transition:0.3s;
        }

        input:focus, select:focus{
            outline:none;
            border-color:#2980b9;
            box-shadow:0 0 8px rgba(41,128,185,0.2);
            background:white;
        }

        button{
            width:100%;
            padding:13px;
            background:linear-gradient(135deg,#2c3e50,#34495e);
            color:white;
            border:none;
            border-radius:10px;
            font-size:15px;
            font-weight:bold;
            cursor:pointer;
            transition:0.3s;
            box-shadow:0 5px 15px rgba(44,62,80,0.25);
        }

        button:hover{
            transform:translateY(-1px);
            background:linear-gradient(135deg,#1f2d3a,#2c3e50);
        }

        .links{
            margin-top:20px;
            text-align:center;
            padding-top:15px;
            border-top:1px solid #eee;
        }

        .links a{
            text-decoration:none;
            color:#2980b9;
            font-size:14px;
            font-weight:bold;
        }

        .links a:hover{
            color:#1f5f8b;
            text-decoration:underline;
        }
    </style>
</head>
<body>

<div class="background-circle1"></div>
<div class="background-circle2"></div>
<div class="background-circle3"></div>

<div class="brand-side">
    <h1>MACHINAFLOW</h1>
    <p>Production Scheduling & Machine Control System</p>
</div>

<div class="register-box">
    <h2>Register</h2>
    <p>Buat akun baru untuk mengakses sistem produksi</p>

    <form method="POST">
        <?= csrf_input_rizkia(); ?>

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username_rizkia" placeholder="Masukkan username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password_rizkia" placeholder="Masukkan password" required>
        </div>

        <div class="input-group">
            <label>Role</label>
            <select name="role_rizkia" required>
                <option value="admin">Admin</option>
                <option value="operator">Operator</option>
                <option value="engineering">Engineering</option>
            </select>
        </div>

        <button name="daftar_rizkia">Daftar</button>
    </form>

    <div class="links">
        <a href="login_rizkia.php">Sudah punya akun? Login</a>
    </div>
</div>

<?php if(isset($popup_rizkia)){ ?>
<script>
    alert("<?= addslashes($popup_rizkia); ?>");
    <?php if(strpos($popup_rizkia, 'berhasil') !== false){ ?>
    window.location.href = "login_rizkia.php";
    <?php } ?>
</script>
<?php } ?>

</body>
</html>
