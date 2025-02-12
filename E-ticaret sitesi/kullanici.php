<a href="cikis.php">Oturumu Kapat </a>

<?php
session_start();

if ($_SESSION["tur"] == 'kullanici') {
    // Veritabanı bağlantısı
    $list = new PDO("mysql:host=localhost;dbname=oturum", 'root', '');
    $kullanici_id = $_SESSION["id"];

    // Kategori ve fiyat aralığına göre arama işlemi
    $kategori_arama = '';
    $min_fiyat = '';
    $max_fiyat = '';
    
    if (isset($_GET['arama_kategori'])) {
        $kategori_arama = $_GET['arama_kategori'];
    }
    if (isset($_GET['min_fiyat'])) {
        $min_fiyat = $_GET['min_fiyat'];
    }
    if (isset($_GET['max_fiyat'])) {
        $max_fiyat = $_GET['max_fiyat'];
    }

    // Sepete ekleme işlemi
    if (isset($_GET['id'])) {
        $urun_id = $_GET['id'];

        // Ürünün stok bilgisini al
        $stokKontrol = $list->prepare("SELECT adet FROM urun WHERE urun_id = ?");
        $stokKontrol->execute([$urun_id]);
        $stok = $stokKontrol->fetchColumn(); // Stok miktarı

        // Sepetteki mevcut adet
        $adetKontrol = $list->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
        $adetKontrol->execute([$kullanici_id, $urun_id]);
        $sepet_adet = $adetKontrol->fetchColumn();

        // Sepetteki mevcut adedi stokla karşılaştır
        if ($sepet_adet < $stok) {
            $yeni_adet = $sepet_adet + 1;  // Bir adet daha ekle
            if ($sepet_adet > 0) {
                // Ürün zaten sepetteyse, mevcut adedi arttır
                $guncelle = $list->prepare("UPDATE sepet SET adet = ? WHERE kullanici_id = ? AND urun_id = ?");
                $guncelle->execute([$yeni_adet, $kullanici_id, $urun_id]);
            } else {
                // Ürün sepette değilse, yeni ekle
                $ekle = $list->prepare("INSERT INTO sepet(kullanici_id, urun_id, adet) VALUES (?, ?, ?)");
                $ekle->execute([$kullanici_id, $urun_id, 1]);
            }
        } else {
            echo "<script>alert('Daha fazla ürün kalmadı!');</script>";
        }
    }

    // Favorilere ekleme işlemi
    if (isset($_GET['favori_id'])) {
        $urun_id = $_GET['favori_id'];

        $kontrol = $list->prepare("SELECT * FROM favori WHERE kullanici_id = ? AND urun_id = ?");
        $kontrol->execute([$kullanici_id, $urun_id]);

        if ($kontrol->rowCount() > 0) {
            echo "<script>alert('Bu ürün zaten favorilerinize eklenmiş!');</script>";
        } else {
            $ekleFavori = $list->prepare("INSERT INTO favori(kullanici_id, urun_id) VALUES (?, ?)");
            $ekleFavori->execute([$kullanici_id, $urun_id]);
            echo "<script>alert('Ürün favorilere eklendi!');</script>";
        }
    }

    // Ürünleri listeleme (kategori ve fiyat aralığına göre filtreleme)
    $query = "SELECT * FROM urun WHERE 1";  // Varsayılan sorgu, tüm ürünleri getirir

    // Kategori araması
    if ($kategori_arama) {
        $query .= " AND kategori LIKE '%" . $kategori_arama . "%'";
    }

    // Fiyat aralığına göre filtreleme
    if ($min_fiyat && $max_fiyat) {
        $query .= " AND fiyat BETWEEN " . $min_fiyat . " AND " . $max_fiyat;
    } elseif ($min_fiyat) {
        $query .= " AND fiyat >= " . $min_fiyat;
    } elseif ($max_fiyat) {
        $query .= " AND fiyat <= " . $max_fiyat;
    }

    $urunListesi = $list->query($query);

    // Arama kutusu ve kategori seçme formu
    echo '<center>';
    echo "Ürün Arama";
    echo '<form method="GET">';
    echo 'Kategori: <input type="text" name="arama_kategori" placeholder="Kategori ara..." value="' . htmlspecialchars($kategori_arama) . '">';
    echo 'Min Fiyat: <input type="number" name="min_fiyat" placeholder="Min Fiyat" value="' . htmlspecialchars($min_fiyat) . '">';
    echo 'Max Fiyat: <input type="number" name="max_fiyat" placeholder="Max Fiyat" value="' . htmlspecialchars($max_fiyat) . '">';
    echo '<input type="submit" value="Ara">';
    echo '</form>';
    echo '</center>';

    if ($urunListesi->rowCount()) {
        echo '<center>';
        foreach ($urunListesi as $urun) {
            $id = $urun['urun_id'];
            $fotograf = $urun['fotograf'];

            echo '<table>';
            echo '<tr>';
            echo '<td><img src="' . $fotograf . '" alt="Ürün Resmi" width="150" height="150"></td>';
            echo '<td style="padding-left: 20px;">'; 
            echo 'Seri No: ' . $urun['serino'] . '<br>';
            echo 'Ad: ' . $urun['ad'] . '<br>';
            echo 'Adet: ' . $urun['adet'] . '<br>';
            echo 'Fiyat: ' . $urun['fiyat'] . '<br>';
            echo 'Kategori: ' . $urun['kategori'] . '<br>';  // Kategoriyi ekledik
            echo '</td>';
            echo '</tr>';

            // Sepetteki mevcut adet
            $adetKontrol = $list->prepare("SELECT adet FROM sepet WHERE kullanici_id = ? AND urun_id = ?");
            $adetKontrol->execute([$kullanici_id, $id]);
            $adet = 0;
            if ($adetKontrol->rowCount() > 0) {
                $urun = $adetKontrol->fetch();
                $adet = $urun['adet'];
            }

            // Sepetteki adet bilgisini yaz
            echo '<tr>';
            echo '<td colspan="2" style="text-align: center; padding-top: 10px;">';
            echo 'Sepetteki Adet: ' . $adet . '<br>';
            // Sepete ekleme butonu
            echo '<a href="?id=' . $id . '">';
            echo '<img src="sepett.jpg" width="50" height="50" alt="Sepete Ekle">';  // Sepete ekle ikonu
            echo '</a>';

            // Favoriye ekleme butonu
            echo '<a href="?favori_id=' . $id . '">';
            echo '<img src="yıldız.jpg" width="50" height="50" alt="Favorilere Ekle">';  // Favorilere ekle ikonu
            echo '</a>';
            echo '</td>';
            echo '</tr>';
            echo '</table>';
            
            // Sepet ve Favori simgelerinin sabit konumu
            echo '<div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">';
            echo '<a href="sepet.php">';
            echo '<img src="sepett.png" width="90" height="100" alt="Sepet Sayfasına Git">';
            echo '</a>';
            echo '</div>';

            echo '<div style="position: fixed; top: 10px; right: 110px; z-index: 1000;">';
            echo '<a href="favoriler.php">';
            echo '<img src="star.png" width="90" height="100" alt="Favoriler Sayfasına Git">';
            echo '</a>';
            echo '</div>';
        }
        echo '</center>';
    } else {
        echo "<center><h2>Hiç ürün bulunamadı.</h2></center>";
    }

} else {
    header('location:giris.php');
}
?>