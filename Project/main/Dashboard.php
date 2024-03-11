<?php
include "Connection.php";
session_start();
$id_user = $_SESSION['id_user'];
$jumlahAlpa = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_users, tb_absen WHERE tb_users.id_user = tb_absen.id_user 
AND tb_users.id_user = '$id_user' AND status = 'Alpa' AND keterangan = 'Sudah Validasi'"));

$totalApdLengkap = 0;
$totalApdTidakLengkap = 0;
$historyEquipment = "SELECT * FROM tb_users, tb_apd WHERE tb_users.id_user = tb_apd.id_user AND keterangan = 'Sudah Validasi' AND tb_users.id_user = '$id_user'";
$resultHistoryEquipment = mysqli_query($Conn, $historyEquipment);
while ($printData = mysqli_fetch_array($resultHistoryEquipment)) {
    $totalHelm = ($printData['helm'] == 'Yes') ? 1 : 0;
    $totalVest = ($printData['rompi'] == 'Yes') ? 1 : 0;
    $totalBoots = ($printData['sepatu'] == 'Yes') ? 1 : 0;
    $totalCard = ($printData['id_card'] == 'Yes') ? 1 : 0;
    $totalEyeglass = ($printData['kacamata'] == 'Yes') ? 1 : 0;
    $totalHarness = ($printData['body_harness'] == 'Yes') ? 1 : 0;
    $totalGloves = ($printData['sarung_tangan'] == 'Yes') ? 1 : 0;
    $totalAPD = ($totalHelm + $totalVest + $totalBoots + $totalCard + $totalEyeglass + $totalHarness + $totalGloves);
    if ($totalAPD <= 6) {
        $totalApdTidakLengkap++;
    } else {
        $totalApdLengkap++;
    }
}

$popupMessage = "";

if ($jumlahAlpa >= 3) {
    $text = "juga";
    $popupMessage .= "Saudara diketahui tidak hadir sebanyak " . $jumlahAlpa . " kali tanpa pemberitahuan maupun izin tertulis kepada perusahaan. ";
} else {
    $text = "";
}

if ($totalApdTidakLengkap >= 3) {
    $popupMessage .= "Saudara " . $text . " diketahui tidak menggunakan APD secara lengkap sebanyak " . $totalApdTidakLengkap . " kali.";
}

if (!empty($popupMessage)) {
    $notif = "display: flex";
} else {
    $notif = "display: none";
}

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

$totalEmployee = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_users WHERE level = 'Petugas'"));
$attendanceToday = 0;
$absentToday = 0;
$query = "SELECT status, COUNT(*) as count FROM tb_absen WHERE status = 'Hadir' AND tanggal = CURDATE() AND keterangan = 'Sudah Validasi' UNION 
        SELECT status, COUNT(*) as count FROM tb_absen WHERE status IN ('Sakit', 'Izin', 'Alpa') AND tanggal = CURDATE() AND keterangan = 'Sudah Validasi'";
$result = mysqli_query($Conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['status'] == 'Hadir') {
        $attendanceToday = $row['count'];
    } elseif (in_array($row['status'], ['Sakit', 'Izin', 'Alpa'])) {
        $absentToday += $row['count'];
    }
}
function getPercentage($Conn, $columnName)
{
    $queryTotal = mysqli_num_rows(mysqli_query($Conn, "SELECT $columnName FROM tb_apd WHERE keterangan = 'Sudah Validasi'"));
    $queryYes = mysqli_num_rows(mysqli_query($Conn, "SELECT $columnName FROM tb_apd WHERE $columnName = 'Yes' AND keterangan = 'Sudah Validasi'"));
    return number_format(($queryYes / $queryTotal) * 100, 1) . '%';
}
$equipmentColumns = ['helm', 'kacamata', 'sarung_tangan', 'id_card', 'rompi', 'body_harness', 'sepatu'];
$percentages = [];

foreach ($equipmentColumns as $column) {
    $percentages[$column] = getPercentage($Conn, $column);
}

$equipmentTotal = array_sum(array_map(function ($column) use ($Conn) {
    return mysqli_num_rows(mysqli_query($Conn, "SELECT $column FROM tb_apd WHERE keterangan = 'Sudah Validasi'"));
}, $equipmentColumns));

$equipmentYesTotal = array_sum(array_map(function ($column) use ($Conn) {
    return mysqli_num_rows(mysqli_query($Conn, "SELECT $column FROM tb_apd WHERE $column = 'Yes' AND keterangan = 'Sudah Validasi'"));
}, $equipmentColumns));

$totalEquipment = number_format(($equipmentYesTotal / $equipmentTotal) * 100, 0);
$percentageEquipmentTotal = number_format(($equipmentYesTotal / $equipmentTotal) * 100, 0) . '%';
if ($totalEquipment == 100) {
    $styleStatus = "color: #60A578; background-color: #D4EADE;;";
    $textStatus = "Very Good";
} else if ($totalEquipment >= 92) {
    $styleStatus = "color: #A26840; background-color: #F9C7A4;";
    $textStatus = "Good";
} else if ($totalEquipment >= 87) {
    $styleStatus = "color: #92344C; background-color: #FFC3CF;";
    $textStatus = "Bad";
} else {
    $styleStatus = "color: #92344C; background-color: #FFC3CF;";
    $textStatus = "Bad";
}

// DATA SETUP
$data = array();
for ($month = 1; $month <= 12; $month++) {
    $totalAttendanceQuery = mysqli_fetch_assoc(mysqli_query($Conn, "SELECT COUNT(*) as total_attendance FROM tb_absen WHERE MONTH(tanggal) = $month AND keterangan = 'Sudah Validasi'"));
    $totalAttendance = $totalAttendanceQuery['total_attendance'];

    $totalPresentQuery = mysqli_fetch_assoc(mysqli_query($Conn, "SELECT COUNT(*) as total_present FROM tb_absen WHERE MONTH(tanggal) = $month AND status = 'Hadir' AND keterangan = 'Sudah Validasi'"));
    $totalPresent = $totalPresentQuery['total_present'];
    $totalAbsent = $totalAttendance - $totalPresent;

    $data[] = array(
        'bulan' => substr(date("M", mktime(0, 0, 0, $month, 1)), 0, 3),
        'total_attendance' => $totalAttendance,
        'total_present' => $totalPresent,
        'total_absent' => $totalAbsent
    );
}

$Time = gmdate("H:i", time() + 7 * 3600); // SET TIME FOR GREETING
$T = explode(":", $Time);
$Hours = $T[0];
$Second = $T[1];
if ($Hours >= 00 and $Hours < 11) {
    if ($Second >= 00 and $Second < 60) {
        $Greet = "Good Morning";
    }
} else if ($Hours >= 11 and $Hours < 18) {
    if ($Second >= 00 and $Second < 60) {
        $Greet = "Good Afternoon";
    }
} else if ($Hours >= 18 and $Hours <= 24) {
    if ($Second >= 00 and $Second < 60) {
        $Greet = "Good Night";
    }
} else {
    $Greet = "Error";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Dashboard</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Dashboard.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
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
                <a href="Dashboard.php" class="Active">
                    <div class="markLine"></div>Dashboard
                </a>
                <a href="Attendance.php">
                    <div class="markLine" style="display: none;"></div>Attendance
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
            <p>Hello,
                <?php echo $Greet; ?>
            </p>
            <p>
                <?php echo $_SESSION['nama']; ?>
            </p>
        </div>
        <div class="subHeaderRight">
            <a href="Register.php" style="<?php echo $registerAccess; ?>"><i class="bi bi-person-fill-add"></i> Add
                Employee</a>
            <div class="notif" style="<?php echo $notif; ?>">
                <button onclick="openPopup()"><i class="bi bi-envelope-exclamation"
                        style="<?php echo $formAttendance; ?>"></i></button>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="Popup" id="Popup">
        <button class="btnClose" onclick="closePopup()">&times</button>
        <div class="PopupHeader">
            <img src="Image/Icon.png">
            <div class="copMail">
                <p>PT. PLN (Persero)</p>
                <p>ULP Sukarami</p>
            </div>
        </div>
        <div class="linePopup"></div>
        <div class="PopupContent">
            <p>SURAT PERINGATAN KE-SATU (SP 1)</p>
            <p>Nomor : SP1 / 12 /
                <?php echo date("Ym"); ?>
            </p>
            <p>Surat peringatan ini ditujukan kepada Saudara
                <?php echo $_SESSION['nama']; ?> dikarenakan sikap indisipliner dan
                pelanggaran terhadap tata tertib perusahaan yang saudara lakukan.
                <?php echo $popupMessage; ?>
                <br><br>
                Agar Saudara dapat merubah sikap dan bertindak secara profesional lagi, maka perusahaan memberikan
                sanksi sebagai berikut.
                <br>
                • &nbsp &nbspPemotongan gaji sebesar 10% selama 1 bulan
                <br>
                • &nbsp &nbspDilarang menggunakan inventaris perusahaan berupa kendaraan
                <br><br>
                Apabila teguran SP 1 ini tidak direspon dengan baik, maka perusahaan akan mengeluarkan SP 2 yang berarti
                bersifat pemecatan.
                <br><br>
                Demikian surat peringatan ini diterbitkan agar dapat ditaati sebagaimana semestinya. Diharapkan Saudara
                <?php echo $_SESSION['nama']; ?> dapat merubah sikap dan mampu menunjukkan kinerja yang profesional.
            </p>
            <div class="signature">
                <div class="signatureLeft">
                    <p>Hormat Kami,</p>
                    <p>Team Leader K3L</p>
                    <img src="Image/TTD_SPV.png" width="80px">
                    <p>Rizki Junito</p>
                </div>
                <div class="signatureRight">
                    <p>Mengetahui,</p>
                    <p>Manager ULP Sukarami</p>
                    <img src="Image/TTD_MNG.png" width="80px">
                    <p>Agus Ibnu Tsani</p>
                </div>
            </div>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="courses">
        <div class="cardCourses">
            <div class="cardCoursesTop">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="cardCoursesBottom">
                <p>Total Employee</p>
                <p id="totalEmployee">0</p>
            </div>
        </div>
        <div class="cardCourses">
            <div class="cardCoursesTop">
                <i class="bi bi-clipboard2-check-fill"></i>
            </div>
            <div class="cardCoursesBottom">
                <p>Attendance Today</p>
                <p id="attendancePercentage">
                    <?php echo number_format(($attendanceToday / $totalEmployee) * 100, 0); ?>%
                </p>
            </div>
        </div>
        <div class="cardCourses">
            <div class="cardCoursesTop">
                <i class="bi bi-clipboard2-x-fill"></i>
            </div>
            <div class="cardCoursesBottom">
                <p>Absent Today</p>
                <p id="absentToday">
                    <?php echo number_format(($absentToday / $totalEmployee) * 100, 0) . '%'; ?>
                </p>
            </div>
        </div>
        <div class="cardCourses">
            <div class="cardCoursesTop">
                <i class="bi bi-bar-chart-fill"></i>
                <p style="<?php echo $styleStatus; ?>">
                    <?php echo $textStatus; ?>
                </p>
            </div>
            <div class="cardCoursesBottom">
                <p>Equipment Completeness</p>
                <p id="equipmentPercentage">
                    <?php echo $percentageEquipmentTotal; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="statistic">
        <div class="overview">
            <div class="headerOverview">
                <div class="headerOverviewLeft">
                    <p>Overview</p>
                    <p>
                        <?php echo date("Y"); ?>
                    </p>
                </div>
                <div class="headerOverviewRight">
                    <p><i class="bi bi-circle-fill"></i>Attendance</p>
                    <p><i class="bi bi-circle-fill"></i>Absent</p>
                </div>
            </div>
            <div class="contentOverview">
                <?php
                foreach ($data as $item) {
                    $month = $item['bulan'];
                    $totalAttendance = $item['total_attendance'];
                    $totalPresent = $item['total_present'];
                    $totalAbsent = $item['total_absent'];

                    $percentagePresent = ($totalAttendance != 0) ? number_format(($totalPresent / $totalAttendance) * 100, 1) . '%' : '0%';
                    $percentageAbsent = ($totalAttendance != 0) ? number_format(($totalAbsent / $totalAttendance) * 100, 1) . '%' : '0%';
                    ?>
                    <div class="wrapper">
                        <div class="backgroundBar">
                            <div class="barAbsent animatedBar" style="height: <?php echo $percentageAbsent; ?>;"></div>
                            <div class="barAttendance animatedBar" style="height: <?php echo $percentagePresent; ?>;"></div>
                        </div>
                        <p>
                            <?php echo $month; ?>
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="activity">
            <div class="headerActivity">
                <p>Recent Activity</p>
            </div>
            <div class="lineActivity"></div>
            <div class="contentActivity">
                <?php
                include "Connection.php";
                date_default_timezone_set('Asia/Jakarta');
                $currentTime = date("H:i:s");
                $startTime = "00:00:00";
                $endTime = "23:59:00";
                $recentAttendance = mysqli_query($Conn, "SELECT tb_users.id_user, tb_users.nama, tb_users.jabatan, tb_absen.jam, tb_absen.keterangan 
                FROM tb_users INNER JOIN tb_absen ON tb_users.id_user = tb_absen.id_user WHERE tb_absen.tanggal = CURDATE() AND tb_absen.jam 
                BETWEEN '$startTime' AND '$endTime' ORDER BY tb_absen.jam DESC");
                $recentEquipment = mysqli_query($Conn, "SELECT tb_users.id_user, tb_users.nama, tb_users.jabatan, tb_apd.jam, tb_apd.keterangan 
                FROM tb_users INNER JOIN tb_apd ON tb_users.id_user = tb_apd.id_user WHERE tb_apd.tanggal = CURDATE() AND tb_apd.jam 
                BETWEEN '$startTime' AND '$endTime' ORDER BY tb_apd.jam DESC");
                $result = array();
                while ($Print = mysqli_fetch_assoc($recentAttendance)) {
                    $Print['activity'] = "Attendance";
                    if ($Print > 0) {
                        $nothingActivity = "display: none;";
                    } else {
                        $nothingActivity = "display: flex;";
                    }
                    if ($Print['keterangan'] == 'Belum Validasi') {
                        $Print['color'] = "color: #A26840; background-color: #F9C7A4;";
                        $Print['status'] = "Pending";
                    } else if ($Print['keterangan'] == 'Sudah Validasi') {
                        $Print['color'] = "color: #60A578; background-color: #D4EADE;";
                        $Print['status'] = "Valid";
                    } else if ($Print['keterangan'] == 'Tidak Validasi') {
                        $Print['color'] = "color: #92344C; background-color: #FFC3CF;";
                        $Print['status'] = "Invalid";
                    }
                    $result[] = $Print;
                }
                while ($Print = mysqli_fetch_assoc($recentEquipment)) {
                    $Print['activity'] = "Equipment";
                    if ($Print > 0) {
                        $nothingActivity = "display: none;";
                    } else {
                        $nothingActivity = "display: flex;";
                    }
                    if ($Print['keterangan'] == 'Belum Validasi') {
                        $Print['color'] = "color: #A26840; background-color: #F9C7A4;";
                        $Print['status'] = "Pending";
                    } else if ($Print['keterangan'] == 'Sudah Validasi') {
                        $Print['color'] = "color: #60A578; background-color: #D4EADE;";
                        $Print['status'] = "Valid";
                    } else if ($Print['keterangan'] == 'Tidak Validasi') {
                        $Print['color'] = "color: #92344C; background-color: #FFC3CF;";
                        $Print['status'] = "Invalid";
                    }
                    $result[] = $Print;
                }
                usort($result, function ($a, $b) {
                    return strtotime($b['jam']) - strtotime($a['jam']);
                });
                foreach ($result as $Print) {
                    ?>
                    <div class="recentBox">
                        <div class="infoUser">
                            <p>
                                <?php echo $Print['nama']; ?>
                            </p>
                            <p>
                                <?php echo $Print['jabatan']; ?>
                            </p>
                        </div>
                        <div class="recentActivity">
                            <div class="recentActivityBox">
                                <p>
                                    <?php echo $Print['activity']; ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="recentActivityBox">
                                <p>
                                    <?php echo date("H.i A", strtotime($Print['jam'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="recentActivityBox">
                                <p style="<?php echo $Print['color']; ?>">
                                    <?php echo $Print['status']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
                <p class="nothingActivity" style="<?php echo $nothingActivity; ?>">There is no activity today</p>
            </div>
        </div>
    </div>

    <script>
        // SCRIPT FOR ANIMATE TOTAL EMPLOYEE
        document.addEventListener('DOMContentLoaded', function () {
            var totalEmployeeElement = document.getElementById('totalEmployee');
            var totalEmployee = <?php echo $totalEmployee; ?>;
            animateText(totalEmployeeElement, totalEmployee, 1000);
            function animateText(element, targetValue, duration) {
                var currentValue = 0;
                var startTime = null;
                function updateText(timestamp) {
                    if (!startTime) startTime = timestamp;
                    var progress = timestamp - startTime;
                    var percentage = Math.min(progress / duration, 1);
                    currentValue = Math.floor(percentage * targetValue);
                    element.innerText = currentValue;
                    if (percentage < 1) {
                        requestAnimationFrame(updateText);
                    }
                }
                requestAnimationFrame(updateText);
            }
        });

        // SCRIPT FOR ANIMATE ATTENDANCE TODAY
        document.addEventListener('DOMContentLoaded', function () {
            var attendancePercentageElement = document.getElementById('attendancePercentage');
            var initialPercentage = 0;
            var targetPercentage = <?php echo number_format(($attendanceToday / $totalEmployee) * 100, 0); ?>;
            var duration = 1000;
            animateText(attendancePercentageElement, initialPercentage, targetPercentage, duration);
            function animateText(element, startValue, targetValue, duration) {
                var startTime = null;
                function updateText(timestamp) {
                    if (!startTime) startTime = timestamp;
                    var progress = timestamp - startTime;
                    var percentage = Math.min(progress / duration, 1);
                    var currentValue = Math.floor((percentage * (targetValue - startValue)) + startValue);
                    element.innerText = currentValue + '%';
                    if (percentage < 1) {
                        requestAnimationFrame(updateText);
                    }
                }
                requestAnimationFrame(updateText);
            }
        });

        // SCRIPT FOR ANIMATE ABSENT TODAY
        document.addEventListener('DOMContentLoaded', function () {
            var attendancePercentageElement = document.getElementById('absentToday');
            var initialPercentage = 0;
            var targetPercentage = <?php echo number_format(($absentToday / $totalEmployee) * 100, 0); ?>;
            var duration = 1000;
            animateText(attendancePercentageElement, initialPercentage, targetPercentage, duration);
            function animateText(element, startValue, targetValue, duration) {
                var startTime = null;
                function updateText(timestamp) {
                    if (!startTime) startTime = timestamp;
                    var progress = timestamp - startTime;
                    var percentage = Math.min(progress / duration, 1);
                    var currentValue = Math.floor((percentage * (targetValue - startValue)) + startValue);
                    element.innerText = currentValue + '%';
                    if (percentage < 1) {
                        requestAnimationFrame(updateText);
                    }
                }
                requestAnimationFrame(updateText);
            }
        });

        // SCRIPT FOR ANIMATE EQUIPMENT PERCENTAGE
        $(document).ready(function () {
            var equipmentPercentageElement = $('#equipmentPercentage');
            var targetPercentage = <?php echo str_replace('%', '', $percentageEquipmentTotal); ?>;
            var duration = 1000; // Sesuaikan durasi animasi sesuai kebutuhan Anda
            $({ percentage: 0 }).animate({ percentage: targetPercentage }, {
                duration: duration,
                step: function () {
                    equipmentPercentageElement.text(Math.round(this.percentage) + '%');
                }
            });

        });

        // SCRIPT ANIMASI BARCHART
        $(document).ready(function () {
            $('.animatedBar').each(function () {
                var initialHeight = 0; // Tinggi awal
                var finalHeight = parseFloat($(this).css('height'));
                $(this).css('height', initialHeight);
                $(this).animate({
                    height: finalHeight + '%'
                }, 2000);
            });
        });

        // SCRIPT RESPONSIVE
        dropdown = document.querySelector(".dropDown");
        navbarRight = document.querySelector(".navbarRight");
        dropdown.onclick = function () {
            navbarRight.classList.toggle("Active");
        };

        // SCRIPT POP UP
        function openPopup() {
            document.getElementById('Popup').classList.add('show');
            document.getElementById('overlay').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('Popup').classList.remove('show');
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>

</html>