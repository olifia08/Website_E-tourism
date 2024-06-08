<?php
session_start();
include '../connecting.php';

$error_message = "";

// Jika ada request method POST (pengguna mengirimkan form login)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memeriksa apakah Username dan Password sudah di-set
    if (isset($_POST['Username']) && isset($_POST['Password'])) {
        $username_login = $_POST['Username'];
        $password_input = md5($_POST['Password']);
        
        // Siapkan pernyataan SQL untuk memilih password hash
        $sql = "SELECT ID_USER, PASSWORD_USER FROM user WHERE USERNAME_USER = ?";
        $stmt = mysqli_prepare($koneksi, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username_login);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $id_user, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    // Verifikasi password (gunakan md5 hanya jika password disimpan sebagai md5)
                    if ($password_input == $hashed_password) {
                        // Password benar, set session variabel
                        $_SESSION['login'] = true;
                        $_SESSION['USERNAME_USER'] = $username_login;
                        $_SESSION['ID_USER'] = $id_user;
                        header("Location: beranda-user-ticketing.php");
                        exit();
                    } else {
                        $error_message = "Username atau password tidak valid";
                    }
                } else {
                    $error_message = "Username atau password tidak valid";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Terjadi kesalahan: " . mysqli_error($koneksi);
            }
        } else {
            $error_message = "Terjadi kesalahan: " . mysqli_error($koneksi);
        }

        mysqli_close($koneksi);
    } else {
        $error_message = "Mohon masukkan username dan password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alex+Brush" rel="stylesheet">
    <link rel="stylesheet" href="../css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../css/ionicons.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.timepicker.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/icomoon.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="oi oi-menu"></span> Menu
      </button>

      <div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item "><a href="../index.php" class="nav-link">Beranda</a></li>
          <li class="nav-item"><a href="../news.php" class="nav-link">News</a></li>
          <li class="nav-item"><a href="../event.php" class="nav-link">Event</a></li>
          <li class="nav-item cta"><a href="login-user-ticketing.php" class="nav-link"><span>Logint</span></a></li>
        </ul>
      </div>
    </div>
</nav>
<!-- END nav -->
    
<div class="hero-wrap js-fullheight" style="background-image: url('../images/bg_1.jpg');">
  <div class="overlay"></div>
  <div class="container">
    <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center" data-scrollax-parent="true">
      <div class="col-md-9 ftco-animate text-center" data-scrollax=" properties: { translateY: '70%' }">
        <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="login-user-ticketing.html">Home</a></span> <span>Login</span></p>
        <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Login</h1>
      </div>
    </div>
  </div>
</div>

<section class="ftco-section contact-section ftco-degree-bg">
  <div class="container">
    <h2>LOGIN USER</h2>
    <?php
    if (!empty($error_message)) {
        echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
    }
    ?>
    <div class="row block-9">
      <div class="col-md-6 pr-md-5">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <input type="text" class="form-control" name="Username" placeholder="Username" id="Username" required>
            <span class="error" id="UsernameError"></span>
          </div>
          <div class="form-group">
            <input type="password" class="form-control" name="Password" placeholder="Password" id="Password" required>
            <span class="error" id="PasswordError"></span>
          </div>
          <div>
            <input type="submit" value="Masuk" class="btn btn-primary py-3 px-5">
          </div>
          <div>
            <p>Don't have an account yet? <a href="registrasi-user-ticketing.php">Registration for free</a></p>
          </div>
        </form>
      </div>
      <div class="col-md-6" id="">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.024365105715!2d112.79302617357078!3d-7.2380598710860715!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9c4f53b233f%3A0x147a117a35d5f080!2sUPTD%20Taman%20Hiburan%20Pantai%20Kenjeran!5e0!3m2!1sid!2sid!4v1715595853762!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </div>
</section>

<!-- loader -->
<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.easing.1.3.js"></script>
    <script src="../js/jquery.waypoints.min.js"></script>
    <script src="../js/jquery.stellar.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../js/jquery.animateNumber.min.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/jquery.timepicker.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=false"></script>
    <script src="../js/scrollax.min.js"></script>
    <script src="../js/main.js"></script>

</body>
</html>
