<?php
session_start();

if ($_SESSION["tur"] == 'kullanici') {
    $list = new PDO("mysql:host=localhost;dbname=oturum", 'root', '');
    $kullanici_id = $_SESSION["id"];

    if (isset($_GET['id'])) {
        $urun_id = $_GET['id'];

        $stokKontrol = $list->prepare("SELECT adet FROM urun WHERE urun_id = ?");
        $stokKontrol->execute([$urun_id]);
        $stok = $stokKontrol->fetchColumn(); // Stok miktarı

        $adetKontrol = $list->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $adetKontrol->execute([$kullanici_id, $urun_id]);
        $sepet_adet = $adetKontrol->fetchColumn();

        if ($sepet_adet < $stok) {
            $yeni_adet = $sepet_adet + 1; // Bir adet daha ekle
            if ($sepet_adet > 0) {
                $guncelle = $list->prepare("UPDATE sepet SET adet = ? WHERE kullanici_id = ? AND urun_id = ?");
                $guncelle->execute([$yeni_adet, $kullanici_id, $urun_id]);
            } else {
                $ekle = $list->prepare("INSERT INTO sepet(kullanici_id, urun_id, adet) VALUES (?, ?, ?)");
                $ekle->execute([$kullanici_id, $urun_id, 1]);
            }
        } else {
            echo "<script>alert('Daha fazla ürün kalmadı!');</script>";
        }
    }

    if (isset($_GET['sil_id'])) {
        $sil_id = $_GET['sil_id'];
        $sil = $list->prepare("DELETE FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $sil->execute([$kullanici_id, $sil_id]);
    }

    if (isset($_GET['eksi_id'])) {
        $eksi_id = $_GET['eksi_id'];

        $adetKontrol = $list->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $adetKontrol->execute([$kullanici_id, $eksi_id]);
        $sepet_adet = $adetKontrol->fetchColumn();

        if ($sepet_adet > 1) {
            $yeni_adet = $sepet_adet - 1;
            $guncelle = $list->prepare("UPDATE sepet SET adet = ? WHERE kullanici_id = ? AND urun_id = ?");
            $guncelle->execute([$yeni_adet, $kullanici_id, $eksi_id]);
        } else {
            $sil = $list->prepare("DELETE FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
            $sil->execute([$kullanici_id, $eksi_id]);
        }
    }

    if (isset($_GET['arti_id'])) {
        $arti_id = $_GET['arti_id'];

        $stokKontrol = $list->prepare("SELECT adet FROM urun WHERE urun_id = ?");
        $stokKontrol->execute([$arti_id]);
        $stok = $stokKontrol->fetchColumn(); // Stok miktarı

        $adetKontrol = $list->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $adetKontrol->execute([$kullanici_id, $arti_id]);
        $sepet_adet = $adetKontrol->fetchColumn();

        if ($sepet_adet < $stok) {
            $yeni_adet = $sepet_adet + 1; // Bir adet artır
            $guncelle = $list->prepare("UPDATE sepet SET adet = ? WHERE kullanici_id = ? AND urun_id = ?");
            $guncelle->execute([$yeni_adet, $kullanici_id, $arti_id]);
        } else {
            echo "<script>alert('Daha fazla ürün eklenemez!');</script>";
        }
    }

    $sepetListesi = $list->prepare("
        SELECT urun.urun_id, urun.serino, urun.ad, urun.fiyat, urun.adet AS urun_adet, urun.fotograf, sepet.adet AS sepet_adet, urun.kategori 
        FROM sepet
        INNER JOIN urun ON sepet.urun_id = urun.urun_id
        WHERE sepet.kullanici_id = ?
    ");
    $sepetListesi->execute([$kullanici_id]);

    $toplam_tutar = 0; 

    if ($sepetListesi->rowCount() > 0) {
        echo "<center><h2>Sepetteki Ürünleriniz:</h2></center>";
        foreach ($sepetListesi as $urun) {
            $c = $urun['fotograf'];
            $urun_id = $urun['urun_id'];
            $sepet_adet = $urun['sepet_adet']; // Sepetteki adet
            $kategori = $urun['kategori']; // Ürünün kategorisi

            $toplam_tutar += $urun['fiyat'] * $sepet_adet; // Her ürünün fiyatını sepetteki adetle çarpıyoruz

            echo "<center>";
            echo "<img src='$c' width='100' height='100'><br>";
            echo "Seri No: " . $urun['serino'] . "<br>";
            echo "Ad: " . $urun['ad'] . "<br>";
            echo "Stok Adeti: " . $urun['urun_adet'] . "<br>"; // Ürünün stok adeti
            echo "Sepetteki Adet: " . $sepet_adet . "<br>"; // Sepetteki toplam adedi gösterir.
            echo "Kategori: " . $kategori . "<br>"; // Kategori bilgisini ekledik

            echo "<a href='?eksi_id=$urun_id'>
                    <img src='eksiii.png' alt='Adet Azalt' width='40' height='40' style='cursor: pointer;'>
                  </a>";
            echo "<a href='?sil_id=$urun_id'>
                    <img src='cöp.png' alt='Sil' width='40' height='40' style='cursor: pointer;'>
                  </a>";
            echo "<a href='?arti_id=$urun_id'>
                    <img src='artı.png' alt='Adet Artır' width='40' height='40' style='cursor: pointer;'>
                  </a>";
            echo "<hr>";
            echo "</center>";
        }
        echo "<center><h3>Toplam Tutar: " . $toplam_tutar . " TL</h3></center>";
    } else {
        echo "<center><h2>Henüz sepetinizde ürün bulunmamaktadır.</h2></center>";
    }
	 echo '<div style="position: fixed; top: 10px; right: 110px; z-index: 1000;">';
    echo '<a href="sepet.php">';
    echo '<img src="star.png" width="90" height="100" alt="Sepet Sayfasına Git">';
    echo '</a>';
    echo '</div>';

    echo '<div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">'; 
    echo '<a href="kullanici.php">';
    echo '<img src="home.png" width="90" height="100" alt="Favoriler Sayfasına Git">';
    echo '</a>';
    echo '</div>';

    echo '<div style="position: fixed; top: 10px; right: 200px; z-index: 1000;">'; 
    echo '<a href="cikti.php">';
    echo '<img src="fatura.png" width="90" height="100" alt="Favoriler Sayfasına Git">';
    echo '</a>';
    echo '</div>';
}
?>
