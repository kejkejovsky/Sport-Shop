<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Logowanie</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
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
	if(!isset($_SESSION['zalogowany'])){
		$_SESSION['zalogowany'] = 0;
	}
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	//Logowanie
	if(isSet($_POST['logowanie']))
	{
		if(isSet($_POST['login']))		{		$log = test_input($_POST['login']);		}
		else							{		$log = "";					}
		
		if(isSet($_POST['haslo']))		{		$has = test_input($_POST['haslo']);		}
		else							{		$has = "";					}
		$zap = "SELECT * FROM pracownik WHERE login = '$log';";
		$zapExec = mysqli_query($baza, $zap);
		$row = mysqli_fetch_array($zapExec);
		if(strcmp($row['login'], $log)==0){
			if(strcmp($row['haslo'], $has)==0){
				$_SESSION['zalogowany'] = 1;
				$_SESSION['id_pracownika'] = $row['id_pracownika'];
				if(strcmp($log,'admin')==0){ $_SESSION['admin']=1;}
			}
			else { echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Błędne hasło</div>"; }
		}
		else { echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Błędny login</div>"; }
		mysqli_errno($baza);
	}
	//Wylogowanie
	if(isSet($_POST['wyloguj']))
	{
		$_SESSION['zalogowany'] = 0;
		$_SESSION['id_pracownika'] = 0;
		$_SESSION['admin']=0;
		echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Pomyślnie wylogowano</div>";
	}
	
	if($_SESSION['zalogowany'] == 1)
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='index.php'";
		echo "</script>";
	}
?>

<br/><br/><br/><br/><br/><br/><br/><br/>
<div class="row">
	<div class="col-sm-4"></div>
	<div class='col-sm-4'><div id='ramka'>
<form action='logowanie.php' method='POST'>
<div class='form-group'>
<input name='login' class="form-control mr-sm-2" type='text'  placeholder='Login' required /></div>
<div class='form-group'>
<input name='haslo' class="form-control mr-sm-2" type='password'  placeholder='Hasło' required /></div>
<div class='form-group'>
<button class="btn btn-success my-2 mr-sm-2" type='submit'  name='logowanie'>Zaloguj</button>
</div></div>
</form>
</div>
<div class="col-sm-4"></div>

</body>
</html>