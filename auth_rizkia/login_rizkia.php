<?php

session_start();
include '../config_rizkia/koneksi_rizkia.php';

if(isset($_POST['login_rizkia'])){
    $username_rizkia = $_POST['username_rizkia'];
    $password_rizkia = md5($_POST['password_rizkia']);

    $query_rizkia = mysqli_query($conn_rizkia,"SELECT * FROM users_rizkia 
        WHERE username_rizkia='$username_rizkia' AND password_rizkia='$password_rizkia'");

    $data_rizkia = mysqli_fetch_assoc($query_rizkia);

    if($data_rizkia){
        $_SESSION['user_rizkia'] = $data_rizkia;

        if($data_rizkia['role_rizkia'] == 'admin'){
            header("Location: ../admin_rizkia/dashboard_rizkia.php");
        }elseif($data_rizkia['role_rizkia'] == 'operator'){
            header("Location: ../operator_rizkia/dashboard_rizkia.php");
        }else{
            header("Location: ../engineering_rizkia/dashboard_rizkia.php");
        }
        exit;
    }else{
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - MachinaFlow</title>
    <style>
        body{
            margin:0;
            padding:0;
            font-family:Arial, sans-serif;
            background:linear-gradient(135deg,#1f2d3a,#34495e,#2c3e50);
            height:100vh;
            overflow:hidden;
        }

        .background-circle1,
        .background-circle2{
            position:absolute;
            border-radius:50%;
            background:rgba(255,255,255,0.05);
            z-index:0;
            animation:floatCircle 10s ease-in-out infinite;
        }

        .background-circle1{
            width:320px;
            height:320px;
            top:-90px;
            left:-90px;
        }

        .background-circle2{
            width:260px;
            height:260px;
            bottom:-80px;
            right:-80px;
            animation-delay:2s;
        }

        @keyframes floatCircle{
            0%,100%{ transform:translateY(0px); }
            50%{ transform:translateY(20px); }
        }

        .container{
            display:flex;
            width:100%;
            height:100vh;
            position:relative;
            z-index:2;
        }

        .left-side{
            width:50%;
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:80px;
            color:white;
            animation:slideLeft 1s ease;
        }

        @keyframes slideLeft{
            from{
                opacity:0;
                transform:translateX(-50px);
            }
            to{
                opacity:1;
                transform:translateX(0);
            }
        }

        .left-side h1{
            margin:0;
            font-size:56px;
            font-weight:bold;
            letter-spacing:3px;
            animation:glowText 3s ease-in-out infinite;
        }

        @keyframes glowText{
            0%,100%{
                text-shadow:0 0 10px rgba(255,255,255,0.15);
            }
            50%{
                text-shadow:0 0 25px rgba(255,255,255,0.35);
            }
        }

        .left-side h2{
            margin:15px 0;
            font-size:22px;
            font-weight:normal;
            color:#dfe6e9;
            animation:fadeUp 1.2s ease;
        }

        .left-side p{
            margin-top:10px;
            max-width:500px;
            line-height:1.8;
            font-size:15px;
            color:#bdc3c7;
            animation:fadeUp 1.5s ease;
        }

        @keyframes fadeUp{
            from{
                opacity:0;
                transform:translateY(20px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        .right-side{
            width:50%;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .login-box{
            width:400px;
            background:rgba(255,255,255,0.98);
            padding:40px;
            border-radius:18px;
            box-shadow:0 15px 40px rgba(0,0,0,0.30);
            animation:fadeIn 0.8s ease-in-out;
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

        .login-box h2{
            margin:0 0 8px;
            text-align:center;
            color:#2c3e50;
            font-size:28px;
        }

        .login-box p{
            text-align:center;
            color:#7f8c8d;
            margin-bottom:28px;
            font-size:14px;
        }

        .error{
            background:#fdecea;
            color:#c0392b;
            padding:12px;
            border-radius:8px;
            margin-bottom:18px;
            text-align:center;
            font-size:14px;
            border:1px solid #f5c6cb;
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
        }

        button:hover{
            transform:translateY(-2px);
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
            margin:0 10px;
            font-weight:bold;
        }

        .links a:hover{
            text-decoration:underline;
        }
    </style>
</head>
<body>

<div class="background-circle1"></div>
<div class="background-circle2"></div>

<div class="container">

    <div class="left-side">
        <h1>MACHINAFLOW</h1>
        <h2>Production Scheduling & Machine Control System</h2>
        <p>
            Sistem manajemen produksi modern untuk mengatur penjadwalan,
            kontrol mesin, monitoring operator, dan efisiensi proses manufaktur
            dalam satu platform terintegrasi.
        </p>
    </div>

    <div class="right-side">
        <div class="login-box">
            <h2>Login</h2>
            <p>Masuk ke sistem untuk melanjutkan aktivitas produksi</p>

            <?php if(isset($error)){ ?>
                <div class="error"><?= $error ?></div>
            <?php } ?>

            <form method="POST">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username_rizkia" placeholder="Masukkan username" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password_rizkia" placeholder="Masukkan password" required>
                </div>

                <button name="login_rizkia">Login</button>
            </form>

            <div class="links">
                <a href="register_rizkia.php">Daftar</a>
                <a href="forgot_rizkia.php">Lupa Password</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>