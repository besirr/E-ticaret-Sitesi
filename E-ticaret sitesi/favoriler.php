<a href="cikis.php">Oturumu Kapat </a>

<?php
session_start();

if ($_SESSION["tur"] == 'kullanici') {
    $list = new PDO("mysql:host=localhost;dbname=oturum", 'root', '');
    
    $kullanici_id = $_SESSION["id"];

    if (isset($_GET['id'])) {
        $urun_id = $_GET['id'];

        $kontrol = $list->prepare("SELECT * FROM favori WHERE kullanici_id = ? AND urun_id = ?");
        $kontrol->execute([$kullanici_id, $urun_id]);
        
        if ($kontrol->rowCount() > 0) {
            echo "<script>alert('Bu ürün zaten favorilerinize eklenmiş!');</script>";
        } else {
            $ekle = $list->prepare("INSERT INTO favori(kullanici_id, urun_id) VALUES (?, ?)");
            $ekle->execute([$kullanici_id, $urun_id]);
            echo "<script>alert('Ürün favorilere eklendi!');</script>";
        }
    }

    if (isset($_GET['sil_id'])) {
        $sil_id = $_GET['sil_id'];
        $sil = $list->prepare("DELETE FROM favori WHERE kullanici_id = ? AND urun_id = ?");
        $sil->execute([$kullanici_id, $sil_id]);
    }

    $favoriler = $list->prepare("
        SELECT urun.urun_id, urun.serino, urun.ad, urun.adet, urun.fotograf, urun.kategori 
        FROM favori
        INNER JOIN urun ON favori.urun_id = urun.urun_id
        WHERE favori.kullanici_id = ?
    ");
    $favoriler->execute([$kullanici_id]);

    if ($favoriler->rowCount() > 0) {
        echo "<center><h2>Favori Ürünleriniz:</h2></center>";
        foreach ($favoriler as $urun) {
            $c = $urun['fotograf'];
            $urun_id = $urun['urun_id'];
            echo "<center>";
            echo "<div style='display: inline-block; margin: 10px; text-align: center;'>";
            echo "<img src='$c' width='150' height='150' alt='Ürün Resmi'><br>";
            echo "Seri No: " . $urun['serino'] . "<br>";
            echo "Ad: " . $urun['ad'] . "<br>";
            echo "Adet: " . $urun['adet'] . "<br>";
            echo "Kategori: " . $urun['kategori'] . "<br>";  // Kategori bilgisini ekledik

            echo "<a href='?sil_id=$urun_id'>
                    <img src='cöp.png' alt='Sil' width='40' height='40' style='cursor: pointer;'>
                  </a>";
            echo "</div>";
            echo "<hr>";
            echo "</center>";
			
			
		
			
			
			
			
			
			
        }
    } 
	else 
	{
        echo "<center><h2>Henüz favori ürün eklemediniz.</h2></center>";
    }
	

	 echo '<div style="position: fixed; top: 10px; right: 110px; z-index: 1000;">';
            echo '<a href="sepet.php">';
            echo '<img src="sepett.png" width="90" height="100" alt="Sepet Sayfasına Git">';
            echo '</a>';
            echo '</div>';
    
            echo '<div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">'; 
            echo '<a href="kullanici.php">';
            echo '<img src="home.png" width="90" height="100" alt="Favoriler Sayfasına Git">';
            echo '</a>';
            echo '</div>';
			
			 echo '<div style="position: fixed; top: 10px; right: 200px; z-index: 1000;">'; 
            echo '<a href="cikti.php">';
            echo '<img src="home.png" width="90" height="100" alt="Favoriler Sayfasına Git">';
            echo '</a>';
            echo '</div>';
			
} else {
    header('location:giris.php');
}
?>
