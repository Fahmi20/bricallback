<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRINT PO</title>

    <link rel="stylesheet" href="<?= base_url() ?>assets/vendors/core/core.css">

    <link rel="stylesheet" href="<?= base_url() ?>assets/css/demo1/style.css">
    <style>
        /* .table-bordered,
        .table-bordered td, */
        .table-bordered thead th {
            border: 2px solid;
        }

        hr {
            border: 2px solid;
        }

        .table-bordered {
            border-color: black;
            width: 100%;
            border-collapse: collapse;
        }

        footer {
            width: 100%;
            bottom: 0px;
            position: absolute;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <hr>
                <h3 style="text-align: center;">PRINT PURCHASE ORDER</h3>
                <hr>
            </div>
        </div>
        <div class="row" style="height: 50px;">
            <div class="col-12">
                <table>
                    <tbody>
                        <tr>
                            <td>Nama</td>
                            <td>: <?= $po['nama_user'] ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal PO</td>
                            <td>: <?= date('d-m-Y', strtotime($po['tgl_po'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h4 style="text-align: Center;">Detail Barang</h4>
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;color:black">No</th>
                            <th style="text-align: center;color:black">Nama Barang</th>
                            <th style="text-align: center;color:black">Kuantiti</th>
                            <th style="text-align: center;color:black">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($detail_po as $row) { ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td style="text-align: center;"><?= $row->nama_barang ?></td>
                                <td style="text-align: center;"><?= $row->qty_po ?></td>
                                <td style="text-align: center;"><?= $row->ket_po ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th colspan="2" style="text-align: center;">Total</th>
                            <th style="text-align: center;"><?= $total['Total'] ?></th>
                            <th></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer>
        <div class="row">
            <div class="col-6">
                <p>Hormat Kami,</p>
                <p>PT. Mayora Indah. TBK,</p>
                <br>
                <br>
                <br>
                <br>
                <p><?= $po['nama_user'] ?></p>
            </div>
            <div class="col-6" style="text-align: right;">
                <p>Diketahui Oleh,</p>
                <p>Manager Warehouse</p>
                <br><br><br><br>
                <p>[NAMA MANAGER]</p>
            </div>

            <div class="col-12" style="text-align: center;">
                <br>
                <br>
                <p>Disetujui Oleh,</p>
                <br><br><br><br>
                <p>Departement Purchasing</p>
            </div>
        </div>
    </footer>

    <script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
    <script>
        setTimeout(function() {
            window.print();
        }, 10);
        window.onfocus = function() {
            setTimeout(function() {
                window.close('', '_parent', '')
            }, 10);
        }
    </script>
</body>

</html>