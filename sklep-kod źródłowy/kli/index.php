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
  <style>
	#ramka {
  transition: box-shadow .3s;
  padding: 20px;
  margin: 5px;
  border-radius:10px;
  border: 1px solid #ccc;
  background: #fff;
  float: left;
  
}
#ramka:hover {
  box-shadow: 0 0 11px rgba(33,33,33,.2); 
}#ramka2 {
  transition: box-shadow .3s;
  padding: 40px;
  margin: 5px;
  border-radius:10px;
  border: 1px solid #ccc;
  background: #fff;
  float: left;
  
}
#ramka2:hover {
  box-shadow: 0 0 11px rgba(33,33,33,.2); 
}

  </style>
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
	if(!isset($_SESSION['logged'])){
		$_SESSION['logged'] = 0;
	}
	if(!isset($_SESSION['koszyk'])){
		$_SESSION['koszyk'] = array();
		$_SESSION['liczba_prod'] = 0;
	}
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	$prod = "";
	$kat = "";
	$prod1 = "";
	$kat1 = "";
	$cena_min = "";
	$cena_max = "";
	if(isSet($_POST['dodaj'])){
		$koszyk = $_SESSION['koszyk'];
		$coun = $_SESSION['liczba_prod'];
		$nazwa = test_input($_POST['item']);
		$kat = test_input($_POST['item1']);
		$prod = test_input($_POST['item2']);
		$rozmiar = test_input($_POST['rozmiar']);
		$ilosc = test_input($_POST['ilosc']);
		$is = 0;
		for($i=0; $i<count($koszyk); $i++){
			if(strcmp($koszyk[$i][0], $nazwa)==0 && strcmp($koszyk[$i][1],$rozmiar)==0 && $koszyk[$i][3]==$kat && $koszyk[$i][4]==$prod){
				$koszyk[$i][2] += $ilosc;
				$is = 1;
				break;
			}
		}
		if($is == 0){
			$koszyk[$coun] = array($nazwa, $rozmiar, $ilosc, $kat, $prod);
			$coun++;
		}
		$_SESSION['koszyk'] = $koszyk;
		$_SESSION['liczba_prod'] = $coun;
		
	}
	?>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
	<!-- Brand/logo -->
	<a class="navbar-brand" href=".">Sklep sportowy</a>
  
	<!-- Links -->
	<ul class="navbar-nav mr-auto">
		
		<li class="nav-item">
			<a class="nav-link" href="koszyk.php"<?php if($_SESSION['liczba_prod']==0) echo "style = 'pointer-events: none;'";?>>Koszyk<span class="badge<?php if($_SESSION['liczba_prod']>0) echo " badge-error"; ?>"><?php echo $_SESSION['liczba_prod']; ?></span></a>
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

	<div class="row">
	<div class="col-sm-2">
	<br/>
	<div class="col-sm-12">
	<form action="index.php" method="POST">
		<div class="form-group">
			<input class="form-control mr-sm-2" type="text" placeholder="Szukaj" name='tekst' <?php 
			if(isSet($_POST['szukaj'])){
				$nazwa = test_input($_POST['tekst']);
				echo "value = '$nazwa'"; }
		?> >
			<button class="btn btn-success my-2 mr-sm-2" name='szukaj'type="submit">Szukaj</button>
		</div>
	</form>
	</div>
	<div class="col-sm-12">
	<form action="index.php" method='POST'>
	<div id='ramka'>
	<?php 
	if(isSet($_POST['filtruj']) || isSet($_POST['dodaj'])){
		$kat1 = test_input($_POST['kategoria']);
		$prod1 = test_input($_POST['producent']);
		$cena_min = test_input($_POST['min']);
		$cena_max = test_input($_POST['max']);
	}
		?>
	Filtruj
		<div class="form-group">
			<select class="custom-select" name='kategoria'>
			<option value='' selected>Kategoria</option>
			<?php 
			$zapytanie = "SELECT * FROM kategoria order by 2;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		while($rowa = mysqli_fetch_array($zap1)) 
		{
				echo "<option value='$rowa[0]' ";
				if($kat1 == $rowa[0]) { echo 'selected'; }
				echo ">$rowa[1]</option>";
		}
		echo "</select>";
			?>
			</select>
		</div>
		<div class="form-group">
		<select class="custom-select" name='producent'>
			<option value='' selected>Producent</option>
			<?php 
			$zapytanie = "SELECT * FROM producent order by 2;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		while($rowa = mysqli_fetch_array($zap1)) 
		{
				echo "<option value='$rowa[0]'";
				if($prod1 == $rowa[0]) { echo 'selected'; }
				echo ">$rowa[1]</option>";
		}
		echo "</select>";
			?>
		</div>
		<div class="form-group">
			Cena: <input class="form-control mr-sm-2" type="number" placeholder='od' name='min' min=0 step=0.01 <?php if(!empty($cena_min)) { echo "value = '$cena_min'"; } ?>> 
			</div><div class="form-group">
			<input class="form-control mr-sm-2" placeholder='do'type="number" name='max' min=0 step=0.01 <?php if(!empty($cena_max)) { echo "value = '$cena_max'"; } ?>>
		</div>

		<button class="btn btn-success my-2 mr-sm-2" name='filtruj' type="submit">Filtruj</button>
	</form>
	</div>
	</div>
	</div>
	<!-- php show other  -->
	<div class="col-sm-10">
	
	<div class="row">
	<div class="col-sm-12"><h2>Lista produktów</h2></div>
	<div class="col-sm-12">
	 <?php 
	if(isSet($_POST['filtruj']) || isSet($_POST['dodaj'])){
		$zapytanie = "SELECT DISTINCT nazwa, kategoria_kategoria_id, producent_producent_id  FROM produkt join stan_magazynowy on nr_produktu = produkt_nr_produktu where zasob>0";
		if(!empty($kat1)){
				$zapytanie = $zapytanie." and kategoria_kategoria_id=$kat1";
			
		}
		if(!empty($prod1)){
				$zapytanie = $zapytanie." and producent_producent_id=$prod1";
			
		}
		if(!empty($cena_min)){
				$zapytanie = $zapytanie." and cena_brutto>=$cena_min";
		}
		if(!empty($cena_max)){
				$zapytanie = $zapytanie." and cena_brutto<=$cena_max";
		}
		$zapytanie = $zapytanie." order by 2,1,3;";
	}
	elseif(isSet($_POST['szukaj']) ){
		$nazwa = test_input($_POST['tekst']);
		$zapytanie = "SELECT DISTINCT nazwa, kategoria_kategoria_id, producent_producent_id FROM produkt join stan_magazynowy on nr_produktu = produkt_nr_produktu where zasob>0 and nazwa like '%$nazwa%' order by 2,1,3;";
	}
	else{
		$zapytanie = "SELECT DISTINCT nazwa, kategoria_kategoria_id, producent_producent_id  FROM produkt join stan_magazynowy on nr_produktu = produkt_nr_produktu where zasob>0 order by 2,1,3;";
	}
		$zapExec = mysqli_query($baza, $zapytanie);
		$ile = mysqli_num_rows($zapExec);
		$kl = 0;
		if($ile>0){
		while($row = mysqli_fetch_array($zapExec)){
			if($kl == 0) { echo "<div class='row align-items-center'>"; }
			echo "<div class='col-sm-3'><div id='ramka'>";
			$quer = "SELECT * FROM produkt where nazwa = '".$row['nazwa']."' and producent_producent_id = ".$row['producent_producent_id'].' and kategoria_kategoria_id='.$row['kategoria_kategoria_id']." order by rozmiar DESC;";
			$querexec = mysqli_query($baza, $quer);
			$tekst = 0;
			echo "<form action='index.php' method='POST'><div class='form-group'>";
			while($dane = mysqli_fetch_array($querexec)){
				$check = "select * from stan_magazynowy where zasob > 0 and produkt_nr_produktu = $dane[0];";
				$checkexec = mysqli_query($baza, $check);
				if(mysqli_num_rows($checkexec)>0){
					if($tekst == 0){
						$tekst = 1;
						$kat = "select * from kategoria where kategoria_id=$dane[7];";
						$katexe = mysqli_query($baza, $kat);
						$katwyn = mysqli_fetch_row($katexe);
						$prod = "select * from producent where producent_id=$dane[8];";
						$prodexe = mysqli_query($baza, $prod);
						$prodwyn = mysqli_fetch_row($prodexe);
						echo "<h4>".$dane[1]."</h4><h6>$katwyn[1]</h6><h6>$prodwyn[1]</h6><p>".$dane['opis']."</p>Rozmiar: <select name='rozmiar' class='form-control'>";
						$cena = $dane['cena_brutto'];
					}
					echo "<option value=".$dane['rozmiar'].">".$dane['rozmiar']."</option>";
				}
			}
			echo "</select><br/>";
			echo "Ilość: <input type='number' class='form-control' name='ilosc' value=1 min=1 required/><br/>";
			echo "Cena: $cena zł<br/>";
			echo "<input type='hidden' name='item' value='".$row['nazwa']."'/>";
			echo "<input type='hidden' name='item1' value='".$row['kategoria_kategoria_id']."'/>";
			echo "<input type='hidden' name='item2' value='".$row['producent_producent_id']."'/>";
			
			echo "<input type='hidden' name='kategoria' value='".$kat1."'/>";
			echo "<input type='hidden' name='producent' value='".$prod1."'/>";
			echo "<input type='hidden' name='min' value='".$cena_min."'/>";
			echo "<input type='hidden' name='max' value='".$cena_max."'/>";
			
			echo "<button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj'>Dodaj do koszyka</button></form></div>";
			
			echo "</div></div>";
			$kl++;
			if($kl == 4) { echo "</div>"; $kl=0; }
		}
		
		if($kl == 1)
			echo "<div class='col-sm-9'></div></div>";
		elseif($kl==2)
			echo "<div class='col-sm-6'></div></div>";
		elseif($kl==3)
			echo "<div class='col-sm-3'></div></div>";
		}else{
			echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Brak produktów do wyświetlenia</div>";
		}
	?>
		</div>	
		</div>



</body>
</html>