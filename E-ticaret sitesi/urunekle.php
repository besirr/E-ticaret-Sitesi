<form action="urunekle.php" method="post" enctype="multipart/form-data">
    <label for="ad">Ürün Adı:</label>
    <input type="text" name="ad" id="ad" required><br>

    <label for="serino">Seri No:</label>
    <input type="text" name="serino" id="serino" required><br>

    <label for="adet">Adet:</label>
    <input type="number" name="adet" id="adet" required><br>

    <label for="fiyat">Fiyat:</label>
    <input type="text" name="fiyat" id="fiyat" required><br>

    <label for="kategori">Kategori:</label>
    <input type="text" name="kategori" id="kategori" required><br>

    <label for="fotograf">Ürün Fotoğrafı:</label>
    <input type="file" name="fotograf" id="fotograf" required><br>

    <input type="submit" value="Ürün Ekle">
</form>

<?php
$pdo = new PDO("mysql:host=localhost;dbname=oturum", 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = $_POST['ad'];
    $serino = $_POST['serino'];
    $adet = $_POST['adet'];
    $fiyat = $_POST['fiyat'];
    $kategori = $_POST['kategori']; // Kategori bilgisi

    $fotograf = 'uploads/' . $_FILES['fotograf']['name'];
    move_uploaded_file($_FILES['fotograf']['tmp_name'], $fotograf);

    $ekle = $pdo->prepare("INSERT INTO urun (ad, serino, adet, fiyat, kategori, fotograf) VALUES (?, ?, ?, ?, ?, ?)");
    $ekle->execute([$ad, $serino, $adet, $fiyat, $kategori, $fotograf]);

    echo "Ürün başarıyla eklendi!";
}
?>
