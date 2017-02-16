<?php
header('Content-Type: text/html; charset=utf-8');

session_start();

include('action.php');

if(isset($_POST['submit'])){

  $ip     = $_SERVER['REMOTE_ADDR'];
  $smtag    = "FIX_MC49";

  $company  = $_REQUEST['company'];
  $name     = $_REQUEST['name'];
  $email    = $_REQUEST['email'];
  $phone    = $_REQUEST['phone'];

  $body = '
  <html>
  <body>

  Nazwa firmy / Instytucji: '.$company.'<br>
  Imię i nazwisko: '.$name.'<br>
  Telefon: '.$phone.'<br>
  Adres e-mail: '.$email.'<br>
  ____________________________________________________________________<br>
  <br>
  IP: '.$ip.'<br>
  Numer akcji: '.$tracking.'<br>
  SalesManago TAG: '.$smtag.'<br>
  </body>
  </html>
  ';

  date_default_timezone_set('Etc/UTC');

  require 'lib/phpmailer/PHPMailerAutoload.php';

  $mail = new PHPMailer;

  $mail->Debugoutput = 'html';

  $mail->Host = "";

  $mail->Port = 25;

  $mail->SMTPAuth = true;

  $mail->Username = "";

  $mail->Password = "";

  $mail->setFrom('automat@localhost', 'localhost - automat');
  $mail->addAddress('admin@localhost.pl');

  $mail->Subject = 'Poprawiony egzemplarz';
  $mail->CharSet = 'UTF-8';

  $mail->MsgHTML($body);

if(isset($_POST['sendform'])){
  // Create connection
  $conn = new mysqli("", "", "", "", 3307);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

    mysqli_set_charset($conn,"utf8");

    //escape variables for security
    $DBcompany = mysqli_real_escape_string($conn, $company);
    $DBname = mysqli_real_escape_string($conn, $name);
    $DBemail = mysqli_real_escape_string($conn, $email);
    $DBphone = mysqli_real_escape_string($conn, $phone);
    $DBip = mysqli_real_escape_string($conn, $ip);
    $DBsmtag = mysqli_real_escape_string($conn, $smtag);
    $DBtracking = mysqli_real_escape_string($conn, $tracking);

    $sql = "INSERT INTO lp (company,name,email,phone,ip,smtag,tracking)
    VALUES ('$DBcompany','$DBname','$DBemail','$DBphone','$DBip','$DBsmtag','$DBtracking')";

    if ($conn->query($sql) === TRUE) {
       // echo "Successfully Saved";

        } else {
        echo "Error: Go back and Try Again ! " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

  require_once('lib/fpdf/fpdf.php');
  require_once('lib/fpdi/fpdi.php');

  $wmText = $name . ' ' . $email;
  $wmText = iconv('UTF-8', 'iso-8859-2', $wmText);


  $date = date('d-m-Y_G-i-s');
  $n = substr(uniqid('', true), -5);
  $fileName = 'magazyn.pdf';
  $newFile = 'pdf/temp/magazyn'.$n.'.pdf';

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
  // exit();

  $_SESSION['name'] = $name;
  $_SESSION['email'] = $email;

// }
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PDF watermark download</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="/css/style.css" />
</head>

<body>

<header class="header header-page">
  <div class="container">
    <div class="row">
      <div class="col-xs-4 col-sm-4 col-md-4">
        <h2 class="logo">
          <img src="/images/logo.gif" width="339" height="133" alt="" title="" class="img-responsive" />
        </h2>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4">
        <img src="/images/slogan.gif" width="180" height="136" alt="" title="" class="img-responsive" />
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4"></div>
    </div>
  </div>
</header>

<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-8 content-box">

        <div class="col-xs-12 col-sm-12 col-md-6" style="margin-bottom: 20px;">Zapraszamy do pobrania bezpłatnego wydania magazynu.</div>

        <img class="size-full img-responsive center" style="margin-top: 20px; margin-bottom: 10px;" src="/images/cover.jpg" alt="" width="531" height="517">

		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 form-box">

      <h2>Pobierz plik PDF</h2>

      <div class="contact">

                
              <form id="download-pdf" method="post" action="">
                <div class="form-group">
                    <label class="control-label col-md-5">Imię i nazwisko <span class="required">*</span></label>
                    <div class="col-md-7">
                      <input type="text" class="form-control" required name="name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-5">Adres e-mail <span class="required">*</span></label>
                    <div class="col-md-7">
                      <input type="email" class="form-control" required name="email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-5">Firma / Instytucja <span class="required">*</span></label>
                    <div class="col-md-7">
                      <input type="text" class="form-control" required name="company">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-5">Telefon <span class="required">*</span></label>
                    <div class="col-md-7">
                      <input type="text" class="form-control" required name="phone">
                    </div>
                </div>
                <div class="form-group checkbox">
                    <label class="control-label agreement">
                      <input type="checkbox" required>  Wyrażam zgodę na przechowywanie i przetwarzanie moich danych osobowych dla potrzeb procesu rejestracji zgłoszenia oraz w celach marketingowych dla firmy Explanator zgodnie z ustawą z dnia 29.08.1997 r. o ochronie danych osobowych (Dz.U. nr 133 poz. 883) oraz z ustawą z dnia 18.07.2002 r. o świadczeniu usług drogą elektroniczną. <span class="required">*</span>
                    </label>
                </div>

                <p class="small-text"><span class="">*</span> Pola oznaczone gwiazdką są wymagane.</p>

                <div class="form-group">
                    <input type="submit" name="sendform" class="btn btn-primary" value="Wyślij">
                </div>
            </form>
            <?php 

              if (!$name=='' && !$email==''){
                echo '
                  <div class="alert alert-success" role="alert">
                    <b>Sukces!</b> Fromularz został poprawnie wypełniony.
                  </div>';
                echo '<div class="form-group">';
                echo '<a href="'.$newFile.'" download class="btn btn-primary">Pobierz plik PDF</a>';
                echo '</div>';
              } 
              else {
                echo '
                  <div class="alert alert-danger" role="alert">
                  <b>Błąd!</b> Proszę wypełnić poprawnie formularz.
                  </div>';
              }

            ?>
        </div>
		</div>

	</div>
</div>
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/modernizr.custom.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery.placeholder.min.js"></script>
<script type="text/javascript">
  var _smid = "XXX";
  (function() {
    var sm = document.createElement('script'); sm.type = 'text/javascript'; sm.async = true;
    sm.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.salesmanago.pl/static/sm.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sm, s);
  })();
</script>
<?php 
  if (!$name=='' && !$email==''){
    echo '<script type="text/javascript">';
    echo "$('#download-pdf').hide();";
    echo '</script>';
  }
?>
</body>
</html>

