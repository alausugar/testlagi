<?php
// INSPECT PROJECT
// BY : DINDA DWI SAFITRI, M. HERDIN RIWANTO, RIVALDO NIZAMI

include "Connection.php";
session_start();
$Username = '';
$Password = '';
$alertMessage = '';
if (isset($_POST['Login'])) {
    echo '<script>showLoader();</script>';
    usleep(500000);
    $Username = mysqli_escape_string($Conn, $_POST['Username']);
    $Password = mysqli_escape_string($Conn, $_POST['Password']);
    $SearchUser = mysqli_query($Conn, "SELECT * FROM tb_users WHERE username = '$Username' AND password = '$Password'");

    if (mysqli_num_rows($SearchUser) > 0) {
        $Session = mysqli_fetch_array($SearchUser);
        $_SESSION['nama'] = $Session['nama'];
        $_SESSION['team'] = $Session['team'];
        $_SESSION['level'] = $Session['level'];
        $_SESSION['id_user'] = $Session['id_user'];
        $_SESSION['jabatan'] = $Session['jabatan'];
        $_SESSION['username'] = $Session['username'];
        header("Location: Dashboard.php");
        exit();
    } else {
        $alertMessage = "Please check your Username or Password!";
    }
    echo '<script>hideLoader();</script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Login</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Login.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
    </style>
    <script>
        // SCRIPT 1
        function preventBack() {
            window.history.forward()
        };
        setTimeout("preventBack()", 0);
        window.onunload = function () { null; }
        // SCRIPT 2
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="navbar">
            <div class="navbarLeft">
                <p>Inspect</p>
            </div>
            <div class="navbarRight">

            </div>
        </div>
    </div>

    <div class="loginPage">
        <div class="loginPageLeft">
            <div class="contentLoginPageLeft">
                <p>Welcome to,</p>
                <p>Inspect</p>
                <p>Accidents are not scheduled, stay safety.</p>
            </div>
        </div>
        <div class="loginPageRight">
            <div class="contentLoginPageRight">
                <div class="headerFormLogin">
                    <p>Login Account</p>
                    <div class="lineHeaderForm"></div>
                    <p>Please Log in using the registered account,
                        if you don't have an account please contact Admin/Helpdesk</p>
                </div>
                <form class="contentFormLogin" method="POST" autocomplete="off">
                    <div class="inputBox">
                        <input type="text" name="Username" required />
                        <label>Username</label>
                    </div>
                    <div class="inputBox">
                        <input type="password" name="Password" required />
                        <label>Password</label>
                    </div>
                    <p class="alertMessage" style="<?php echo !empty($alertMessage) ? 'animation: fadeIn 4s;' : ''; ?>">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?php echo $alertMessage; ?>
                    </p>
                    <button class="loginButton" name="Login" onclick="showLoader()">LOGIN</button>
                    <div class="loader" id="loader" style="<?php echo $loader; ?>"></div>
                </form>
            </div>
            <div class="footerLoginPage">
                <p>Inspect &copy 2024. All right reserved.</p>
            </div>
        </div>
    </div>
    <script>
        function showLoader() {
            document.getElementById('loader').style.display = 'flex';
        }
        function hideLoader() {
            document.getElementById('loader').style.display = 'none';
        }
    </script>
</body>

</html>