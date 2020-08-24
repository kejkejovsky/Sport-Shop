<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Koszyk</title>
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
	if($_SESSION['logged']==0){
		echo "<script type='text/javascript'>";
		echo "window.location ='logowanie.php'";
		echo "</script>";
	}
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	if(isSet($_GET['del'])){
		$id = test_input($_GET['del']);
		$arr = $_SESSION['koszyk'];
		unset($arr[$id]);
		$_SESSION['liczba_prod']--;
		$_SESSION['koszyk']=array_values($arr);
	}
	if(isSet($_POST['zamow'])){
		$koszyk = $_SESSION['koszyk'];
		$id_klienta = $_SESSION['id_klienta'];
		$plat = $_POST['plat'];
		if($plat == 'K') $oplacone = 'N';
		else $oplacone = 'T';
		$zap = "INSERT INTO zamowienia values(NULL, CURRENT_DATE, CURRENT_DATE, '$oplacone', NULL, 'N', NULL, '$plat', $id_klienta);";
		mysqli_query($baza, $zap);
		$last = mysqli_insert_id($baza);
		$adres = test_input($_POST['adres']);
		if($adres==0){
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
				$newadr = "INSERT INTO adres values(NULL,'$miasto','$kod','$ulica','$nr_domu',$nr_mieszkania,$id_klienta);";
				if(mysqli_query($baza, $newadr)) 
				{
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano adres</div>";
				}
				echo mysqli_error($baza);
			}
		}
		for($i=0;$i<count($koszyk);$i++){
			$produkt = "select * from produkt where nazwa='".$koszyk[$i][0]."' and rozmiar = '".$koszyk[$i][1]."' and kategoria_kategoria_id = ".$koszyk[$i][3]." and producent_producent_id=".$koszyk[$i][4].";";
			$proexec = mysqli_query($baza, $produkt);
			$row = mysqli_fetch_array($proexec);
			$dodaj = "INSERT INTO zamowienie_produkt values(".$koszyk[$i][2].",NULL,$last,$row[0]);";
			mysqli_query($baza,$dodaj);
			
		}
		$fak = "INSERT INTO faktura_sprzedazy values(NULL, CURRENT_DATE, wartosc($last,0), wartosc($last,1), wartosc($last,1)-wartosc($last,0), $last);";
		mysqli_query($baza, $fak);
		$koszyk = array();
		$_SESSION['koszyk'] = $koszyk;
		$_SESSION['liczba_prod']=0;
	}
	if($_SESSION['liczba_prod']==0){
		echo "<script type='text/javascript'>";
		echo "window.location ='index.php'";
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
			<a class='nav-link' href='konto.php'>Konto</a>
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
<br/>
<div class='row'>
<div class="col-sm-2"></div>
<div class="col-sm-8">
<div class="col-sm-12">
<table class='table table-striped table-hover'>
<tr>
			<th>Lp.</th>
			<th>Nazwa</th>
			<th>Rozmiar</th>
			<th>Ilość</th>
			<th>Cena</th>
			<th></th>
		</tr>
<?php 
	$koszyk = $_SESSION['koszyk'];
	$sum = 0;
	for($i=0; $i<count($koszyk); $i++){
		$zap = "SELECT * FROM produkt where nazwa = '".$koszyk[$i][0]."' and rozmiar = '".$koszyk[$i][1]."' and kategoria_kategoria_id = ".$koszyk[$i][3]." and producent_producent_id=".$koszyk[$i][4].";";
		$zapExec = mysqli_query($baza, $zap);
		$row = mysqli_fetch_array($zapExec);
		$sum += $koszyk[$i][2] * $row['cena_brutto'];
		$lp = $i + 1;
		echo "<tr><td>$lp</td><td>".$koszyk[$i][0]."</td><td>".$koszyk[$i][1]."</td><td>".$koszyk[$i][2]."</td><td>".($koszyk[$i][2] * $row['cena_brutto'])." zł</td>";
		echo "<td><a href='koszyk.php?del=$i'>Usuń</a></td>";
		echo "</tr>";
	}
?>
<tr><td></td><td></td><td></td><td>Suma</td><td><?php echo $sum." zł";?></td><td></td></tr>
</table>
</div>

<br/>
<div class='col-sm-12'>
<h5>Adres wysyłki</h5></div>
<div class='col-sm-12'>
<form action='koszyk.php' method='POST'>
<?php 
	$zap1 = "Select * from adres where klient_id_klienta=".$_SESSION['id_klienta'];
	$zap1exec = mysqli_query($baza, $zap1);
	$num = mysqli_num_rows($zap1exec);
	while($row = mysqli_fetch_array($zap1exec)){
		echo "<div class='radio'><label><input type='radio' name='adres' onclick='enableDisable()' value=$row[0]/> $row[2] $row[1] $row[3] $row[4]";
		if(!empty($row[5])) echo "/$row[5]";
		echo "</label></div>";
	}
?>
<div class="radio">
<label><input type='radio' name='adres' id='new' onclick='enableDisable()' value='0' checked>Nowy adres</label>
<script type='text/javascript'>
function enableDisable() {
    var cb1 = document.getElementById('new').checked;
    document.getElementById('kod').disabled = !(cb1);
    document.getElementById('miasto').disabled = !(cb1);
	document.getElementById('ulica').disabled = !(cb1);
	document.getElementById('nr_domu').disabled = !(cb1);
	document.getElementById('nr_mieszkania').disabled = !(cb1);
	document.getElementById('kod').required = cb1;
    document.getElementById('miasto').required = cb1;
	document.getElementById('ulica').required = cb1;
	document.getElementById('nr_domu').required = cb1;
}
</script>
<div class='form-group form-inline'><div class="form-group">
<input name='kod' id='kod' type='text'  placeholder='Kod pocztowy' required /></div>&nbsp;
<div class="form-group">
<input name='miasto' id='miasto' type='text'  placeholder='Miasto' required /></div>&nbsp;
<div class="form-group">
<input name='ulica' id='ulica' type='text'  placeholder='Ulica' required /></div>&nbsp;
<div class="form-group">
<input name='nr_domu' id='nr_domu' type='text'  placeholder='Numer domu' required /></div>&nbsp;
<div class="form-group">
<input name='nr_mieszkania' id='nr_mieszkania' type='text' pattern="[0-9]*" title='Proszę wprowadzić liczbę' placeholder='Numer mieszkania' /></div>
</div>
</div>
</div>
<div class='col-sm-12'>
<h5>Rodzaj płatności</h5></div>
<div class='col-sm-12'>
<div class="radio">
<label><input type='radio' name='plat' value='K' checked>Przelew</label></div>
<div class="radio">
<label><input type='radio' name='plat' value='G'>Gotówka przy odbiorze</label></div>
</div>
<div class='col-sm-12'>
<button class="btn btn-success my-2 mr-sm-2" type='submit' name='zamow'>Zamów</button>
</form>
</div>
</body>
</html>