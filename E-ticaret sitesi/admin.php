<a href="cikis.php">Oturumu Kapat </a>
<?php
session_start();

if($_SESSION["tur"] == 'admin')
{
	echo "<body><center><h2>
	<a href='urunsil.php'>Ürün Sil </a><br>
	<a href='urunekle.php'>Ürün Ekle </a><br>
	</h2></center></body>";
}
else
{
	header('location:giris.php');
}





?>