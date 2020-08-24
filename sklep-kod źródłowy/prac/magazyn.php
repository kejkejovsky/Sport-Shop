<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Magazyn</title>
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
		if(strcmp($item,"new")==0)
		{
			$check = "select * from magazyn where nazwa='$prod_nazwa';";
			$checkexe = mysqli_query($baza, $check);
			if(mysqli_num_rows($checkexe) == 0)
			{
				$query = "INSERT INTO magazyn(nazwa) VALUES ('$prod_nazwa');";
				if(mysqli_query($baza, $query))
				{
					echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano magazyn</div>";
					$adder = "SELECT * from produkt order by 1";
					if($adderExec = mysqli_query($baza, $adder))
					{
						if(mysqli_num_rows($adderExec) > 0)
						{
							while($rowa = mysqli_fetch_array($adderExec))
							{
								$add = "INSERT INTO stan_magazynowy values(CURRENT_DATE, 0, $rowa[0], '$prod_nazwa');";
								mysqli_query($baza, $add);
							}
						}
					}
				}
			}
			else
			{
				echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Magazyn już istnieje</div>";
			}
		}
		else
		{
			$check = "select * from magazyn where nazwa='$prod_nazwa';";
			$checkexe = mysqli_query($baza, $check);
			if(mysqli_num_rows($checkexe) == 0)
			{
				$query = "INSERT INTO magazyn(nazwa) VALUES ('$prod_nazwa');";
				if(mysqli_query($baza, $query))
					echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano magazyn</div>";
				echo mysqli_error($baza);
				$upp = "UPDATE stan_magazynowy SET magazyn_nazwa='$prod_nazwa' WHERE magazyn_nazwa = '$item';";
				mysqli_query($baza, $upp);
				echo mysqli_error($baza);
				$upp = "UPDATE pracownik SET magazyn_nazwa='$prod_nazwa' WHERE magazyn_nazwa = '$item';";
				mysqli_query($baza, $upp);
				echo mysqli_error($baza);
				$query = "DELETE FROM magazyn WHERE nazwa = '$item';";
				mysqli_query($baza, $query);
			}
			else
			{
				echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Magazyn już istnieje</div>";
			}
		}
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$delete = "DELETE FROM stan_magazynowy WHERE magazyn_nazwa = '$item'";
		mysqli_query($baza, $delete);
		$query = "DELETE FROM magazyn WHERE nazwa = '$item';";
		if(mysqli_query($baza, $query)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto magazyn</div>";
		}
		echo mysqli_error($baza);
	}
?>

<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
<tr>
	<th>Lp.</th>
	<th>Nazwa</th>
	<th></th><th></th>
</tr>

	
<?php	
	
	$zapytanie = "SELECT * FROM magazyn order by 1;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		$zap2 = "SELECT * FROM stan_magazynowy WHERE magazyn_nazwa = '$rowa[0]' and zasob > 0;";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows = mysqli_num_rows($zap2Exec);
		$zap2 = "SELECT * FROM pracownik WHERE magazyn_nazwa = '$rowa[0]';";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows += mysqli_num_rows($zap2Exec);
		echo "<tr><td>$lp</td><td>$rowa[0]</td>";
		echo "<td><a href='magazyn.php?id=$rowa[0]'>Edycja</a></td><td>";
		if($numberRows == 0)
			echo "<a href='magazyn.php?del=$rowa[0]'>Usuń</a>";
		echo '</td></tr>';
		$lp++;
	}
?>

<?php
	if(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM magazyn WHERE nazwa = '$idProd';";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$name = $row[0];
			}
		} 
		else 
		{ 
			$idProd = "new";
			$name = "";
		}
		
		echo "<form action='magazyn.php' method='POST'>";
				
		echo "<tr><td></td><td>
				<input name='prod_nazwa' class='form-control mr-sm-2' type='text' type='text'  placeholder='Nazwa magazynu' value='$name' required /></td>
				<td>
				<input type='hidden' name='item' value='$idProd' />
				<button class='btn btn-success my-2 mr-sm-2'  type='submit' name='dodaj_producent'>+</button></td>
				<td></td></tr>";
		
		echo "</form>";	
?>
</table>
</div>
</div>
</body>
</html>