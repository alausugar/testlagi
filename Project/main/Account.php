<?php
include "Connection.php";
session_start();

$idUser = mysqli_real_escape_string($Conn, $_GET['idUser']);
$userData = mysqli_query($Conn, "SELECT * FROM tb_users WHERE id_user = '$idUser'");
$dataUser = mysqli_fetch_assoc($userData);
$idUser = $dataUser['id_user'];
$userName = $dataUser['nama'];
$userJob = $dataUser['jabatan'];
$userTeam = $dataUser['team'];
$userUsername = $dataUser['username'];

$access = mysqli_query($Conn, "SELECT * FROM tb_users WHERE nama = '$_SESSION[nama]'");
$accessResult = mysqli_fetch_array($access);
if ($accessResult['level'] == "Admin") {
    $deleteButton = "display: flex;";
} else if ($accessResult['level'] == "Supervisor") {
    $deleteButton = "display: none";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Account</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Account.css?v=<?php echo time(); ?>" />
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
                <a href="Equipment.php">
                    <div class="markLine" style="display: none;"></div>Equipment
                </a>
                <a href="Report.php" style="<?php echo $buttonAccess; ?>">
                    <div class="markLine" style="display: none;"></div>Report
                </a>
                <a href="Register.php" style="<?php echo $buttonAccess; ?>">
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
            <p>Users</p>
            <p>Account Details</p>
        </div>
        <div class="subHeaderRight">
            <a href="Register.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#252525"
                    stroke="#252525" stroke-width="0.25" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                </svg>Go Back</a>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="account">
        <div class="accountLeft">
            <div class="headerAccountLeft">
                <p>Overview</p>
            </div>
            <div class="lineAccountLeft"></div>
            <div class="contentAccountLeft">
                <?php
                include "Connection.php";
                $totalAttendance = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$_GET[idUser]' AND keterangan = 'Sudah Validasi'"));
                $totalPresent = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$_GET[idUser]' AND status = 'Hadir' AND keterangan = 'Sudah Validasi'"));
                $totalSick = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$_GET[idUser]' AND status = 'Sakit' AND keterangan = 'Sudah Validasi'"));
                $totalPermission = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$_GET[idUser]' AND status = 'Izin' AND keterangan = 'Sudah Validasi'"));
                $totalAlpha = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_absen WHERE id_user = '$_GET[idUser]' AND status = 'Alpa' AND keterangan = 'Sudah Validasi'"));
                $percentagePresent = ($totalAttendance != 0) ? number_format(($totalPresent / $totalAttendance) * 100, 0) . '%' : '0%';

                $id_user = $_GET['idUser'];

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
                $percentageEquipment = $Equipment > 0 ? number_format(($EquipmentYes / $Equipment) * 100, 0) . '%' : '0%';
                $present = ($totalAttendance != 0) ? number_format(($totalPresent / $totalAttendance) * 100, 0) : '0';
                $equipment = ($Equipment != 0) ? number_format(($EquipmentYes / $Equipment) * 100, 0) : '0';
                $averageRating = number_format(((($present + $equipment) / 2) / 100) * 5, 1);
                ?>
                <div class="accountLeftBox">
                    <div class="boxAttendance">
                        <p>Present</p>
                        <p>
                            <?php echo $totalPresent; ?>
                        </p>
                    </div>
                    <div class="boxAttendance">
                        <p>Sick</p>
                        <p>
                            <?php echo $totalSick; ?>
                        </p>
                    </div>
                    <div class="boxAttendance">
                        <p>Permission</p>
                        <p>
                            <?php echo $totalPermission; ?>
                        </p>
                    </div>
                    <div class="boxAttendance">
                        <p>Alpha</p>
                        <p>
                            <?php echo $totalAlpha; ?>
                        </p>
                    </div>
                </div>
                <div class="accountLeftBox">
                    <p>Attendance Completeness</p>
                    <p class="percentage" data-value="<?php echo $percentagePresent; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentagePresent; ?>"></div>
                    </div>
                </div>
                <div class="accountLeftBox">
                    <p>Equipment Completeness</p>
                    <p class="percentage" data-value="<?php echo $percentageEquipment; ?>">0%</p>
                    <div class="backgroundBar">
                        <div class="bar" style="width: <?php echo $percentageEquipment; ?>"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accountRight">
            <div class="headerAccountRight">
                <p>Users Details</p>
            </div>
            <div class="lineAccountRight"></div>
            <div class="contentAccountRight">
                <div class="infoUser">
                    <div class="biodata">
                        <p class="idUser">
                            <?php echo $idUser; ?>
                        </p>
                        <p class="userName">
                            <?php echo $userName; ?>
                        </p>
                        <p class="userJob">
                            <?php echo $userJob; ?>
                        </p>
                        <p class="userTeam">
                            <?php echo $userTeam; ?>
                        </p>
                    </div>
                    <div class="rating">
                        <p>Average Rating</p>
                        <div class="ratingStar">
                            <div class="starRating" data-rating="<?php echo $averageRating; ?>">
                                <span class="star" data-value="1">&#9733;</span>
                                <span class="star" data-value="2">&#9733;</span>
                                <span class="star" data-value="3">&#9733;</span>
                                <span class="star" data-value="4">&#9733;</span>
                                <span class="star" data-value="5">&#9733;</span>
                            </div>
                            <p>
                                <?php echo $averageRating; ?>
                            </p>
                        </div>
                    </div>
                    <div class="footer">
                        <p class="location"><i class="bi bi-geo-alt-fill"></i> ULP Sukarami</p>
                        <p class="address">Jl. Kelapa Gading Km 9, RT. 1 RW, Karya Baru, Alang Alang Lebar,<br> Karya
                            Baru,
                            Kec.
                            Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 30961</p>
                    </div>
                </div>
                <div class="action" style="<?php echo $deleteButton; ?>">
                    <a href="?delete=<?php echo $idUser; ?>">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "Connection.php";
    // DELETE DATA
    if (isset($_GET['delete'])) {
        $id = mysqli_real_escape_string($Conn, $_GET['delete']);
        $deleteData = mysqli_query($Conn, "DELETE FROM tb_users WHERE id_user = '$id'");
        if ($deleteData) {
            echo "<meta http-equiv=refresh content=0;URL='Register.php'>";
        } else {
            echo "Error updating record: " . mysqli_error($Conn);
        }
    }
    ?>

    <script>
        // SCRIPT RATING
        document.addEventListener('DOMContentLoaded', function () {
            const ratingContainer = document.querySelector('.starRating');
            const stars = ratingContainer.querySelectorAll('.star');
            const averageRating = parseFloat(ratingContainer.getAttribute('data-rating'));
            console.log('Average Rating:', averageRating);
            stars.forEach((star, index) => {
                if (index + 1 <= averageRating) {
                    star.classList.add('Active');
                }
            });
        });

        // SCRIPT ANIMATE OVERVIEW
        $(document).ready(function () {
            function setPercentageAndAnimate(percentageElement, barElement) {
                var targetPercentage = percentageElement.data('value');
                barElement.css('width', '0%');
                barElement.animate({ width: targetPercentage }, {
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
        });
    </script>
</body>

</html>