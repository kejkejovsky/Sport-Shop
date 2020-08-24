<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<title>Rejestracja</title>

</head>

<body>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href=".">Sklep sportowy</a>
	</nav>
<?php
	require_once("../config.php");
	date_default_timezone_set('Europe/Warsaw');
	function test_input($data) 
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	if(!isset($_SESSION['logged'])){
		$_SESSION['logged'] = 0;
	}
	
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	//Rejestracja
	if(isSet($_POST['rejestruj'])){
		$imie = test_input($_POST['imie']);
		$nazwisko = test_input($_POST['nazwisko']);
		$login = test_input($_POST['login']);
		$haslo = test_input($_POST['haslo']);
		if(isSet($_POST['rodzaj']))
			$rodzaj = test_input($_POST['rodzaj']);
		else
			$rodzaj = 'k';
		if($rodzaj == 'f'){
			$firma = test_input($_POST['firma']);
			$regon = test_input($_POST['regon']);
			$nip = test_input($_POST['nip']);
		}
		else{
			$firma = "";
			$regon = 0;
			$nip = 0;
		}
		$quer = "select * from klient where login = '$login';";
		$querexec = mysqli_query($baza, $quer);
		$coun = mysqli_num_rows($querexec);
		if($coun != 0){
			echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Użytkownik o takim loginie już istnieje</div>";
		}else{
			$add = "INSERT INTO klient values(NULL, '$imie', '$nazwisko', '$login', '$haslo', '$rodzaj', '$firma', $regon, $nip);";
			if(mysqli_query($baza, $add)){
				$_SESSION['logged'] = 1;
				$_SESSION['id_klienta'] = mysqli_insert_id($baza);
			}
			echo mysqli_error($baza);
		}
		
		
	}
	if($_SESSION['logged'] == 1)
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='index.php'";
		echo "</script>";
	}
?>

<br/><br/><br/><br/><br/>
<div class="row">
	<div class="col-sm-4"></div>
	<div class='col-sm-4'>
	<div class='form-group'>
<form action='rejestracja.php' method='POST'>
<input name='imie' class="form-control mr-sm-2" type='text'  placeholder='Imię' required /></div>
<div class='form-group'>
<input name='nazwisko' class="form-control mr-sm-2" type='text'  placeholder='Nazwisko' required /></div>
<div class='form-group'>
<input name='login' class="form-control mr-sm-2" type='text'  placeholder='Login' required /></div>
<div class='form-group'>
<input name='haslo' class="form-control mr-sm-2" type='password'  placeholder='Hasło' required /></div>
<div class='checkbox'>
<label><input name='rodzaj' id='rodzaj'  type='checkbox' value='f' onClick="enableDisable()"> Firma</label></div>
<div class='form-group'>
<input name='firma'class="form-control mr-sm-2" id='firma'  type='text'  placeholder='Nazwa' disabled/></div>
<div class='form-group'>
<input name='regon' class="form-control mr-sm-2" id='regon'  type='text'  placeholder='Regon' disabled/></div>
<div class='form-group'>
<input name='nip' class="form-control mr-sm-2" id='nip' type='text'  placeholder='NIP'  disabled/></div>
<div class='form-group'>
<button class="btn btn-success my-2 mr-sm-2" type='submit' name='rejestruj'>Rejestruj</button>
</form>
</div>
</div>
<div class="col-sm-4"></div>
</div>
<script type='text/javascript'>
function enableDisable() {
    var cb1 = document.getElementById('rodzaj').checked;
    document.getElementById('regon').disabled = !(cb1);
    document.getElementById('nip').disabled = !(cb1);
	document.getElementById('firma').disabled = !(cb1);
	document.getElementById('regon').required = !(cb1);
    document.getElementById('nip').required = !(cb1);
	document.getElementById('firma').required = !(cb1);
}
</script>
</body>
</html>