<?php
session_start();
include '../connecting.php';

// Jika ada request method POST (pengguna mengirimkan form login)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah USERNAME_ADMIN dan PASSWORD_ADMIN sudah di-set
    if (isset($_POST['USERNAME_ADMIN']) && isset($_POST['PASSWORD_ADMIN'])) {
        $username_login = $_POST['USERNAME_ADMIN'];
        $password = md5($_POST['PASSWORD_ADMIN']);
        
        // Siapkan pernyataan SQL untuk memilih password hash
        $sql = "SELECT ID_DATA_ADMIN, PASSWORD_ADMIN FROM data_admin WHERE USERNAME_ADMIN = ?";
        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username_login);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $id_admin, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    // Verifikasi password (gunakan md5 hanya jika password disimpan sebagai md5)
                    if ($password == $hashed_password) {
                        // Password benar, set session variabel
                        $_SESSION['login'] = true;
                        $_SESSION['USERNAME_ADMIN'] = $username_login;
                        $_SESSION['ID_DATA_ADMIN'] = $id_admin;
                        header("Location: beranda-admin.php");
                        exit();
                    } else {
                        echo "<script>alert('Username atau password tidak valid');</script>";
                        header("Location: login-admin.php");
                    }
                } else {
                    echo "<script>alert('Username atau password tidak valid');</script>";
                    header("Location: login-admin.php");
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Terjadi kesalahan: " . mysqli_error($koneksi);
            }
        } else {
            echo "Terjadi kesalahan: " . mysqli_error($koneksi);
        }

        mysqli_close($koneksi);
    } else {
        echo "<script>alert('Mohon masukkan username dan password');</script>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f8f9fa;
    }
    .login-container {
      max-width: 400px;
      width: 100%;
      padding: 20px;
      background-color: #ffffff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    .login-container .form-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #c0392b;
    }
    .login-container .form-control {
      padding-left: 40px;
    }
    .login-container .btn-primary {
      background-color: #c0392b;
      border-color: #c0392b;
    }
    .login-container .btn-primary:hover {
      background-color: #e74c3c;
      border-color: #e74c3c;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2 class="text-center">Admin Login</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <div class="mb-3">
        <label for="USERNAME_ADMIN" class="form-label">Username</label>
        <input type="text" class="form-control" name="USERNAME_ADMIN" id="USERNAME_ADMIN" placeholder="Username">
      </div>
      <div class="mb-3">
        <label for="PASSWORD_ADMIN" class="form-label">Password</label>
        <input type="password" class="form-control" name="PASSWORD_ADMIN" id="PASSWORD_ADMIN" placeholder="Password">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
