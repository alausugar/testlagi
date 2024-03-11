<?php
include "Connection.php";
session_start();
$id_user = $_SESSION['id_user'];
date_default_timezone_set('Asia/Jakarta');

$access = mysqli_query($Conn, "SELECT * FROM tb_users WHERE nama = '$_SESSION[nama]'");
$accessResult = mysqli_fetch_array($access);
if ($accessResult['level'] == "Admin" || $accessResult['level'] == "Supervisor") {
    $registerAccess = "display: flex;";
    $reportAccess = "display: flex";
    $formAttendance = "display: none";
} else if ($accessResult['level'] == "K3L") {
    $registerAccess = "display: none;";
    $reportAccess = "display: flex";
    $formAttendance = "display: none";
} else {
    $registerAccess = "display: none";
    $reportAccess = "display: none";
    $formAttendance = "display: flex";
}

$limitAttendance = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$id_user' AND tanggal = CURDATE() AND keterangan IN ('Sudah Validasi', 'Belum Validasi')"));
if ($limitAttendance > 0) {
    $attendanceButton = "display: none;";
    $message = "display: flex;";
} else {
    $attendanceButton = "display: block;";
    $message = "display: none;";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Attendance</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Attendance.css?v=<?php echo time(); ?>" />
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
        // SCRIPT CLEAR
        function clearDateInputs() {
            document.querySelector('input[name="tanggalAwal"]').value = '';
            document.querySelector('input[name="tanggalAkhir"]').value = '';
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
                <a href="Attendance.php" class="Active">
                    <div class="markLine"></div>Attendance
                </a>
                <a href="Equipment.php">
                    <div class="markLine" style="display: none;"></div>Equipment
                </a>
                <a href="Report.php" style="<?php echo $reportAccess; ?>">
                    <div class="markLine" style="display: none;"></div>Report
                </a>
                <a href="Register.php" style="<?php echo $registerAccess; ?>">
                    <div class="markLine" style="display: none;"></div>Register
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
        <div class="subHeaderRight">
            <form action="ExcelAbsen.php" style="<?php echo $registerAccess; ?>" method="GET">
                <input type="hidden" name="tanggalAwal" value="<?php echo $_GET["tanggalAwal"] ?? ''; ?>">
                <input type="hidden" name="tanggalAkhir" value="<?php echo $_GET["tanggalAkhir"] ?? ''; ?>">
                <button type="submit"><i class="bi bi-file-earmark-spreadsheet-fill"></i>Export</button>
            </form>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="attendance">
        <div class="formAttendance" style="<?php echo $formAttendance; ?>">
            <div class="headerFormAttendance">
                <p>Attendance</p>
                <p>
                    <?php echo date("M, d"); ?>
                </p>
            </div>
            <div class="lineFormAttendance"></div>
            <form class="contentFormAttendance" method="POST" autocomplete="off">
                <div class="dataBox">
                    <input type="text" name="nama" value="<?php echo $_SESSION['nama']; ?>" disabled />
                    <label>Full Name</label>
                </div>
                <div class="dataBox">
                    <input type="text" name="jabatan" value="<?php echo $_SESSION['jabatan']; ?>" disabled />
                    <label>Job Title</label>
                </div>
                <div class="dataBox">
                    <input type="text" name="team" value="<?php echo $_SESSION['team']; ?>" disabled />
                    <label>Team</label>
                </div>
                <div class="choiceChipBox">
                    <div class="choiceChip">
                        <input type="radio" id="Present" name="Choice" value="Hadir">
                        <label for="Present">
                            <p>Present</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="radio" id="Sick" name="Choice" value="Sakit">
                        <label for="Sick">
                            <p>Sick</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="radio" id="Permission" name="Choice" value="Izin">
                        <label for="Permission">
                            <p>Permission</p>
                        </label>
                    </div>
                </div>
                <p class="alertMessage" style="display: none;">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Please select your attendance status
                </p>
                <button class="attendanceButton" type="submit" name="Attendance"
                    style="<?php echo $attendanceButton; ?>">SUBMIT</button>
                <p class="message" style="<?php echo $message; ?>"><i class="bi bi-check-circle-fill"></i>You've done
                    your attendance today. Work spirit!</p>
            </form>
        </div>

        <div class="tableAttendance">
            <div class="headerTableAttendance">
                <div class="headerTableAttendanceLeft">
                    <p>Attendance History</p>
                    <div class="rangeDate">
                        <div class="rangeDate">
                            <p>
                                <?php
                                $startDate = null;
                                $endDate = null;
                                if (isset($_GET['tanggalAwal']) && isset($_GET['tanggalAkhir'])) {
                                    $startDate = $_GET['tanggalAwal'];
                                    $endDate = $_GET['tanggalAkhir'];
                                }
                                if ($startDate && $endDate) {
                                    echo date("d M, Y", strtotime($startDate)) . " - " . date("d M, Y", strtotime($endDate));
                                } else {
                                    echo "All Data";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <form class="headerTableAttendanceRight" method="GET">
                    <div class="inputRange">
                        <label>From</label>
                        <input type="date" name="tanggalAwal" value="<?php echo $_GET["tanggalAwal"] ?? ''; ?>">
                    </div>
                    <div class="inputRange">
                        <label>To</label>
                        <input type="date" name="tanggalAkhir" value="<?php echo $_GET["tanggalAkhir"] ?? ''; ?>">
                    </div>
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="lineTableAttendance"></div>
            <div class="contentTableAttendance">
                <?php
                include "Connection.php";
                $historyAttendance = "SELECT * FROM tb_users, tb_absen WHERE tb_users.id_user = tb_absen.id_user AND keterangan = 'Sudah Validasi'";

                $startDate = $_GET["tanggalAwal"] ?? null;
                $endDate = $_GET["tanggalAkhir"] ?? null;
                if ($startDate && $endDate) {
                    $startDate = mysqli_real_escape_string($Conn, $startDate);
                    $endDate = mysqli_real_escape_string($Conn, $endDate);
                    $historyAttendance .= " AND tb_absen.tanggal BETWEEN '$startDate' AND '$endDate'";
                }
                $historyAttendance .= " ORDER BY tb_absen.tanggal DESC";

                $resultHistoryAttendance = mysqli_query($Conn, $historyAttendance);
                while ($printData = mysqli_fetch_array($resultHistoryAttendance)) {
                    if ($printData['status'] == 'Hadir') {
                        $Color = "color: #60A578; background-color: #D4EADE;";
                        $Status = "Present";
                    } else if ($printData['status'] == 'Sakit') {
                        $Color = "color: #368687; background-color: #C5E7E8;";
                        $Status = "Sick";
                    } else if ($printData['status'] == 'Izin') {
                        $Color = "color: #A26840; background-color: #F9C7A4;";
                        $Status = "Permission";
                    } else if ($printData['status'] == 'Alpa') {
                        $Color = "color: #92344C; background-color: #FFC3CF;";
                        $Status = "Alpha";
                    }
                    ?>
                    <div class="attendanceBox">
                        <div class=" infoUser">
                            <p>
                                <?php echo $printData['nama']; ?>
                            </p>
                            <p>
                                <?php echo $printData['jabatan']; ?>
                            </p>
                        </div>
                        <div class="attendanceActivity">
                            <div class="attendanceActivityBox">
                                <p>
                                    <?php echo date("M, d", strtotime($printData['tanggal'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="attendanceActivityBox">
                                <p>
                                    <?php echo date("H.i A", strtotime($printData['jam'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="attendanceActivityBox">
                                <p class="status" style="<?php echo $Color; ?>">
                                    <?php echo $Status; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>


            <?php
            include "Connection.php";
            date_default_timezone_set('Asia/Jakarta');
            $tanggal = date("Y/m/d");
            $jam = date("H:i:s");
            $id_user = $_SESSION['id_user'];
            $selectMax = mysqli_query($Conn, "SELECT MAX(id_absen) as maxId_Absen FROM tb_absen");
            $resultMax = mysqli_fetch_array($selectMax);
            $maxCode = $resultMax['maxId_Absen'];
            $no = (int) substr($maxCode, -3);
            $no++;
            $newCode = sprintf("215%03s", $no);
            if (isset($_POST['Attendance'])) {
                $Status = $_POST['Choice'];
                $Insert = mysqli_query($Conn, "INSERT INTO tb_absen (id_absen, id_user, tanggal, jam, status, keterangan) 
        VALUES ('$newCode', '$id_user', '$tanggal', '$jam', '$Status', 'Belum Validasi')");
                echo "<meta http-equiv=refresh content=1;URL='Attendance.php'>";
            }

            $hari = date("N", strtotime($tanggal));

            if ($hari >= 1 && $hari <= 5) {
                $jamLimit = strtotime("09:30:00");
                $jamAbsen = strtotime($jam);

                if ($jamAbsen > $jamLimit) {
                    $Employee = mysqli_query($Conn, "SELECT id_user FROM tb_users WHERE level = 'Petugas'");
                    while ($rowUser = mysqli_fetch_assoc($Employee)) {
                        $currentUser = $rowUser['id_user'];
                        $checkUserAttendance = mysqli_query($Conn, "SELECT COUNT(*) as countAbsen FROM tb_absen WHERE id_user = '$currentUser' AND tanggal = '$tanggal'");
                        $resultCekUserAttendance = mysqli_fetch_array($checkUserAttendance);
                        $countUserAttendance = $resultCekUserAttendance['countAbsen'];
                        if ($countUserAttendance == 0) {
                            $no++;
                            $newCode = sprintf("215%03s", $no);
                            $Insert = mysqli_query($Conn, "INSERT INTO tb_absen (id_absen, id_user, tanggal, jam, status, keterangan) 
                    VALUES ('$newCode', '$currentUser', '$tanggal', '$jam', 'Alpa', 'Belum Validasi')");
                            echo "<meta http-equiv=refresh content=1;URL='Attendance.php'>";
                        }
                    }
                }
            } ?>
            <script>
                // SCRIPT RESPONSIVE
                dropdown = document.querySelector(".dropDown");
                navbarRight = document.querySelector(".navbarRight");
                dropdown.onclick = function () {
                    navbarRight.classList.toggle("Active");
                };

                // SCRIPT FOR BUTTON ATTENDANCE
                const radioButtons = document.querySelectorAll('input[name="Choice"]');
                const attendanceButton = document.querySelector('.attendanceButton');
                const alertMessage = document.querySelector('.alertMessage');

                attendanceButton.addEventListener('click', function (event) {
                    let isSelected = false;
                    radioButtons.forEach(function (radioButton) {
                        if (radioButton.checked) {
                            isSelected = true;
                        }
                    });
                    if (!isSelected) {
                        event.preventDefault();
                        alertMessage.style.display = "flex";
                    } else {
                        alertMessage.style.display = "none";
                    }
                });
                radioButtons.forEach(function (radioButton) {
                    radioButton.addEventListener('change', function () {
                        alertMessage.style.display = "none";
                    });
                });
            </script>
</body>

</html>