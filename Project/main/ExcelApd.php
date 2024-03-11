<?php
include "Connection.php";
if (isset($_GET['tanggalAwal']) && isset($_GET['tanggalAkhir'])) {
    $startDate = $_GET['tanggalAwal'];
    $endDate = $_GET['tanggalAkhir'];
}

header("Content-type:application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Penggunaan_APD_Yantek.xls");
?>
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        .header {
            width: 100%;
            text-align: center;
            position: relative;
        }

        .header h1 {
            margin: 0;
        }

        .header h4 {
            margin-top: 0;
        }

        table {
            border-collapse: collapse;
            width: 45%;
        }

        th {
            padding: 6px 10px;
            text-align: center;
            background-color: #93BFCF;
            border: solid 1.5px #C1C1C1;
        }

        th:nth-child(2) {
            text-align: start;
        }

        td {
            padding: 2px 10px;
            text-align: center;
            border: solid 1.5px #C1C1C1;
        }

        td:nth-child(2) {
            text-align: start;
        }

        @media print {
            header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN ABSENSI PETUGAS YANTEK</h1>
        <h4>Periode :
            <?php
            if ($startDate && $endDate) {
                echo date("d M, Y", strtotime($startDate)) . " - " . date("d M, Y", strtotime($endDate));
            } else {
                echo "Keseluruhan";
            }
            ?>
        </h4>
    </div>

    <table>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 22%; text-align: left;">Nama</th>
            <th style="width: 10%;">Jabatan</th>
            <th style="width: 15%;">Tanggal</th>
            <th style="width: 12%;">Jam</th>
            <th style="width: 16%;">Status</th>
        </tr>
        <?php
        include "Connection.php";
        $query = "SELECT * FROM tb_users INNER JOIN tb_apd ON tb_users.id_user = tb_apd.id_user WHERE keterangan = 'Sudah Validasi'";
        if ($startDate && $endDate) {
            $startDate = mysqli_real_escape_string($Conn, $startDate);
            $endDate = mysqli_real_escape_string($Conn, $endDate);
            $query .= " AND tb_apd.tanggal BETWEEN '$startDate' AND '$endDate'";
        }
        $query .= " ORDER BY tb_apd.tanggal DESC";
        $no = 1;
        $result = mysqli_query($Conn, $query);
        while ($printData = mysqli_fetch_assoc($result)) {
            $totalHelm = ($printData['helm'] == 'Yes') ? 1 : 0;
            $totalVest = ($printData['rompi'] == 'Yes') ? 1 : 0;
            $totalBoots = ($printData['sepatu'] == 'Yes') ? 1 : 0;
            $totalCard = ($printData['id_card'] == 'Yes') ? 1 : 0;
            $totalEyeglass = ($printData['kacamata'] == 'Yes') ? 1 : 0;
            $totalHarness = ($printData['body_harness'] == 'Yes') ? 1 : 0;
            $totalGloves = ($printData['sarung_tangan'] == 'Yes') ? 1 : 0;
            $printData['data'] = ($totalHelm + $totalVest + $totalBoots + $totalCard + $totalEyeglass + $totalHarness + $totalGloves);
            if ($printData['data'] == '7') {
                $status = "Lengkap";
                $color = "color: green;";
            } else if ($printData['data'] < '6') {
                $status = "Tidak Lengkap";
                $color = "color: red;";
            }
            ?>
            <tr>
                <td>
                    <?php echo $no++; ?>
                </td>
                <td style="text-align: left;">
                    <?php echo $printData['nama']; ?>
                </td>
                <td>
                    <?php echo $printData['jabatan']; ?>
                </td>
                <td>
                    <?php echo date("d-M-Y", strtotime($printData['tanggal'])); ?>
                </td>
                <td>
                    <?php echo date("H.i A", strtotime($printData['jam'])); ?>
                </td>
                <td style="<?php echo $color; ?>">
                    <?php echo $status; ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>

    <div class="signature">
        <h6 style="font-size: 14px">Manager ULP Sukarami</h6>
        <br><br>
        <p style="font-size: 14px">Agus Ibnu Tsani</p>
    </div>
</body>

</html>
<?php
echo ob_get_clean();
?>