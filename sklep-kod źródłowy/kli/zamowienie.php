<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <title>Sklep</title>
  <meta charset="utf-8">
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
	if($_SESSION['logged'] == 0)
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='logowanie.php'";
		echo "</script>";
	}
	
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	if(isSet($_GET['id'])) 
		{ 
			$id_zam = test_input($_GET['id']); 
		}
	else
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='konto.php'";
		echo "</script>";
	}
	
?>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href=".">Sklep sportowy</a>
  
	<!-- Links -->
	<ul class="navbar-nav mr-auto">
		
		<li class="nav-item">
			<a class="nav-link" href="koszyk.php"<?php if($_SESSION['liczba_prod']==0) echo "style = 'pointer-events: none;'";?>>Koszyk<span class="badge"><?php echo $_SESSION['liczba_prod']; ?></span></a>
		</li>
		<?php
		if($_SESSION['logged'] == 0)
		{
			echo "<li class='nav-item'>
			<a class='nav-link' href='logowanie.php'>Logowanie</a>
		</li>";
		}
		else{
			echo "<li class='nav-item'>
			<form action='logowanie.php' method='POST'>
			<input type='hidden' name='wyloguj' value='wyloguj'/>
			<a class='nav-link' href='konto.php'>Konto</a></form>
		</li>";
			echo "<li class='nav-item'>
			<form action='logowanie.php' method='POST'>
			<input type='hidden' name='wyloguj' value='wyloguj'/>
			<a class='nav-link' href='javascript:;' onclick='parentNode.submit();'>Wyloguj</a></form>
		</li>";
		}
		?>
		
	</ul>
</nav>
<div class='row'>
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
<?php echo "<h2>Zamówienie nr $id_zam</h2>"; ?>

<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Nazwa</th>
	<th>Rozmiar</th>
	<th>Ilość</th>
	<th>Cena</th>
</tr>

	
<?php	
	
	$zap = "SELECT * FROM produkt p, zamowienie_produkt z where p.nr_produktu=z.produkt_nr_produktu and z.zamowienia_nr_zamowienia=$id_zam;";		
	$zap1 = mysqli_query($baza, $zap);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<tr><td>$lp</td><td>".$rowa['nazwa']."</td><td>".$rowa['rozmiar']."</td><td>".$rowa['ilosc']."</td><td>".($rowa['cena_brutto']*$rowa['ilosc'])." zł</td></tr>";
		$lp++;
	}
?>


<?php 
	$zapytanie = "SELECT * FROM faktura_sprzedazy where zamowienia_nr_zamowienia = $id_zam order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$rowa = mysqli_fetch_array($zap1);
	$zap2 = "SELECT * FROM zamowienia where nr_zamowienia = $id_zam order by 1;";		
	$zap2exe = mysqli_query($baza, $zap2);
	$zap2wyn = mysqli_fetch_array($zap2exe);
	echo "<tr><td></td><td></td><td></td><td>Data zamówienia:</td><td>$rowa[1]</td></tr>";
	echo "<tr><td></td><td></td><td></td><td>Forma płatności:</td><td>";
	if($zap2wyn['forma_platnosci'] == 'K')
	{
		echo "Przelew";
	}
	else
	{
		echo "Gotówka przy odbiorze";
	}
	echo "</td></tr>";
	echo "<tr><td></td><td></td><td></td><td>Wartość netto:</td><td>$rowa[2] zł</td></tr>";
	echo "<tr><td></td><td></td><td></td><td>Wartość VAT:</td><td>$rowa[4] zł</td></tr>";
	echo "<tr><td></td><td></td><td></td><td>Wartość brutto:</td><td>$rowa[3] zł</td></tr>";
?>

</table>
</body>
</html>