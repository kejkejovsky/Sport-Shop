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
	if($_SESSION['logged']==0){
		echo "<script type='text/javascript'>";
		echo "window.location ='logowanie.php'";
		echo "</script>";
	}
	
	if(!isset($_SESSION['koszyk'])){
		$_SESSION['koszyk'] = array();
		$_SESSION['liczba_prod'] = 0;
	}
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	$id_klienta = $_SESSION['id_klienta'];
	

	//Dodawanie/Modyfikacja kontaktu
	if(isSet($_POST['dodaj_producent']))
	{
		$item = test_input($_POST['item']);
		$prod_nazwa = test_input($_POST['prod_nazwa']);
		$cena_n = test_input($_POST['cena_n']);
		$item = test_input($_POST['item']);
		$check = "select * from kontakt_klient where nr_telefonu='$prod_nazwa' and email='$cena_n' and klient_id_klienta=$id_klienta;";
		$checkexec = mysqli_query($baza, $check);
		if(mysqli_num_rows($checkexec)==0)
		{
			if($item == 0)
			{
				$query = "INSERT INTO kontakt_klient VALUES (NULL, $prod_nazwa, '$cena_n', $id_klienta);";
				if(mysqli_query($baza, $query)) {
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano dane kontaktowe</div>";
				}
				echo mysqli_error($baza);
			}
			else
			{
				$query = "UPDATE kontakt_klient SET nr_telefonu=$prod_nazwa, email='$cena_n' WHERE id_kontaktu = $item and klient_id_klienta=$id_klienta;";
				if(mysqli_query($baza, $query)) {
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano dane kontaktowe</div>";
				}
				echo mysqli_error($baza);
			}
		}
		else
		{
			echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dane kontaktowe już istnieją</div>";
		}
	}
	//Usuwanie kontaktu
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$query = "DELETE FROM kontakt_klient WHERE id_kontaktu = $item and klient_id_klienta=$id_klienta;";
		if(mysqli_query($baza, $query)) 
		{
			echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto dane kontaktowe</div>";
		}
		echo mysqli_error($baza);
	}
	//Dodawanie/Modyfikacja adresu
	if(isSet($_POST['dodaj_adres']))
	{
		$item = test_input($_POST['item']);
		$miasto = test_input($_POST['miasto']);
		$kod = test_input($_POST['kod']);
		$ulica = test_input($_POST['ulica']);
		$nr_domu = test_input($_POST['nr_domu']);
		$nr_mieszkania = test_input($_POST['nr_mieszkania']);
		$check = "select * from adres where miasto='$miasto' and kod_pocztowy='$kod' and ulica='$ulica' and nr_domu='$nr_domu' and nr_mieszkania=$nr_mieszkania and klient_id_klienta=$id_klienta;";
		if(empty($nr_mieszkania))
		{
			$nr_mieszkania = 'NULL';
			$check = "select * from adres where miasto='$miasto' and kod_pocztowy='$kod' and ulica='$ulica' and nr_domu='$nr_domu' and nr_mieszkania is null and klient_id_klienta=$id_klienta;";
		}
		
		$checkexec = mysqli_query($baza, $check);
		if(mysqli_num_rows($checkexec)==0)
		{
			if($item == 0)
			{
				$newadr = "INSERT INTO adres values(NULL,'$miasto','$kod','$ulica','$nr_domu',$nr_mieszkania,$id_klienta);";
				if(mysqli_query($baza, $newadr)) 
				{
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano adres</div>";
				}
				echo mysqli_error($baza);
			}
			else
			{
				$newadr = "UPDATE adres SET miasto='$miasto',kod_pocztowy='$kod',ulica='$ulica',nr_domu='$nr_domu',nr_mieszkania=$nr_mieszkania where klient_id_klienta=$id_klienta and id_adresu=$item;";
				if(mysqli_query($baza, $newadr)) 
				{
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano adres</div>";
				}
				echo mysqli_error($baza);
			}
		}
		else
		{
			echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Adres już istnieje</div>";
		}
	}
	//Usuwanie adresu
	if(isSet($_GET['adresdel']))
	{
		$item = test_input($_GET['adresdel']);
		$query = "DELETE FROM adres WHERE id_adresu = $item and klient_id_klienta=$id_klienta;";
		if(mysqli_query($baza, $query)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto adres</div>";
		}
		echo mysqli_error($baza);
	}
	?>

<div class='row'>
	<div class="col-sm-2"></div>
	<div class="col-sm-8">
	<?php $dane = "select * from klient where id_klienta=$id_klienta;";
	$daneexec=mysqli_query($baza, $dane);
	$dane_tekst =mysqli_fetch_array($daneexec);
	echo "<h1>Witaj ".$dane_tekst['imie']." ".$dane_tekst['nazwisko']."</h1>";
	if($dane_tekst['rodzaj_klienta']=='f'){
		echo "<h3>Firma ".$dane_tekst['nazwa_firmy']."</h3><h5>NIP: ".$dane_tekst['nip']."</h5><h5>Regon: ".$dane_tekst['regon']."</h5>";
	}
	
	?>
	
		<h2>Lista zamówień</h2>
		<table class='table table-striped table-hover'>
		<tr>
			<th>Lp.</th>
			<th>Nr zamówienia</th>
			<th>Status</th>
			<th>Cena</th>
			
		</tr>
		<?php 
		
		$zapytanie = "SELECT * FROM zamowienia where klient_id_klienta=$id_klienta order by 1 DESC;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		$lp = 1;
		while($rowa = mysqli_fetch_array($zap1)) 
		{
			$zap2 = "SELECT * FROM faktura_sprzedazy WHERE zamowienia_nr_zamowienia = $rowa[0];";
			$zap2Exec = mysqli_query($baza, $zap2);
			$koszt = mysqli_fetch_array($zap2Exec);
			echo "<tr><td>$lp</td><td><a href='zamowienie.php?id=$rowa[0]'>$rowa[0]</a></td><td>";
			if($rowa['czy_oplacone']=='T'){
				if($rowa['czy_zrealizowane']=='T'){
					echo "Wysłane</td>";
				}else{
					echo "Realizowane</td>";
				}
			}else{
				echo "Nieopłacone</td>";
			}
			echo "<td>".$koszt['wartosc_brutto']." zł</td>";
			$lp++;
			
			echo "</tr>";
		}
		
		?>
		</table>
		</div>
		<div class="col-sm-2">
</div>
</div>
<div class='row'>
<div class="col-sm-2"></div>

<div class="col-sm-8">
<h2>Dane kontaktowe</h2>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Nr telefonu</th>
	<th>Email</th>
	<th></th>
	<th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM kontakt_klient where klient_id_klienta = $id_klienta order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<tr><td>$lp</td><td>$rowa[1]</td><td>$rowa[2]</td>";
		echo "<td><a href='konto.php?id=$rowa[0]'>Edycja</a></td>";
		echo "<td><a href='konto.php?del=$rowa[0]'>Usuń</a></td>";
		echo '</tr>';
		$lp++;
	}
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM kontakt_klient WHERE id_kontaktu = $idProd and klient_id_klienta = $id_klienta;";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$name = $row[1];
				$cena_n = $row[2];;
			}
		} 
		else 
		{ 
			$idProd = 0;
			$name = "";
			$cena_n = "";
		}
		
		echo "<form action='konto.php' method='POST'>";
				
		echo "<tr>
				<td></td>
				<td><input class='form-control mr-sm-2' name='prod_nazwa'  type='text' pattern='[0-9]*' placeholder='Nr telefonu' value='$name' required /></td>
				<td><input class='form-control mr-sm-2' name='cena_n'  type='text'  placeholder='Email' value='$cena_n' required /></td>
				";
		
		echo "	<input type='hidden' name='item' value='$idProd' />
				<td><button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_producent'>+</button></td><td></td></tr>";
		
		echo "</form>";	
		
?>
</table>
</div>
<div class="col-sm-2"></div>
</div>
<div class="row">
<div class="col-sm-2">
</div>
<div class="col-sm-8">
<h2>Adresy</h2>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Miasto</th>
	<th>Kod</th>
	<th>Ulica</th>
	<th>Numer domu</th>
	<th>Numer mieszkania</th>
	<th></th>
	<th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM adres where klient_id_klienta = $id_klienta order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<tr><td>$lp</td><td>$rowa[1]</td><td>$rowa[2]</td><td>$rowa[3]</td><td>$rowa[4]</td><td>$rowa[5]</td>";
		echo "<td><a href='konto.php?adresid=$rowa[0]'>Edycja</a></td>";
		echo "<td><a href='konto.php?adresdel=$rowa[0]'>Usuń</a></td>";
		echo '</tr>';
		$lp++;
	}
	if(isSet($_GET['adresid'])) 
		{ 
			$idProd = test_input($_GET['adresid']); 
			$zap4 = "SELECT * FROM adres WHERE id_adresu = $idProd and klient_id_klienta = $id_klienta;";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$miasto = $row['miasto'];
				$kod = $row['kod_pocztowy'];
				$ulica = $row['ulica'];
				$dom = $row['nr_domu'];
				$miesz = $row['nr_mieszkania'];
			}
		} 
		else 
		{ 
			$idProd = 0;
			$miasto = "";
			$kod = "";
			$ulica = "";
			$dom = "";
			$miesz = "";
		}
		
		echo "<form action='konto.php' method='POST'>";
				
		echo "<tr>
				<td></td>
				<td><input class='form-control mr-sm-2' name='miasto'  type='text'  placeholder='Miasto' value='$miasto' required /></td>
				<td><input class='form-control mr-sm-2' name='kod'  type='text'  placeholder='Kod pocztowy' value='$kod' required /></td>
				<td><input class='form-control mr-sm-2' name='ulica'  type='text'  placeholder='Ulica' value='$ulica' required /></td>
				<td><input class='form-control mr-sm-2' name='nr_domu'  type='text'  placeholder='Numer domu' value='$dom' required /></td>
				<td><input class='form-control mr-sm-2' name='nr_mieszkania'  type='text' pattern='[0-9]*'  placeholder='Numer mieszkania' title='Proszę wprowadzić liczbę' value='$miesz' /></td>
				";
		
		echo "	<input type='hidden' name='item' value='$idProd' />
				<td><button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_adres'>+</button></td><td></td></tr>";
		
		echo "</form>";	
		
?>
</table>
</div>
<div class="col-sm-2"></div>
</div>
</body>
</html>