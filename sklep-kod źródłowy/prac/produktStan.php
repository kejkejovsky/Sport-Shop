<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Stan produktu</title>
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
	if($_SESSION['zalogowany'] == 0)
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='logowanie.php'";
		echo "</script>";
	}
	
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM produkt WHERE nr_produktu = $idProd;";
			$zap4Exec = mysqli_query($baza, $zap4);
			$row = mysqli_fetch_array($zap4Exec);
			$name = $row[1];
			$roz = $row['rozmiar'];
			$zap5 = "select * from kategoria where kategoria_id = ".$row['kategoria_kategoria_id'].";";
			$zap5exe = mysqli_query($baza, $zap5);
			$zap5wyn = mysqli_fetch_array($zap5exe);
			$kat = $zap5wyn['nazwa_kategorii'];
			$zap6 = "select * from producent where producent_id = ".$row['producent_producent_id'].";";
			$zap6exe = mysqli_query($baza, $zap6);
			$zap6wyn = mysqli_fetch_array($zap6exe);
			$prod = $zap6wyn['nazwa_producenta'];
		}
	else
	{
		echo "<script type='text/javascript'>";
		echo "window.location ='produkt.php'";
		echo "</script>";
	}
	
	
?>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href=".">Sklep sportowy</a>
  
	<!-- Links -->
	<ul class="navbar-nav mr-auto">
		<li class='nav-item'><a class='nav-link' href='produkt.php'>Powrót</a>
		</li>
		<?php
			echo "<li class='nav-item'>
			<form action='logowanie.php' method='POST'>
			<input type='hidden' name='wyloguj' value='wyloguj'/>
			<a class='nav-link' href='javascript:;' onclick='parentNode.submit();'>Wyloguj</a></form>
		</li>";
		?>
		
	</ul>
</nav>
<?php 
//Uzupełnij zasób
	if(isSet($_POST['uzupelnij']))
	{
		$id_produkt = test_input($_POST['item2']);
		$mag = test_input($_POST['magazyn']);
		$ilosc = test_input($_POST['ilosc']);
		$upd = "call zmien_ilosc($id_produkt, $ilosc, '$mag', 0);";
		if(mysqli_query($baza, $upd)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zwiększono stan produktu</div>";
		}
		echo mysqli_error($baza);
	}
?>
<div class='row'>
<div class='col-sm-12'>
<?php 
	echo "<h2>Stan $name rozmiar $roz w magazynach</h2>";
	echo "<h4>Producent: $prod</h4>";
	echo "<h4>Kategoria: $kat</h4>";
 ?>
</div>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Magazyn</th>
	<th>Ilość</th>
	<th>Ostatnia zmiana</th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM stan_magazynowy where produkt_nr_produktu=$idProd order by 4;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<tr><td>$lp</td><td>$rowa[3]</td><td>$rowa[1]</td><td>$rowa[0]</td></tr>";
		$lp++;
	}
?>


<form action='produktStan.php?id=<?php echo $idProd; ?>' method='POST'>
<tr><td></td><td>
<select class='form-control mr-sm-2' name='magazyn'>
<?php 
	$zapytanie = "SELECT * FROM magazyn order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<option value='$rowa[0]'>$rowa[0]</option>";
	}
?>
</select></td><td>
<input name='ilosc' class='form-control mr-sm-2' type='number'  placeholder='Ilość' value=1 min=1 required /></td><td>
<?php echo "<input type='hidden' name='item2' value='$idProd'  />";?>
<button class='btn btn-success my-2 mr-sm-2' type='submit' name='uzupelnij'>+</button></td></tr>
</form>
</table>
</div>
</div>
</body>
</html>