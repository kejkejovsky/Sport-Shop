<?php session_start(); ?>
<html>
<head>
	<meta charset='UTF-8' />
	<title>Pracownik - Produkt</title>
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
		
		$proc = test_input($_POST['proc']);
		$cena_b = round($cena_n * (1+$proc/100),2);
		$roz = test_input($_POST['roz']);
		$opis = test_input($_POST['opis']);
		$kat = test_input($_POST['kat']);
		$prod = test_input($_POST['prod']);
		$item = test_input($_POST['item']);
		
		if($item == 0)
		{
			$check = "select * from produkt where nazwa='$prod_nazwa' and rozmiar='$roz' and kategoria_kategoria_id=$kat and producent_producent_id=$prod;";
			$checkexec = mysqli_query($baza, $check);
			if(mysqli_num_rows($checkexec)==0)
			{
				$query = "INSERT INTO produkt VALUES (NULL, '$prod_nazwa', $cena_n, $cena_b, $proc, '$roz', '$opis', $kat, $prod);";
				if(mysqli_query($baza, $query)) {
					echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Dodano produkt</div>";
					$last_id = mysqli_insert_id($baza);
					$zap2 = "SELECT * FROM magazyn order by 1;";
					$zap2Exec = mysqli_query($baza, $zap2);
					while($row = mysqli_fetch_array($zap2Exec))
					{
						$add = "INSERT INTO stan_magazynowy values(CURRENT_DATE, 0, $last_id, '$row[0]');";
						mysqli_query($baza, $add);
					}
				}
				echo mysqli_error($baza);
			}
			else
			{
				echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Produkt już istnieje</div>";
			}
		}
		else
		{
			$check = "select * from produkt where nazwa='$prod_nazwa' and rozmiar='$roz' and kategoria_kategoria_id=$kat and producent_producent_id=$prod and nr_produktu <> $item;";
			$checkexec = mysqli_query($baza, $check);
			if(mysqli_num_rows($checkexec)==0)
			{
				$query = "UPDATE produkt SET nazwa='$prod_nazwa', cena_netto=$cena_n, cena_brutto=$cena_b, procent_vat=$proc, rozmiar='$roz', opis='$opis', kategoria_kategoria_id=$kat, producent_producent_id=$prod WHERE nr_produktu = $item;";
			
				if(mysqli_query($baza, $query)) 
					echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Zmodyfikowano produkt</div>";
			}
			else
			{
				echo "<div class='alert alert-danger alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Produkt już istnieje</div>";
			}
		}
	}
	//Usuwanie
	if(isSet($_GET['del']))
	{
		$item = test_input($_GET['del']);
		$del = "DELETE FROM stan_magazynowy where produkt_nr_produktu = $item";
		mysqli_query($baza, $del);
		$query = "DELETE FROM produkt WHERE nr_produktu = $item;";
		if(mysqli_query($baza, $query)) {
			echo "<div class='alert alert-success alert-dissmissible'><a href='.' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Usunięto produkt</div>";
		}
	}
	
?>

<div class='row'>
<div class='col-sm-12'>


<table class='table table-striped table-hover' id='tabela'>
<tr>
	<th>Lp.</th>
	<th ><input class="form-control" id="szukaj_osoby" type="text" placeholder="Nazwa"></th>
	<th>Cena netto</th>
	<th>Cena brutto</th>
	<th>Procent VAT</th>
	<th>Rozmiar</th>
	<th>Opis</th>
	<th>Kategoria</th>
	<th>Producent</th>
	<th>Ilość</th>
	<th></th><th></th><th></th><th></th>
</tr>

<?php	
	
	$zapytanie = "SELECT * FROM produkt order by kategoria_kategoria_id,nazwa,producent_producent_id, rozmiar DESC;";		
	$zap1 = mysqli_query($baza, $zapytanie);
	$lp = 1;
	while($rowa = mysqli_fetch_array($zap1)) 
	{
		$numberRows = 0;
		$zap2 = "SELECT * FROM zamowienie_produkt WHERE produkt_nr_produktu = $rowa[0];";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows = mysqli_num_rows($zap2Exec);
		$zap2 = "SELECT * FROM stan_magazynowy WHERE produkt_nr_produktu = '$rowa[0]' and zasob > 0;";
		$zap2Exec = mysqli_query($baza, $zap2);
		$numberRows += mysqli_num_rows($zap2Exec);
		$ilosc = 0;
		while($row = mysqli_fetch_array($zap2Exec))
		{
			$ilosc += $row[1];
		}
		$kquery = "SELECT * FROM kategoria where kategoria_id = $rowa[7]";
		$kqueryExec = mysqli_query($baza, $kquery);
		$kanswer = mysqli_fetch_array($kqueryExec);
		
		$pquery = "SELECT * FROM producent where producent_id = $rowa[8]";
		$pqueryExec = mysqli_query($baza, $pquery);
		$panswer = mysqli_fetch_array($pqueryExec);
		echo "<tr><td>$lp</td><td id='gg'>$rowa[1]</td><td>$rowa[2] zł</td><td>$rowa[3] zł</td><td>$rowa[4]%</td><td>$rowa[5]</td><td>$rowa[6]</td><td>$kanswer[1]</td><td>$panswer[1]</td><td>$ilosc</td>";
		echo "<td><a href='produkt.php?rozmiar=$rowa[0]'>Dodaj inny rozmiar</a></td>";
		echo "<td><a href='produktStan.php?id=$rowa[0]'>Uzupełnij</a></td>";
		echo "<td><a href='produkt.php?id=$rowa[0]'>Edycja</a></td><td>";
		if($numberRows == 0)
			echo "<a href='produkt.php?del=$rowa[0]'>Usuń</a>";
		echo '</td></tr>';
		$lp++;
	}
?>

<?php
	if(isSet($_GET['rozmiar'])){
		$idProd = test_input($_GET['rozmiar']); 
		$zap4 = "SELECT * FROM produkt WHERE nr_produktu = $idProd;";
		$zap4Exec = mysqli_query($baza, $zap4);
		while($row = mysqli_fetch_array($zap4Exec))
		{
			$idProd = 0;
			$name = $row[1];
			$cena_n = $row[2];
			$cena_b = $row[3];
			$proc = $row[4];
			$roz = "";
			$opis = $row[6];
			$kat = $row[7];
			$prod = $row[8];
		}
	}
	elseif(isSet($_GET['id'])) 
		{ 
			$idProd = test_input($_GET['id']); 
			$zap4 = "SELECT * FROM produkt WHERE nr_produktu = $idProd;";
			$zap4Exec = mysqli_query($baza, $zap4);
			while($row = mysqli_fetch_array($zap4Exec))
			{
				$name = $row[1];
				$cena_n = $row[2];
				$proc = $row[4];
				$roz = $row[5];
				$opis = $row[6];
				$kat = $row[7];
				$prod = $row[8];
			}
		} 
		else 
		{ 
			$idProd = 0;
			$name = "";
			$cena_n = "";
			$proc = "";
			$roz = "";
			$opis = "Opis produktu";
			$kat = "";
			$prod = "";
		}
		
		echo "<form action='produkt.php' method='POST'>";
				
		echo "<tr><td></td><td>
				<input class='form-control mr-sm-2' name='prod_nazwa'  type='text'  placeholder='Nazwa produktu' value='$name' required /></td>
				<td>
				<input class='form-control mr-sm-2' name='cena_n'  type='text'  placeholder='Cena netto' value='$cena_n' required /></td>
				<td>
				</td>
				<td>
				<select class='form-control mr-sm-2' name='proc'>
					<option value='23'";
					if($proc == 23) {echo " selected";}
					echo " >23%</option>
					<option value='8'";
					if($proc == 8) {echo " selected";}
					echo " >8%</option>
					<option value='5'";
					if($proc == 5) {echo " selected";}
					echo ">5%</option>
				</select></td>
				<td>
				<input class='form-control mr-sm-2' name='roz'  type='text'  placeholder='Rozmiar' value='$roz' required /></td>
				<td>
				<textarea class='form-control mr-sm-2' name='opis' >$opis</textarea></td>
				<td>
				";
		$zapytanie = "SELECT * FROM kategoria order by 2;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		echo "<select name='kat' class='form-control mr-sm-2'>";
		while($rowa = mysqli_fetch_array($zap1)) 
		{
			if($kat == $rowa[0])
				echo "<option value='$rowa[0]' selected>$rowa[1]</option>";
			else
				echo "<option value='$rowa[0]'>$rowa[1]</option>";
		}
		echo "</select></td>
				<td>";
		
		$zapytanie = "SELECT * FROM producent order by 2;";		
		$zap1 = mysqli_query($baza, $zapytanie);
		echo "<select name='prod' class='form-control mr-sm-2'>";
		while($rowa = mysqli_fetch_array($zap1)) 
		{
			if($prod == $rowa[0])
				echo "<option value='$rowa[0]' selected>$rowa[1]</option>";
			else
				echo "<option value='$rowa[0]'>$rowa[1]</option>";
		}
		echo "</select></td>
				<td>";
		
		echo "	<input type='hidden' name='item' value='$idProd' />
				<button class='btn btn-success my-2 mr-sm-2' type='submit' name='dodaj_producent'>+</button></td>
				<td></td><td></td><td></td><td></td></tr>";
		
		echo "</form>";	
?>
</table>
</div>
</div>


<script>

var $rows = $('#tabela tr #gg'); //pobranie wierszy z tabeli
$('#szukaj_osoby').keyup(function() { //funkcja keyup jest wywolywana kiedy uzytkownik nacisnie klawisz
		$rows.parent().show();
        var val = '^(?=.*\\b' + $.trim($(this).val()).split(/\s+/).join('\\b)(?=.*\\b') + ').*$',
            reg = RegExp(val, 'i'),
            text; // uzycie wyrazenia regularnego do sprawdzenia elementu
 
        $rows.show().filter(function() { // najpierw pokazujemy wszystkie wiersze, a potem stosujemy funkcje filter()
            text = $(this).text().replace(/\s+/g, ' ');
            return !reg.test(text); //sprawdzamy czy wiersz pasuje doelementu szukanego, jeśli nie to chowamy ten wiersz 
        }).parent().hide();
    });

</script>
</body>
</html>