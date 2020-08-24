<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Pracownicy</title>
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
	//Dodawanie/Modyfikacja
	if(isSet($_POST['dodaj_producent']))
	{
		$prod_nazwa = test_input($_POST['prod_nazwa']);
		$cena_n = test_input($_POST['cena_n']);
		
		$opis = test_input($_POST['opis']);
		$kat = test_input($_POST['kat']);
		$item = test_input($_POST['item']);
		if($_SESSION['admin']==1)
		{
			$cena_b = test_input($_POST['cena_b']);
			$proc = test_input($_POST['proc']);
			$roz = test_input($_POST['roz']);
			if($item == 0)
			{
				$check = "select * from pracownik where login='$roz'";
				$checkexec = mysqli_query($baza, $check);
				if(mysqli_num_rows($checkexec)==0)
				{
					$query = "INSERT INTO pracownik VALUES (NULL, '$prod_nazwa', '$cena_n', $cena_b, date('$proc'), '$roz', '$opis', '$kat');";
					if(mysqli_query($baza, $query)) 
					{
						echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano pracownika</div>";
					}
					echo mysqli_error($baza);
				}
				else
				{
					echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Pracownik o tym loginie już istnieje</div>";
				}
			}
			else
			{
				$check = "select * from pracownik where login='$roz' and id_pracownika <> $item;";
				$checkexec = mysqli_query($baza, $check);
				if(mysqli_num_rows($checkexec)==0)
				{
					$query = "UPDATE pracownik SET imie='$prod_nazwa', nazwisko='$cena_n', placa=$cena_b, data_zatrudnienia=date('$proc'), login='$roz', haslo='$opis', magazyn_nazwa='$kat' WHERE id_pracownika = $item;";
					if(mysqli_query($baza, $query)) 
					{
						echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano pracownika</div>";
					}
				}
				else
				{
					echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Pracownik o tym loginie już istnieje</div>";
				}
			}
		}
		else
		{
			$query = "UPDATE pracownik SET imie='$prod_nazwa', nazwisko='$cena_n', haslo='$opis', magazyn_nazwa='$kat' WHERE id_pracownika = $item;";
			if(mysqli_query($baza, $query)) 
				echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano pracownika</div>";
		}
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$query = "DELETE FROM pracownik WHERE id_pracownika = $item;";
		if(mysqli_query($baza, $query)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto pracownika</div>";
		}
	}
?>

<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Imię</th>
	<th>Nazwisko</th>
	<th>Płaca</th>
	<th>Data zatrudnienia</th>
	<th>Login</th>
	<th>Hasło</th>
	<th>Magazyn</th>
	<th></th>
	<th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM pracownik order by nazwisko, imie;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		if($_SESSION['admin']==1 or $rowa['id_pracownika']==$_SESSION['id_pracownika']){
		echo "<tr><td>$lp</td><td>$rowa[1]</td><td>$rowa[2]</td><td>$rowa[3] zł</td><td>$rowa[4]</td><td>$rowa[5]</td><td>$rowa[6]</td><td>$rowa[7]</td>";
		echo "<td><a href='pracownicy.php?id=$rowa[0]'>Edycja</a></td>";
		echo "<td>";
		if(strcmp($rowa['login'],"admin")!=0 && $_SESSION['admin']==1) {echo "<a href='pracownicy.php?del=$rowa[0]'>Usuń</a>";}
		echo "</td>";
		echo '</tr>';
		$lp++;
		}
	}
?>

<?php
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM pracownik WHERE id_pracownika = $idProd;";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$name = $row[1];
				$cena_n = $row[2];
				$cena_b = $row[3];
				$proc = $row[4];
				$roz = $row[5];
				$opis = $row[6];
				$kat = $row[7];
			}
		} 
		else 
		{ 
			$idProd = 0;
			$name = "";
			$cena_n = "";
			$cena_b = "";
			$proc = "";
			$roz = "";
			$opis = "";
			$kat = "";
		}
		
		echo "<form action='pracownicy.php' method='POST'>";
				
		echo "<tr><td></td><td>
				<input name='prod_nazwa' class='form-control mr-sm-2' type='text'  placeholder='Imię' value='$name' required /></td>
				<td>
				<input name='cena_n' class='form-control mr-sm-2' type='text'  placeholder='Nazwisko' value='$cena_n' required /></td>";
				if($_SESSION['admin']==1){
				echo "<td>
				<input name='cena_b' class='form-control mr-sm-2' type='text'  placeholder='Płaca' pattern='[0-9]*' title='Proszę wprowadzić liczbę' value='$cena_b' required /></td>
				<td>
				<input name='proc' class='form-control mr-sm-2' type='date'  placeholder='Data zatrudnienia' value='$proc' required /></td>
				<td>
				<input name='roz' class='form-control mr-sm-2' type='text'  placeholder='Login' value='$roz' required /></td>";}
				else {echo "<td></td><td></td><td></td>";}
				echo "
				<td>
				<input name='opis' class='form-control mr-sm-2' type='text'  placeholder='Hasło' value='$opis' required /></td>
				<td>
				";
		$zapytanie = "SELECT * FROM magazyn order by 1;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		echo "<select class='form-control mr-sm-2' name='kat'>";
		while($rowa = mysqli_fetch_array($zap1)) 
		{
			if($kat == $rowa[0])
				echo "<option value='$rowa[0]' selected>$rowa[0]</option>";
			else
				echo "<option value='$rowa[0]'>$rowa[0]</option>";
		}
		echo "</select>";
		
		echo "	<input type='hidden' name='item' value='$idProd' /></td>
				<td>
				<button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_producent'";
				if($_SESSION['admin']==0 && $idProd == 0) {echo " disabled ";}
				echo ">+</button></td><td></td></tr>";
		
		echo "</form>";	
?>
</div>
</div>
</table>
</body>
</html>