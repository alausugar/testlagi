<?php
include "Connection.php";
session_start();

$totalUser = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_users"));
$access = mysqli_query($Conn, "SELECT * FROM tb_users WHERE nama = '$_SESSION[nama]'");
$accessResult = mysqli_fetch_array($access);
if ($accessResult['level'] == "Admin") {
    $deleteButton = "display: flex;";
    $idUserWidth = "width: 20%;";
} else if ($accessResult['level'] == "Supervisor") {
    $deleteButton = "display: none";
    $idUserWidth = "width: 10%;";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Register</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Register.css?v=<?php echo time(); ?>" />
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
                <a href="Dashboard.php">
                    <div class="markLine" style="display: none;"></div>Dashboard
                </a>
                <a href="Attendance.php">
                    <div class="markLine" style="display: none;"></div>Attendance
                </a>
                <a href="Equipment.php">
                    <div class="markLine" style="display: none;"></div>Equipment
                </a>
                <a href="Report.php" style="<?php echo $buttonAccess; ?>">
                    <div class="markLine" style="display: none;"></div>Report
                </a>
                <a href="Register.php" style="<?php echo $buttonAccess; ?>" class="Active">
                    <div class="markLine"></div>Register
                </a>
                <a href="Logout.php">Logout</a>
            </div>
            <div class="dropDown">
                <i class="bi bi-list"></i>
            </div>
        </div>
    </div>

    <div class="subHeader">
        <div class="subHeaderLeft">
            <p>How are you?</p>
            <p>
                <?php echo $_SESSION['nama']; ?>
            </p>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="register">
        <div class="formRegister" style="<?php echo $deleteButton; ?>">
            <div class="headerFormRegister">
                <p>Create Account</p>
                <p>Only for employee</p>
            </div>
            <div class="lineFormRegister"></div>
            <form class="contentFormRegister" method="POST" autocomplete="off">
                <div class="inputBox">
                    <input type="text" name="Name" id="namaInput" required />
                    <label>Full Name</label>
                </div>
                <div class="inputBox">
                    <input type="text" name="Jabatan" id="jabatanInput" required />
                    <label>Job Title</label>
                </div>
                <div class="inputBox">
                    <input type="text" name="Team" id="teamInput" required />
                    <label>Team</label>
                </div>
                <div class="inputBox">
                    <input type="text" name="Username" id="usernameInput" required />
                    <label>Username</label>
                </div>
                <div class="inputBox">
                    <input type="password" name="Password" id="Password" required />
                    <label>Password</label>
                </div>
                <p class="alertMessage">
                    <i class="bi bi-exclamation-circle-fill"></i>Minimum 6 Character!
                </p>
                <button name="Register" class="registerButton">REGISTER</button>
            </form>
            <?php
            include "Connection.php";
            $selectMax = mysqli_query($Conn, "SELECT MAX(id_user) as maxId_User FROM tb_users");
            $resultMax = mysqli_fetch_array($selectMax);
            $maxCode = $resultMax['maxId_User'];
            $no = (int) substr($maxCode, -3);
            $no++;
            $newCode = sprintf("141203%03s", $no);
            if (isset($_POST['Register'])) {
                $nama = $_POST['Name'];
                $jabatan = $_POST['Jabatan'];
                $team = $_POST['Team'];
                $username = $_POST['Username'];
                $password = $_POST['Password'];
                $Insert = mysqli_query($Conn, "INSERT INTO tb_users VALUES ('$newCode', '$nama', '$jabatan', '$team', 'Petugas', '$username', '$password')");
                echo "<meta http-equiv=refresh content=1;URL='Register.php'>";
            } ?>
        </div>

        <div class="tableUser">
            <div class="headerTableUser">
                <div class="headerTableUserLeft">
                    <p>Users</p>
                    <p>
                        <?php echo $totalUser; ?> Users
                    </p>
                </div>
                <div class="headerTableUserRight">
                    <div class="legend">
                        <p><i class="bi bi-circle-fill"></i>Attendance</p>
                        <p><i class="bi bi-circle-fill"></i>Equipment</p>
                    </div>
                    <div class="searchInput">
                        <svg class="icon" aria-hidden="true" viewBox="0 0 24 24">
                            <g>
                                <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 
                                4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 
                                11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"
                                    stroke="#93BFCF" stroke-width="1">
                                </path>
                            </g>
                        </svg>
                        <input type="text" id="searchInput" placeholder="Search name" onkeyup="searchFunction()">
                    </div>
                </div>
            </div>
            <div class="lineTableUser"></div>
            <div class="contentTableUser">
                <?php
                include "Connection.php";
                date_default_timezone_set('Asia/Jakarta');
                $userList = mysqli_query($Conn, "SELECT * FROM tb_users ORDER BY id_user ASC");

                while ($printData = mysqli_fetch_array($userList)) {
                    $id_user = $printData['id_user'];

                    $QueryHadir = mysqli_num_rows(mysqli_query($Conn, "SELECT status FROM tb_absen WHERE id_user = '$id_user' AND status = 'Hadir' AND keterangan = 'Sudah Validasi'"));
                    $QueryTotalAbsen = mysqli_num_rows(mysqli_query($Conn, "SELECT status FROM tb_absen WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $persentase_kehadiran = $QueryTotalAbsen > 0 ? number_format(($QueryHadir / $QueryTotalAbsen) * 100, 0) . '%' : 'N/A';

                    $QueryHelm = mysqli_num_rows(mysqli_query($Conn, "SELECT helm FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryHelmYes = mysqli_num_rows(mysqli_query($Conn, "SELECT helm FROM tb_apd WHERE helm = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryEyeglass = mysqli_num_rows(mysqli_query($Conn, "SELECT kacamata FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryEyeglassYes = mysqli_num_rows(mysqli_query($Conn, "SELECT kacamata FROM tb_apd WHERE kacamata = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryGloves = mysqli_num_rows(mysqli_query($Conn, "SELECT sarung_tangan FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryGlovesYes = mysqli_num_rows(mysqli_query($Conn, "SELECT sarung_tangan FROM tb_apd WHERE sarung_tangan = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryCard = mysqli_num_rows(mysqli_query($Conn, "SELECT id_card FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryCardYes = mysqli_num_rows(mysqli_query($Conn, "SELECT id_card FROM tb_apd WHERE id_card = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryVest = mysqli_num_rows(mysqli_query($Conn, "SELECT rompi FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryVestYes = mysqli_num_rows(mysqli_query($Conn, "SELECT rompi FROM tb_apd WHERE rompi = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryHarness = mysqli_num_rows(mysqli_query($Conn, "SELECT body_harness FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryHarnessYes = mysqli_num_rows(mysqli_query($Conn, "SELECT body_harness FROM tb_apd WHERE body_harness = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $QueryBoots = mysqli_num_rows(mysqli_query($Conn, "SELECT sepatu FROM tb_apd WHERE id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));
                    $QueryBootsYes = mysqli_num_rows(mysqli_query($Conn, "SELECT sepatu FROM tb_apd WHERE sepatu = 'Yes' AND id_user = '$id_user' AND keterangan = 'Sudah Validasi'"));

                    $Equipment = $QueryHelm + $QueryEyeglass + $QueryGloves + $QueryCard + $QueryVest + $QueryHarness + $QueryBoots;
                    $EquipmentYes = $QueryHelmYes + $QueryEyeglassYes + $QueryGlovesYes + $QueryCardYes + $QueryVestYes + $QueryHarnessYes + $QueryBootsYes;
                    $persentase_equipment = $Equipment > 0 ? number_format(($EquipmentYes / $Equipment) * 100, 0) . '%' : 'N/A';
                    ?>
                    <a href="Account.php?idUser=<?php echo $printData['id_user']; ?>" class="userBox"
                        dataName="<?php echo strtolower($printData['nama']); ?>">
                        <div class=" infoUser">
                            <p class="idUser" style="<?php echo $idUserWidth; ?>">
                                <?php echo $printData['id_user'] ?>
                            </p>
                            <p class="infoUserGap">|</p>
                            <div class="nameAndJob">
                                <p>
                                    <?php echo $printData['nama']; ?>
                                </p>
                                <p>
                                    <?php echo $printData['jabatan']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="userDetails">
                            <div class="userDetailsBox">
                                <p>
                                    <?php echo $printData['team']; ?>
                                </p>
                            </div>

                            <p class="gap">|</p>
                            <div class="userDetailsBox">
                                <p>
                                    <?php echo $printData['username']; ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="userDetailsBox">
                                <p>
                                    <?php echo isset($persentase_kehadiran) ? $persentase_kehadiran : 'N/A'; ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="userDetailsBox">
                                <p>
                                    <?php echo isset($persentase_equipment) ? $persentase_equipment : 'N/A'; ?>
                                </p>
                            </div>
                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // SCRIPT SEARCH
        function searchFunction() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const attendanceBoxes = document.querySelectorAll('.userBox');

            attendanceBoxes.forEach(item => {
                const itemNama = item.getAttribute('dataName');
                item.style.display = itemNama.includes(searchInput) ? 'flex' : 'none';
            });
        }

        // SCRIPT FOR UPDATE BORDER COLOR
        var namaInput = document.getElementById('namaInput');
        var jabatanInput = document.getElementById('jabatanInput');
        var teamInput = document.getElementById('teamInput');
        var usernameInput = document.getElementById('usernameInput');
        var passwordInput = document.getElementById('Password');

        namaInput.addEventListener("input", function () {
            updateBorderColor(namaInput);
        });
        namaInput.addEventListener("focus", function () {
            namaInput.style.borderColor = "#252525";
        });
        namaInput.addEventListener("blur", function () {
            updateBorderColor(namaInput);
        });

        jabatanInput.addEventListener("input", function () {
            updateBorderColor(jabatanInput);
        });
        jabatanInput.addEventListener("focus", function () {
            jabatanInput.style.borderColor = "#252525";
        });
        jabatanInput.addEventListener("blur", function () {
            updateBorderColor(jabatanInput);
        });

        teamInput.addEventListener("input", function () {
            updateBorderColor(teamInput);
        });
        teamInput.addEventListener("focus", function () {
            teamInput.style.borderColor = "#252525";
        });
        teamInput.addEventListener("blur", function () {
            updateBorderColor(teamInput);
        });

        passwordInput.addEventListener("input", function () {
            updateBorderColor(passwordInput);
        });
        passwordInput.addEventListener("focus", function () {
            passwordInput.style.borderColor = "#252525";
        });
        passwordInput.addEventListener("blur", function () {
            updateBorderColor(passwordInput);
        });

        usernameInput.addEventListener("input", function () {
            updateBorderColor(usernameInput);
        });
        function updateBorderColor(input) {
            var inputValue = input.value;
            var inputLength = inputValue.length;
            if (inputLength === 0 && !input.matches(':focus')) {
                input.style.borderColor = "#ECEDEC";
            } else if (inputLength > 0 || input.matches(':focus')) {
                input.style.borderColor = "#252525";
            }
        }

        // SCRIP VALIDATION PASSWORD INPUT
        var inputPassword = document.getElementById("Password");
        var registerButton = document.querySelector('.registerButton');
        var AlertPassword = document.querySelector('.alertMessage');
        inputPassword.addEventListener("input", function () {
            var inputValue = inputPassword.value;
            var inputLength = inputValue.length;
            if (inputLength === 0 && !inputPassword.matches(':focus')) {
                inputPassword.style.borderColor = "#ECEDEC";
                registerButton.disabled = true;
                AlertPassword.style.opacity = "0";
            } else if (inputLength === 0) {
                inputPassword.style.borderColor = "#252525";
                registerButton.disabled = true;
                AlertPassword.style.opacity = "0";
            } else if (inputLength < 8) {
                inputPassword.style.borderColor = "#DA0605";
                registerButton.disabled = true;
                AlertPassword.style.opacity = "1";
            } else {
                inputPassword.style.borderColor = "#27862A";
                registerButton.disabled = false;
                AlertPassword.style.opacity = "0";
            }
        });

        // SCRIPT RESPONSIVE
        dropdown = document.querySelector(".dropDown");
        navbarRight = document.querySelector(".navbarRight");
        dropdown.onclick = function () {
            navbarRight.classList.toggle("Active");
        };
    </script>
</body>

</html>