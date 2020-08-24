<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Kategoria</title>
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
	//Dodawanie/Modyfikacja
	if(isSet($_POST['dodaj_producent']))
	{
		$prod_nazwa = test_input($_POST['prod_nazwa']);
		$item = test_input($_POST['item']);
		
		if($item == 0)
		{
			$query = "INSERT INTO kategoria(nazwa_kategorii) VALUES ('$prod_nazwa');";
		}
		else
		{
			$query = "UPDATE kategoria SET nazwa_kategorii='$prod_nazwa' WHERE kategoria_id = $item;";
		}
				
		if(mysqli_query($baza, $query)) {
			if($item == 0)
				echo "<h2> Dodano kategorie. </h2>";
			else
				echo "<h2> Zmodifikowano kategorie. </h2>";
		}
		mysqli_errno($baza);
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$query = "DELETE FROM kategoria WHERE kategoria_id = $item;";
		if(mysqli_query($baza, $query)) {
			echo "<h2> Usunięto kategorie. </h2>";
		}
	}
?>
<a href="index.php">Powrót</a>
<table>
<tr>
	<th>Lp.</th>
	<th>Nazwa</th>
	<th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM kategoria order by 2;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		$zap2 = "SELECT * FROM produkt WHERE kategoria_kategoria_id = $rowa[0];";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows = mysqli_num_rows($zap2Exec);
		echo "<tr><td>$lp</td><td>$rowa[1]</td>";
		echo "<td><a href='kategoria.php?id=$rowa[0]'>Edycja</a></td>";
		if($numberRows == 0)
			echo "<td><a href='kategoria.php?del=$rowa[0]'>Usuń</a></td>";
		echo '</tr>';
		$lp++;
	}
?>
</table>
<?php
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM kategoria WHERE kategoria_id = $idProd;";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$name = $row[1];
			}
		} 
		else 
		{ 
			$idProd = 0;
			$name = "";
		}
		
		echo "<form action='kategoria.php' method='POST'>";
				
		echo "<label>Nowy kategoria</label> <br>
				<input name='prod_nazwa'  type='text'  placeholder='Nazwa kategorii' value='$name' required />
				<input type='hidden' name='item' value='$idProd' />
				<button  type='submit' name='dodaj_producent'>+</button>";
		
		echo "</form>";	
?>
</body>
</html>