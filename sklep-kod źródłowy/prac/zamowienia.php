<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Zamówienia</title>
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
	$idprac = $_SESSION['id_pracownika'];
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	$pracownik_id = $_SESSION['id_pracownika'];
	//Opłacenie
	if(isSet($_POST['oplac']))
	{
		$item = test_input($_POST['item']);
		$query = "UPDATE zamowienia SET czy_oplacone='T' WHERE nr_zamowienia = $item;";
		mysqli_query($baza, $query);
		mysqli_errno($baza);
	}
	//Realizowanie
	if(isSet($_POST['realizuj']))
	{
		$item = test_input($_POST['item']);
		$zap = "select * from zamowienie_produkt where zamowienia_nr_zamowienia = $item;";
		$zapexec = mysqli_query($baza,$zap);
		while($row = mysqli_fetch_array($zapexec)){
			$zap2 = "SELECT * FROM stan_magazynowy WHERE produkt_nr_produktu = ".$row['produkt_nr_produktu']." and zasob >= ".$row['ilosc']." order by magazyn_nazwa;";
			$zap2Exec = mysqli_query($baza, $zap2);
			$zap2wyn = mysqli_fetch_array($zap2Exec);
			$cut = "call zmien_ilosc(".$row['produkt_nr_produktu'].", ".$row['ilosc'].",'".$zap2wyn['magazyn_nazwa']."' , 1);";
			mysqli_query($baza, $cut);
		}
		$query = "UPDATE zamowienia SET czy_zrealizowane='T', data_realizacji=CURRENT_DATE, data_wysylki=CURRENT_DATE WHERE nr_zamowienia = $item;";
		mysqli_query($baza, $query);
		mysqli_errno($baza);
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
<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Nr zamówienia</th>
	<th>Data złożenia</th>
	<th>Data przyjęcia</th>
	<th>Opłacone</th>
	<th>Zrealizowane</th>
	<th>Data realizacji</th>
	<th>Data wysyłki</th>
	<th>Forma płatności</th>
	<th>Dane klienta</th>
</tr>

	
<?php	
	$zapytanie = "SELECT * FROM zamowienia order by 1 DESC;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		$nrzam = $rowa[0];
		$zap = "SELECT * FROM klient where id_klienta=$rowa[8]";
		$zapexec = mysqli_query($baza, $zap);
		$klient = mysqli_fetch_array($zapexec);
		$numberRows=0;
		if($rowa[5] != 'T'){
			$prod = "SELECT * from zamowienie_produkt where zamowienia_nr_zamowienia=$nrzam;";
			$prodexe = mysqli_query($baza,$prod);
			while($prodwyn = mysqli_fetch_array($prodexe)){
				$zap2 = "SELECT * FROM stan_magazynowy WHERE produkt_nr_produktu =".$prodwyn['produkt_nr_produktu']." and zasob >= ".$prodwyn['ilosc'].";";
				if($nrzam==3){ echo $zap2;}
				$zap2Exec = mysqli_query($baza, $zap2);
				if(mysqli_num_rows($zap2Exec) == 0) {$numberRows = 1;}
			}
		}
		
		
		echo "<tr><td><a href='zamowieniaPokaz.php?id=$rowa[0]'>$rowa[0]</a></td><td>$rowa[1]</td><td>$rowa[2]</td>";
		if($rowa[3] == 'T')
			echo "<td>TAK</td>";
		else{
			echo "<form action='zamowienia.php' method='POST'><input type='hidden' name='item' value='$nrzam'/><td><button class='btn btn-success my-2 mr-sm-2' type='submit' name='oplac'>Opłać</button></td>";
			echo "</form>";
		}
		
		
		if($rowa[5] == 'T')
			echo "<td>TAK</td>";
		else{
			echo "<form action='zamowienia.php' method='POST'><input type='hidden' name='item' value='$nrzam'/><td><button class='btn btn-success my-2 mr-sm-2' type='submit' name='realizuj'";
			if ($numberRows != 0 || $rowa[3] != 'T') { echo " disabled"; }
			echo ">Zrealizuj</button></td>";
			echo "</form>";
		}
		echo "<td>$rowa[6]</td>";
		echo "<td>$rowa[4]</td>";
		if($rowa[7] == 'K')
		{
			echo "<td>Przelew</td>";
		}
		else
		{
			echo "<td>Gotówka przy odbiorze</td>";
		}
		if($klient['rodzaj_klienta'] == 'k')
		{
			echo "<td>".$klient['imie']." ".$klient['nazwisko']."</td>";
		}
		else
		{
			echo "<td>Firma ".$klient['nazwa_firmy']."</td>";
		}
		echo '</tr>';
	}
?>
</table>

</body>
</html>