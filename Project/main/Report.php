<?php
include "Connection.php";
session_start();

$access = mysqli_query($Conn, "SELECT * FROM tb_users WHERE nama = '$_SESSION[nama]'");
$accessResult = mysqli_fetch_array($access);
if ($accessResult['level'] == "Admin") {
    $registerAccess = "display: flex;";
    $reportAccess = "display: flex";
    $validation = "display: none;";
} else if ($accessResult['level'] == "Supervisor") {
    $registerAccess = "display: flex;";
    $reportAccess = "display: flex";
    $validation = "display: flex;";
} else if ($accessResult['level'] == "K3L") {
    $registerAccess = "display: none;";
    $reportAccess = "display: flex";
    $validation = "display: flex;";
} else {
    $registerAccess = "display: none";
    $reportAccess = "display: none";
    $validation = "display: none;";
}

$queryUser = mysqli_query($Conn, "SELECT COUNT(*) as total FROM tb_users WHERE level = 'Petugas'");
$rowUser = mysqli_fetch_assoc($queryUser);
$totalEmployee = $rowUser['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Report</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Report.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                <a href=" Equipment.php">
                    <div class="markLine" style="display: none;"></div>Equipment
                </a>
                <a href="Report.php" style="<?php echo $reportAccess; ?>" class="Active">
                    <div class="markLine"></div>Report
                </a>
                <a href="Register.php" style="<?php echo $registerAccess; ?>">
                    <div class="markLine" style="display: none;"></div>Register
                </a>
                <a href=" Logout.php">Logout
                </a>
            </div>
            <div class="dropDown">
                <i class="bi bi-list"></i>
            </div>
        </div>
    </div>

    <div class="subHeader">
        <div class="subHeaderLeft">
            <p>Nice to meet you,</p>
            <p>
                <?php echo $_SESSION['nama']; ?>
            </p>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="request">
        <div class="requestLeft">
            <div class="headerRequestLeft">
                <p>Overview</p>
            </div>
            <div class="lineRequestLeft"></div>
            <div class="contentRequestLeft">
                <?php
                include "Connection.php";
                $queryAttendance = mysqli_query($Conn, "SELECT keterangan, COUNT(*) as total FROM tb_absen GROUP BY keterangan");
                $attendanceData = array();
                while ($rowAttendance = mysqli_fetch_assoc($queryAttendance)) {
                    $attendanceData[$rowAttendance['keterangan']] = $rowAttendance['total'];
                }

                $pendingAttendance = $attendanceData['Belum Validasi'] ?? 0;
                $validAttendance = $attendanceData['Sudah Validasi'] ?? 0;
                $invalidAttendance = $attendanceData['Tidak Validasi'] ?? 0;
                $totalAttendanceRequest = $pendingAttendance + $validAttendance + $invalidAttendance;

                $percentagePendingAttendance = number_format(($pendingAttendance / $totalAttendanceRequest) * 100, 0) . '%';
                $percentageValidAttendance = number_format(($validAttendance / $totalAttendanceRequest) * 100, 0) . '%';
                $percentageInvalidAttendance = number_format(($invalidAttendance / $totalAttendanceRequest) * 100, 0) . '%';

                $queryEquipment = mysqli_query($Conn, "SELECT keterangan, COUNT(*) as total FROM tb_apd GROUP BY keterangan");
                $equipmentData = array();
                while ($rowEquipment = mysqli_fetch_assoc($queryEquipment)) {
                    $equipmentData[$rowEquipment['keterangan']] = $rowEquipment['total'];
                }
                $pendingEquipment = $equipmentData['Belum Validasi'] ?? 0;
                $validEquipment = $equipmentData['Sudah Validasi'] ?? 0;
                $invalidEquipment = $equipmentData['Tidak Validasi'] ?? 0;
                $totalEquipmentRequest = $pendingEquipment + $validEquipment + $invalidEquipment;

                $percentagePendingEquipment = number_format(($pendingEquipment / $totalEquipmentRequest) * 100, 0) . '%';
                $percentageValidEquipment = number_format(($validEquipment / $totalEquipmentRequest) * 100, 0) . '%';
                $percentageInvalidEquipment = number_format(($invalidEquipment / $totalEquipmentRequest) * 100, 0) . '%';
                ?>
                <div class="requestLeftBox">
                    <p>Attendance Pending</p>
                    <p class="percentage" data-value="<?php echo $percentagePendingAttendance; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentagePendingAttendance; ?>"></div>
                    </div>
                </div>
                <div class="requestLeftBox">
                    <p>Attendance Valid</p>
                    <p class="percentage" data-value="<?php echo $percentageValidAttendance; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentageValidAttendance; ?>"></div>
                    </div>
                </div>
                <div class="requestLeftBox">
                    <p>Attendance Invalid</p>
                    <p class="percentage" data-value="<?php echo $percentageInvalidAttendance; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentageInvalidAttendance; ?>"></div>
                    </div>
                </div>
                <div class="requestLeftBox">
                    <p>Equipment Pending</p>
                    <p class="percentage" data-value="<?php echo $percentagePendingEquipment; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentagePendingEquipment; ?>"></div>
                    </div>
                </div>
                <div class="requestLeftBox">
                    <p>Equipment Valid</p>
                    <p class="percentage" data-value="<?php echo $percentageValidEquipment; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentageValidEquipment; ?>"></div>
                    </div>
                </div>
                <div class="requestLeftBox">
                    <p>Equipment Invalid</p>
                    <p class="percentage" data-value="<?php echo $percentageInvalidEquipment; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentageInvalidEquipment; ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="requestRight">
            <div class="headerTable">
                <p>Request</p>
            </div>
            <div class="lineTable"></div>
            <div class="contentTable">
                <?php
                include "Connection.php";
                date_default_timezone_set('Asia/Jakarta');
                $currentTime = date("H:i:s");
                $startTime = "00:00:00";
                $endTime = "23:59:00";
                $recentAttendance = mysqli_query($Conn, "SELECT tb_users.nama, tb_users.jabatan, tb_absen.id_absen, tb_absen.jam, tb_absen.status 
                FROM tb_users INNER JOIN tb_absen ON tb_users.id_user = tb_absen.id_user WHERE tb_absen.tanggal = CURDATE() AND tb_absen.jam 
                BETWEEN '$startTime' AND '$endTime' AND tb_absen.tanggal = CURDATE() AND keterangan = 'Belum Validasi' ORDER BY tb_absen.jam DESC");

                $recentEquipment = mysqli_query($Conn, "SELECT tb_users.nama, tb_users.jabatan, tb_apd.id_check, tb_apd.jam, tb_apd.helm, tb_apd.kacamata, tb_apd.sarung_tangan, tb_apd.id_card, tb_apd.rompi, tb_apd.body_harness, tb_apd.sepatu
                FROM tb_users INNER JOIN tb_apd ON tb_users.id_user = tb_apd.id_user WHERE tb_apd.tanggal = CURDATE() AND tb_apd.jam 
                BETWEEN '$startTime' AND '$endTime' AND tb_apd.tanggal = CURDATE() AND keterangan = 'Belum Validasi' ORDER BY tb_apd.jam DESC");

                $result = array();

                while ($Print = mysqli_fetch_assoc($recentAttendance)) {
                    $Print['activity'] = "Attendance";
                    $Print['id'] = $Print['id_absen'];
                    $Print['data'] = $Print['status'];
                    if ($Print > 0) {
                        $nothingActivity = "display: none;";
                    } else {
                        $nothingActivity = "display: flex;";
                    }
                    $result[] = $Print;
                }
                while ($Print = mysqli_fetch_assoc($recentEquipment)) {
                    $Print['activity'] = "Equipment";
                    $Print['id'] = $Print['id_check'];
                    $totalHelm = ($Print['helm'] == 'Yes') ? 1 : 0;
                    $totalVest = ($Print['rompi'] == 'Yes') ? 1 : 0;
                    $totalBoots = ($Print['sepatu'] == 'Yes') ? 1 : 0;
                    $totalCard = ($Print['id_card'] == 'Yes') ? 1 : 0;
                    $totalEyeglass = ($Print['kacamata'] == 'Yes') ? 1 : 0;
                    $totalHarness = ($Print['body_harness'] == 'Yes') ? 1 : 0;
                    $totalGloves = ($Print['sarung_tangan'] == 'Yes') ? 1 : 0;
                    $Print['data'] = ($totalHelm + $totalVest + $totalBoots + $totalCard + $totalEyeglass + $totalHarness + $totalGloves) . '&nbspof 7';

                    if ($Print > 0) {
                        $nothingActivity = "display: none;";
                    } else {
                        $nothingActivity = "display: flex;";
                    }
                    $result[] = $Print;
                }
                usort($result, function ($a, $b) {
                    return strtotime($b['jam']) - strtotime($a['jam']);
                });
                foreach ($result as $Print) {
                    if ($Print['status'] == 'Hadir') {
                        $Color = "color: #60A578;";
                    } else if ($Print['status'] == 'Sakit') {
                        $Color = "color: #368687;";
                    } else if ($Print['status'] == 'Izin') {
                        $Color = "color: #A26840;";
                    } else if ($Print['status'] == 'Alpa') {
                        $Color = "color: #92344C;";
                    }
                    ?>
                    <div class="requestBox">
                        <div class="infoUser">
                            <p>
                                <?php echo $Print['nama']; ?>
                            </p>
                            <p>
                                <?php echo $Print['jabatan']; ?>
                            </p>
                        </div>
                        <div class="requestActivity">
                            <div class="requestActivityBox">
                                <p>
                                    <?php echo $Print['activity']; ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="requestActivityBox">
                                <p>
                                    <?php echo date("H.i A", strtotime($Print['jam'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="requestActivityBox">
                                <p style="<?php echo $Color; ?>">
                                    <?php echo $Print['data']; ?>
                                </p>
                            </div>
                            <p class="gap" style="<?php echo $validation; ?>">|</p>
                            <div class="requestActivityBox" style="<?php echo $validation; ?>">
                                <a href="#"
                                    onclick="openPopup(<?php echo $Print['id']; ?>, '<?php echo $Print['activity']; ?>')">Delete</a>
                                <a
                                    href="?idConfirm=<?php echo $Print['id']; ?>&activityConfirm=<?php echo $Print['activity']; ?>">Confirm</a>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
                <p class="nothingActivity" style="<?php echo $nothingActivity; ?>">There is no request today</p>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="popUp" id="popUp">
        <div class="headerPopUp">
            <p>Are you sure to delete this Request?</p>
            <p>If you are sure, please fill in the reasons below.</p>
        </div>
        <div class="linePopUp"></div>
        <form method="POST" autocomplete="off" class="contentPopUp">
            <input type="hidden" name="idToDelete" id="idToDelete" />
            <input type="hidden" name="activityToDelete" id="activityToDelete" />
            <input type="text" name="Reason" id="Reason" required />
            <div class="button">
                <button name="DeleteData" class="deleteButton">Delete</button>
                <button type="button" onclick="closePopup()">Cancel</button>
            </div>
        </form>
    </div>

    <?php
    include "Connection.php";
    // DELETE DATA
    if (isset($_POST['DeleteData'])) {
        $id = $_POST['idToDelete'];
        $alasan = $_POST['Reason'];
        $actvityName = $_POST['activityToDelete'];
        if ($actvityName == "Attendance") {
            $tableName = "tb_absen";
            $idName = "id_absen";
        } else if ($actvityName == "Equipment") {
            $tableName = "tb_apd";
            $idName = "id_check";
        }
        $deleteData = mysqli_query($Conn, "UPDATE $tableName SET keterangan = 'Tidak Validasi', alasan = '$alasan' WHERE $idName = '$id'");
        if ($deleteData) {
            echo "<meta http-equiv=refresh content=0;URL='Report.php'>";
        } else {
            echo "Error updating record: " . mysqli_error($Conn);
        }
    }
    ?>

    <?php
    include "Connection.php";
    // CONFIRM DATA
    if (isset($_GET['idConfirm']) && isset($_GET['activityConfirm'])) {
        $id = mysqli_real_escape_string($Conn, $_GET['idConfirm']);
        $actvityName = mysqli_real_escape_string($Conn, $_GET['activityConfirm']);
        if ($actvityName == "Attendance") {
            $tableName = "tb_absen";
            $idName = "id_absen";
        } else if ($actvityName == "Equipment") {
            $tableName = "tb_apd";
            $idName = "id_check";
        }
        $confirmData = mysqli_query($Conn, "UPDATE $tableName SET keterangan = 'Sudah Validasi' WHERE $idName = '$id'");
        if ($confirmData) {
            echo "<meta http-equiv=refresh content=0;URL='Report.php'>";
        } else {
            echo "Error updating record: " . mysqli_error($Conn);
        }
    } ?>

    <script>
        // SCRIPT ANIMATE OVERVIEW
        $(document).ready(function () {
            function setPercentageAndAnimate(percentageElement, barElement) {
                var targetPercentage = percentageElement.data('value');
                barElement.css('width', '0%').animate({ width: targetPercentage }, {
                    duration: 1000,
                    step: function (now, fx) {
                        var percentage = Math.round(now * 100 / barElement.parent().width()) + '%';
                        percentageElement.text(percentage);
                    },
                    complete: function () {
                        percentageElement.text(targetPercentage);
                    }
                });
            }
            setPercentageAndAnimate($('.percentage:eq(0)'), $('.bar:eq(0)'));
            setPercentageAndAnimate($('.percentage:eq(1)'), $('.bar:eq(1)'));
            setPercentageAndAnimate($('.percentage:eq(2)'), $('.bar:eq(2)'));
            setPercentageAndAnimate($('.percentage:eq(3)'), $('.bar:eq(3)'));
            setPercentageAndAnimate($('.percentage:eq(4)'), $('.bar:eq(4)'));
            setPercentageAndAnimate($('.percentage:eq(5)'), $('.bar:eq(5)'));
        });

        // SCRIPT RESPONSIVE
        dropdown = document.querySelector(".dropDown");
        navbarRight = document.querySelector(".navbarRight");
        dropdown.onclick = function () {
            navbarRight.classList.toggle("Active");
        };

        // SCRIPT POP UP
        function openPopup(id, activity) {
            document.getElementById("idToDelete").value = id;
            document.getElementById("activityToDelete").value = activity;
            document.getElementById('popUp').classList.add('show');
            document.getElementById("overlay").style.display = "flex";
        }
        function closePopup() {
            document.getElementById('popUp').classList.remove('show');
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>

</html>