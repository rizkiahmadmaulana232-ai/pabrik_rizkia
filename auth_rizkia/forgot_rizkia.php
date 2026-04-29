<?php

include '../config_rizkia/koneksi_rizkia.php';
session_start();
include '../config_rizkia/security_rizkia.php';

if(isset($_POST['reset_rizkia'])){
    if(!csrf_validate_rizkia($_POST['csrf_token_rizkia'] ?? '')){
<<<<<<< codex/jelaskan-logika-alur-yang-dibuat-7lztii
        $popup_rizkia = "Token keamanan tidak valid. Coba refresh halaman.";
        $popup_type_rizkia = "error";
=======
        $error = "Token keamanan tidak valid. Coba refresh halaman.";
>>>>>>> main
    } else {
        $username_rizkia = trim($_POST['username_rizkia'] ?? '');
        $password_baru_input = $_POST['password_baru_rizkia'] ?? '';

        if($username_rizkia === '' || $password_baru_input === ''){
<<<<<<< codex/jelaskan-logika-alur-yang-dibuat-7lztii
            $popup_rizkia = "Username dan password baru wajib diisi.";
            $popup_type_rizkia = "error";
=======
            $error = "Username dan password baru wajib diisi.";
>>>>>>> main
        } else {
            $password_baru_rizkia = password_hash($password_baru_input, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn_rizkia, "UPDATE users_rizkia SET password_rizkia=? WHERE username_rizkia=?");
            mysqli_stmt_bind_param($stmt, "ss", $password_baru_rizkia, $username_rizkia);
            mysqli_stmt_execute($stmt);

            if(mysqli_stmt_affected_rows($stmt) > 0){
<<<<<<< codex/jelaskan-logika-alur-yang-dibuat-7lztii
                $popup_rizkia = "Password berhasil diubah!";
                $popup_type_rizkia = "success";
            } else {
                $popup_rizkia = "Username tidak ditemukan.";
                $popup_type_rizkia = "error";
=======
                $success = "Password berhasil diubah!";
            } else {
                $error = "Username tidak ditemukan.";
>>>>>>> main
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - MachinaFlow</title>
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
            right:220px;
            animation-delay:4s;
        }

        @keyframes floatCircle{
            0%,100%{ transform:translateY(0px); }
            50%{ transform:translateY(-20px); }
        }

        /* BRAND SIDE */
        .brand-side{
            position:absolute;
            left:70px;
            width:420px;
            color:white;
            z-index:2;
            animation:fadeLeft 1.2s ease;
        }

        .brand-side h1{
            margin:0;
            font-size:52px;
            letter-spacing:3px;
            font-weight:bold;
            line-height:1.1;
            animation:glowText 3s ease-in-out infinite;
        }

        .brand-side p{
            margin-top:14px;
            font-size:15px;
            color:#dfe6e9;
            letter-spacing:2px;
            text-transform:uppercase;
            line-height:1.6;
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

        /* RESET BOX */
        .reset-box{
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

        .reset-box h2{
            margin:0 0 8px;
            text-align:center;
            color:#2c3e50;
            font-size:30px;
        }

        .reset-box p{
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

        input{
            width:100%;
            padding:12px;
            border:1px solid #dcdde1;
            border-radius:10px;
            font-size:14px;
            box-sizing:border-box;
            background:#fdfdfd;
            transition:0.3s;
        }

        input:focus{
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

<div class="reset-box">
    <h2>Reset Password</h2>
    <p>Masukkan username dan password baru Anda</p>

<<<<<<< codex/jelaskan-logika-alur-yang-dibuat-7lztii
=======
    <?php if(isset($success)){ ?>
        <div class="success"><?= $success ?></div>
    <?php } ?>

    <?php if(isset($error)){ ?>
        <div class="success" style="background:#fdecea;color:#c0392b;border-color:#f5c6cb;"><?= $error ?></div>
    <?php } ?>

>>>>>>> main
    <form method="POST">
        <?= csrf_input_rizkia(); ?>

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username_rizkia" placeholder="Masukkan username" required>
        </div>

        <div class="input-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru_rizkia" placeholder="Masukkan password baru" required>
        </div>

        <button name="reset_rizkia">Reset Password</button>
    </form>

    <div class="links">
        <a href="login_rizkia.php">Kembali ke Login</a>
    </div>
</div>

<?php if(isset($popup_rizkia)){ ?>
<script>
    alert("<?= addslashes($popup_rizkia); ?>");
</script>
<?php } ?>

</body>
</html>
