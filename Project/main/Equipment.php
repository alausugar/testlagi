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
    $formEquipment = "display: none";
} else if ($accessResult['level'] == "K3L") {
    $registerAccess = "display: none;";
    $reportAccess = "display: flex";
    $formEquipment = "display: none";
} else {
    $registerAccess = "display: none";
    $reportAccess = "display: none";
    $formEquipment = "display: flex";
}

$limitEquipment = mysqli_num_rows(mysqli_query($Conn, "SELECT * FROM tb_apd WHERE id_user = '$id_user' AND tanggal = CURDATE()"));
if ($limitEquipment > 0) {
    $equipmentButton = "display: none;";
    $message = "display: flex;";
} else {
    $equipmentButton = "display: block;";
    $message = "display: none;";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inspect • Equipment</title>
    <link rel="icon" href="Image/K3.png" />
    <link rel="stylesheet" href="Equipment.css?v=<?php echo time(); ?>" />
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
                <a href="Equipment.php" class="Active">
                    <div class="markLine"></div>Equipment
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
            <p>Stay safety,</p>
            <p>
                <?php echo $_SESSION['nama']; ?>
            </p>
        </div>
        <div class="subHeaderRight">
            <form action="ExcelApd.php" style="<?php echo $registerAccess; ?>" method="GET">
                <input type="hidden" name="tanggalAwal" value="<?php echo $_GET["tanggalAwal"] ?? ''; ?>">
                <input type="hidden" name="tanggalAkhir" value="<?php echo $_GET["tanggalAkhir"] ?? ''; ?>">
                <button type="submit"><i class="bi bi-file-earmark-spreadsheet-fill"></i>Export</button>
            </form>
        </div>
    </div>

    <hr color="#ECEDEC" style="margin: -5px 20px 0 20px;">

    <div class="equipment">
        <div class="formEquipment" style="<?php echo $formEquipment; ?>">
            <div class="headerFormEquipment">
                <p>Equipment</p>
                <p>Make sure you use all the equipment!</p>
            </div>
            <div class="lineFormEquipment"></div>
            <form class="contentFormEquipment" method="POST" onsubmit="return validateForm()">
                <div class="choiceChipGroup">
                    <div class="choiceChip">
                        <input type="checkbox" id="Helmet" name="resultHelmet" value="No"
                            onchange="handleCheckboxChange(this, 'resultHelmet')">
                        <label for="Helmet">
                            <p>Helmet</p>
                            <p id="resultHelmet">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Eyeglass" name="resultEyeglass" value="No"
                            onchange="handleCheckboxChange(this, 'resultEyeglass')">
                        <label for="Eyeglass">
                            <p>Eyeglass</p>
                            <p id="resultEyeglass">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Gloves" name="resultGloves" value="No"
                            onchange="handleCheckboxChange(this, 'resultGloves')">
                        <label for="Gloves">
                            <p>Gloves</p>
                            <p id="resultGloves">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Card" name="resultCard" value="No"
                            onchange="handleCheckboxChange(this, 'resultCard')">
                        <label for="Card">
                            <p>Card</p>
                            <p id="resultCard">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Harness" name="resultHarness" value="No"
                            onchange="handleCheckboxChange(this, 'resultHarness')">
                        <label for="Harness">
                            <p>Harness</p>
                            <p id="resultHarness">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Vest" name="resultVest" value="No"
                            onchange="handleCheckboxChange(this, 'resultVest')">
                        <label for="Vest">
                            <p>Vest</p>
                            <p id="resultVest">No</p>
                        </label>
                    </div>
                    <div class="choiceChip">
                        <input type="checkbox" id="Boots" name="resultBoots" value="No"
                            onchange="handleCheckboxChange(this, 'resultBoots')">
                        <label for="Boots">
                            <p>Boots</p>
                            <p id="resultBoots">No</p>
                        </label>
                    </div>
                    <p class="alertMessage">
                        <i class=" bi bi-exclamation-circle-fill"></i>
                        Please choose at least one
                    </p>
                    <button class="equipmentButton" type="submit" name="Equipment"
                        style="<?php echo $equipmentButton; ?>" id="submitButton" disabled>SUBMIT</button>
                    <p class="message" style="<?php echo $message; ?>"><i class="bi bi-check-circle-fill"></i>Stay
                        safety,
                        <?php echo $_SESSION['nama']; ?>!
                    </p>
                </div>
            </form>
        </div>

        <div class="tableEquipment">
            <div class="headerTableEquipment">
                <div class="headerTableEquipmentLeft">
                    <p>Equipment History</p>
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

                <form class="headerTableEquipmentRight" method="GET">
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
            <div class="lineTableEquipment"></div>
            <div class="contentTableEquipment">
                <?php
                include "Connection.php";
                $historyEquipment = "SELECT * FROM tb_users, tb_apd WHERE tb_users.id_user = tb_apd.id_user AND keterangan = 'Sudah Validasi'";

                $startDate = $_GET["tanggalAwal"] ?? null;
                $endDate = $_GET["tanggalAkhir"] ?? null;
                if ($startDate && $endDate) {
                    $startDate = mysqli_real_escape_string($Conn, $startDate);
                    $endDate = mysqli_real_escape_string($Conn, $endDate);
                    $historyEquipment .= " AND tb_apd.tanggal BETWEEN '$startDate' AND '$endDate'";
                }
                $historyEquipment .= " ORDER BY tb_apd.tanggal DESC";

                $resultHistoryEquipment = mysqli_query($Conn, $historyEquipment);
                while ($printData = mysqli_fetch_array($resultHistoryEquipment)) {
                    $totalHelm = ($printData['helm'] == 'Yes') ? 1 : 0;
                    $totalVest = ($printData['rompi'] == 'Yes') ? 1 : 0;
                    $totalBoots = ($printData['sepatu'] == 'Yes') ? 1 : 0;
                    $totalCard = ($printData['id_card'] == 'Yes') ? 1 : 0;
                    $totalEyeglass = ($printData['kacamata'] == 'Yes') ? 1 : 0;
                    $totalHarness = ($printData['body_harness'] == 'Yes') ? 1 : 0;
                    $totalGloves = ($printData['sarung_tangan'] == 'Yes') ? 1 : 0;
                    $printData['data'] = ($totalHelm + $totalVest + $totalBoots + $totalCard + $totalEyeglass + $totalHarness + $totalGloves);
                    if ($printData['data'] == '7') {
                        $Color = "color: #60A578; background-color: #D4EADE;";
                    } else if ($printData['data'] == '6') {
                        $Color = "color: #A26840; background-color: #F9C7A4;";
                    } else if ($printData['data'] < '6') {
                        $Color = "color: #92344C; background-color: #FFC3CF;";
                    }
                    ?>
                    <div class="equipmentBox" dataName="<?php echo strtolower($printData['nama']); ?>">
                        <div class=" infoUser">
                            <p>
                                <?php echo $printData['nama']; ?>
                            </p>
                            <p>
                                <?php echo $printData['jabatan']; ?>
                            </p>
                        </div>
                        <div class="equipmentActivity">
                            <div class="equipmentActivityBox">
                                <p>
                                    <?php echo date("M, d", strtotime($printData['tanggal'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="equipmentActivityBox">
                                <p>
                                    <?php echo date("H.i A", strtotime($printData['jam'])); ?>
                                </p>
                            </div>
                            <p class="gap">|</p>
                            <div class="equipmentActivityBox">
                                <p class="status" style="<?php echo $Color; ?>">
                                    <?php echo $printData['data']; ?>&nbspof 7
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>

    <?php
    include "Connection.php";
    date_default_timezone_set('Asia/Jakarta');

    $tanggal = date("Y/m/d");
    $jam = date("07:58:42");
    $id_user = $_SESSION['id_user'];

    $selectMax = mysqli_query($Conn, "SELECT MAX(id_check) as maxId_Check FROM tb_apd");
    $resultMax = mysqli_fetch_array($selectMax);
    $maxCode = $resultMax['maxId_Check'];
    $no = (int) substr($maxCode, -3);
    $no++;
    $newCode = sprintf("216%03s", $no);

    if (isset($_POST['Equipment'])) {
        $helm = isset($_POST['resultHelmet']) ? $_POST['resultHelmet'] : 'No';
        $kacamata = isset($_POST['resultEyeglass']) ? $_POST['resultEyeglass'] : 'No';
        $sarung_tangan = isset($_POST['resultGloves']) ? $_POST['resultGloves'] : 'No';
        $id_card = isset($_POST['resultCard']) ? $_POST['resultCard'] : 'No';
        $harness = isset($_POST['resultHarness']) ? $_POST['resultHarness'] : 'No';
        $rompi = isset($_POST['resultVest']) ? $_POST['resultVest'] : 'No';
        $sepatu = isset($_POST['resultBoots']) ? $_POST['resultBoots'] : 'No';

        $Insert = mysqli_query($Conn, "INSERT INTO tb_apd (id_check, id_user, tanggal, jam, helm, kacamata, sarung_tangan, id_card, rompi, body_harness, sepatu, keterangan) 
        VALUES ('$newCode', '$id_user', '$tanggal', '$jam', '$helm', '$kacamata', '$sarung_tangan', '$id_card', '$rompi', '$harness', '$sepatu', 'Belum Validasi')");
        echo "<meta http-equiv=refresh content=1;URL='Equipment.php'>";
    }
    ?>
    <script>
        // SCRIPT SEARCH
        function searchFunction() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const attendanceBoxes = document.querySelectorAll('.equipmentBox');

            attendanceBoxes.forEach(item => {
                const itemNama = item.getAttribute('dataName');
                item.style.display = itemNama.includes(searchInput) ? 'flex' : 'none';
            });
        };

        // SCRIPT RESPONSIVE
        dropdown = document.querySelector(".dropDown");
        navbarRight = document.querySelector(".navbarRight");
        dropdown.onclick = function () {
            navbarRight.classList.toggle("Active");
        };

        // SCRIPT FOR CHECKBOX
        function handleCheckboxChange(checkbox, resultId) {
            var resultElement = document.getElementById(resultId);
            if (checkbox.checked) {
                checkbox.value = "Yes";
                resultElement.style.color = "#FFFFFF";
            } else {
                checkbox.value = "No";
                resultElement.style.color = "#C1C1C1";
            }
            resultElement.textContent = checkbox.value;
            validateForm();
        }

        function validateForm() {
            var checkboxes = document.querySelectorAll('input[name^="result"]');
            var submitButton = document.getElementById('submitButton');
            var alertMessage = document.querySelector('.alertMessage');
            var isAtLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            submitButton.disabled = !isAtLeastOneChecked;
            console.log('Is at least one checked:', isAtLeastOneChecked);
            if (!isAtLeastOneChecked) {
                console.log('Displaying alert message');
                alertMessage.style.opacity = '1';
            } else {
                console.log('Hiding alert message');
                alertMessage.style.opacity = '0';
            }
            return isAtLeastOneChecked;
        }

    </script>
</body>

</html>