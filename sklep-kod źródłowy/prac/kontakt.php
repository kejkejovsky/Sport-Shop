<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Dane kontaktowe</title>
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
	$idprac = $_SESSION['id_pracownika'];
	$baza = mysqli_connect($serwer, $user, $haslo, $baza_danych);
	if(!$baza) 	{	die("Nie polaczono");	}
	$pracownik_id = $_SESSION['id_pracownika'];
	//Dodawanie/Modyfikacja
	if(isSet($_POST['dodaj_producent']))
	{
		$prod_nazwa = test_input($_POST['prod_nazwa']);
		$cena_n = test_input($_POST['cena_n']);
		$item = test_input($_POST['item']);
		$check = "select * from kontakt_pracownik where email='$cena_n' and nr_telefonu=$prod_nazwa and pracownik_id_pracownika=$idprac and id_kontaktu <> $item;";
		$checkexec = mysqli_query($baza, $check);
		if(mysqli_num_rows($checkexec)==0)
		{
			if($item == 0)
			{
				$query = "INSERT INTO kontakt_pracownik VALUES (NULL, $prod_nazwa, '$cena_n', $idprac);";
				if(mysqli_query($baza, $query)) 
				{
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano dane kontaktowe</div>";
				}
				echo mysqli_error($baza);
			}
			else
			{
				$query = "UPDATE kontakt_pracownik SET nr_telefonu=$prod_nazwa, email='$cena_n' WHERE id_kontaktu = $item and pracownik_id_pracownika=$idprac;";
			
				if(mysqli_query($baza, $query)) 
					echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano dane kontaktowe</div>";
			}
		}
		else
		{
			echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dane kontaktowe już istnieją</div>";
		}
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$query = "DELETE FROM kontakt_pracownik WHERE id_kontaktu = $item and pracownik_id_pracownika=$idprac;";
		if(mysqli_query($baza, $query)) 
		{
			echo "<div class='alert alert-success alert-dissmissible'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto dane kontaktowe</div>";
		}
	}
?>

<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Nr telefonu</th>
	<th>Email</th>
	<th></th><th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM kontakt_pracownik where pracownik_id_pracownika = $idprac order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		echo "<tr><td>$lp</td><td>$rowa[1]</td><td>$rowa[2]</td>";
		echo "<td><a href='kontakt.php?id=$rowa[0]'>Edycja</a></td>";
		echo "<td><a href='kontakt.php?del=$rowa[0]'>Usuń</a></td>";
		echo '</tr>';
		$lp++;
	}
?>

<?php
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM kontakt_pracownik WHERE id_kontaktu = $idProd and pracownik_id_pracownika = $idprac;";
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
		
		echo "<form action='kontakt.php' method='POST'>";
				
		echo "<tr><td></td><td>
				<input name='prod_nazwa' class='form-control mr-sm-2' type='text' pattern='[0-9]*'  placeholder='Nr telefonu' value='$name' required /></td>
				<td>
				<input name='cena_n' class='form-control mr-sm-2' type='text'  placeholder='Email' value='$cena_n' required /></td>
				<td>
				";
		
		echo "	<input type='hidden' name='item' value='$idProd' />
				<button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_producent'>+</button></td>
				<td></td></tr>";
		
		echo "</form>";	
?>
</table>
</div>
</div>
</body>
</html>