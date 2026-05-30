<?php
session_start();
require_once 'src/Auth.php';
$auth = new App\Auth(__DIR__ . '/data/users.json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $user = $auth->login($_POST['email'], $_POST['password']);
        $_SESSION['user'] = $user; // Set Sesi Login
        if ($user['role'] == 'admin') header("Location: admin.php");
        else header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="text-center">Login</h4>
                        <?php if (isset($_GET['msg'])) echo "<div class='alert alert-success'>{$_GET['msg']}</div>"; ?>
                        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <form method="POST">
                            <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                            <button class="btn btn-success w-100">Masuk</button>
                        </form>
                        <p class="mt-3 text-center">Belum punya akun? <a href="register.php">Daftar</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>