<!doctype html>
<html lang="pl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>PDF - watermark</title>
  </head>
  <body style="padding: 20px;">
    <h1 style="margin: 0 0 40px 0;">PDF - watermark</h1>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <?php
        if (isset($_POST['submit'])) {
            if(isset($_POST['pdfSource'])) {
                if($_POST['pdfSource'] == "0"){
                    $fileError = "Wybierz numer!";
                }
            }
            if(isset($_POST['emailInput'])) {
                if($_POST['emailInput'] == ""){
                    $emailError = "Wpisz adres e-mail!";
                }
            }
        }
    ?>

    <div class="container">
        <form action="#" method="post">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <select class="form-control" id="pdf" name="pdfSource">
                            <option value="0">- wybierz -</option>
                            <?php
                                $dir = 'pdf/source/';

                                if (is_dir($dir)) {
                                    if ($dh = opendir($dir)) {
                                        while (($file = readdir($dh)) !== false) {
                                            if ($file == '.' || $file == '..') {
                                                continue;
                                            }
                                            echo "<option value=\"".$file."\">". $file . "</option>";
                                        }
                                        closedir($dh);
                                    }
                                }
                            ?>
                        </select>
                        <?php echo $fileError;?>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <input type="email" class="form-control" id="emailInput" name="emailInput" aria-describedby="emailHelp" placeholder="Wpisz adres e-mail">
                        <?php echo $emailError;?>
                    </div>
                </div>
                <div class="col-sm">
                    <input type="submit" name="submit" class="btn btn-danger" value="Generuj">
                </div>
            </div>
            <?php
                if($_POST['pdfSource'] != "0" && $_POST['emailInput'] != ""){
                    
                    header('Content-Type: text/html; charset=utf-8');

                    require_once('lib/fpdf/fpdf.php');
                    require_once('lib/fpdi/fpdi.php');

                    $wmText = $_POST['emailInput'];
                    $wmText = iconv('UTF-8', 'iso-8859-2', $wmText);

                    $date = date('d-m-Y_G-i-s');
                    $n = substr(uniqid('', true), -5);
                    $fileName = $_POST['pdfSource'];
                    $fileNameDownload = str_replace("_WWW.pdf", "", $_POST['pdfSource']);
                    $newFile = 'pdf/temp/'.$fileNameDownload.'_'.$n.'.pdf';

                    $pdf = new FPDI();

                    $pagecount = $pdf->setSourceFile('pdf/source/'.$fileName);

                    for ($i = 1; $i <= $pagecount; $i++) {
                        $tplidx = $pdf->importPage($i);
                        $specs = $pdf->getTemplateSize($tplidx);

                        $pdf->addPage($specs['h'] > $specs['w'] ? 'P' : 'L');
                        $pdf->useTemplate($tplidx, null, null, 0, 0, true);

                        $pdf->AddFont('arial','','arial.php');
                        $pdf->SetFont('arial');

                        $pdf->SetTextColor(204, 204, 204);

                        $pdf->SetTextColor(165, 27, 49);
                        $pdf->SetXY(0, 3);

                        $pdf->Write(0, 'Wersja dla: '.$wmText);
                    }

                    $pdf->Output($newFile, 'F');

                    echo "<hr />";
                    echo "<div class=\"row\">";
                    echo "    <div class=\"col-sm\">";
                    echo "Plik dla ".$_POST['emailInput'].", został wygenerowany <a href=\"".$newFile."\" class=\"btn btn-success\">Pobierz</a>";
                    echo "    </div>";
                    echo "</div>";
                }
            ?>
        </form>
    </div>

  </body>
</html>