<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - zarządzanie</title>
	  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
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
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	if($_SESSION['zalogowany'] == 0)
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='logowanie.php'";
		echo "</script>";
	}
?>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href=".">Sklep sportowy</a>
  
	<!-- Links -->
	<ul class="navbar-nav mr-auto">
		
		<?php
			echo "<li class='nav-item'>
			<form action='logowanie.php' method='POST'>
			<input type='hidden' name='wyloguj' value='wyloguj'/>
			<a class='nav-link' href='javascript:;' onclick='parentNode.submit();'>Wyloguj</a></form>
		</li>";
		?>
		
	</ul>
</nav>
<br/>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<div class='col-sm-12'>
<div class='row'>
<div class='col-sm-3'>
<a href="pracownicy.php" class='btn btn-success btn-lg' role='button'><?php if($_SESSION['admin'] == 1) echo "Pracownicy"; else echo "Pracownik"; ?></a></div>
<div class='col-sm-3'>
<a href="kontakt.php" class='btn btn-success btn-lg' role='button'>Kontakt</a></div>
<div class='col-sm-3'>
<a href="producent.php" class='btn btn-success btn-lg' role='button'>Producenci</a></div>
</div>
<br/>
<div class='row'>
<div class='col-sm-3'>
<a href="kategoria.php" class='btn btn-success btn-lg' role='button' role='button'>Kategorie</a></div>
<div class='col-sm-3'>
<a href="magazyn.php" class='btn btn-success btn-lg' role='button'>Magazyny</a></div>
<div class='col-sm-3'>
<a href="produkt.php" class='btn btn-success btn-lg' role='button'>Produkt</a></div>
<div class='col-sm-3'>
<a href="zamowienia.php" class='btn btn-success btn-lg' role='button'>Zamówienia</a></div>
</div>
</div>
</div>
</div>
</body>

</html>