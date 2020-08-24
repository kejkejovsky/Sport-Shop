<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Producent</title>
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
		$item = test_input($_POST['item']);
		if($item == 0)
		{
			$query = "INSERT INTO producent(nazwa_producenta) VALUES ('$prod_nazwa');";
		}
		else
		{
			$query = "UPDATE producent SET nazwa_producenta='$prod_nazwa' WHERE producent_id = $item;";
		}
				
		if(mysqli_query($baza, $query)) {
			if($item == 0)
				echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano producenta</div>";
			else
				echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano producenta</div>";
		}else{
			if(mysqli_errno($baza)==1062){
				echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Producent już istnieje</div>";
			}
		}
		
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$query = "DELETE FROM producent WHERE producent_id = $item;";
		if(mysqli_query($baza, $query)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto producenta</div>";
		}
	}
?>

<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Nazwa</th>
	<th></th>
	<th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM producent order by 2;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{

		$zap2 = "SELECT * FROM produkt WHERE producent_producent_id = $rowa[0];";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows = mysqli_num_rows($zap2Exec);
		echo "<tr><td>$lp</td><td>$rowa[1]</td>";
		echo "<td><a href='producent.php?id=$rowa[0]'>Edycja</a></td><td>";
		if($numberRows == 0)
			echo "<a href='producent.php?del=$rowa[0]'>Usuń</a>";
		echo '</td></tr>';
		$lp++;
	}
?>

<?php
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM producent WHERE producent_id = $idProd;";
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
		
		echo "<form action='producent.php' method='POST'>";
				
		echo "<tr><td></td><td>
				<input name='prod_nazwa' class='form-control mr-sm-2' type='text'  placeholder='Nazwa producenta' value='$name' required /></td>
				<td>
				<input type='hidden' name='item' value='$idProd' />
				<button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_producent'>+</button></td>
				</td><td></td></tr>";
		
		echo "</form>";	
?>
</table>
</div>
</div>
</body>
</html>