<?php
ob_start();

require('fpdf.php'); 

$pdo = new PDO("mysql:host=localhost;dbname=oturum", 'root', '');

session_start();
$kullanici_id = $_SESSION["id"]; 
$toplam_tutar = 0;

$pdf = new FPDF();
$pdf->AddPage(); 
$pdf->SetFont('Arial', '', 12);

$sepetListesi = $pdo->prepare("
    SELECT urun.urun_id, urun.serino, urun.fiyat, urun.fotograf, urun.ad, urun.kategori, 
    sepet.adet AS sepet_adet
    FROM sepet
    INNER JOIN urun ON sepet.urun_id = urun.urun_id
    WHERE sepet.kullanici_id = ?");
$sepetListesi->execute([$kullanici_id]);

if ($sepetListesi->rowCount() > 0) {
    foreach ($sepetListesi as $urun) {
        $c = $urun['fotograf'];
        $urun_id = $urun['urun_id'];
        $serino = $urun['serino'];
        $fiyat = $urun['fiyat'];
        $sepet_adet = $urun['sepet_adet']; 
        $kategori = $urun['kategori'];
        $toplam_tutar += $fiyat * $sepet_adet; // Toplam tutarı hesapla

        $pdf->Cell(40, 10, "Ad: " . $urun['ad']);
        $pdf->Ln(8);
        $pdf->Cell(40, 10, "Urun ID: " . $urun['urun_id']); 
        $pdf->Ln(8);
        $pdf->Cell(40, 10, "Seri No: " . $urun['serino']); 
        $pdf->Ln(8);
        $pdf->Cell(40, 10, "Fiyat: " . $urun['fiyat'] . " TL"); 
        $pdf->Ln(8);
        $pdf->Cell(40, 10, "Kategori: " . $urun['kategori']); 
        $pdf->Ln(8);
        $pdf->Cell(40, 10, "Sepet Adeti: " . $urun['sepet_adet']);
        $pdf->Ln(8);
    }
    
    // Toplam tutarı yazdır
    $pdf->Ln(8); 
    $pdf->Cell(40, 10, "Toplam Tutar: " . $toplam_tutar . " TL");
    $pdf->Ln(8); 
} else {
    $pdf->Cell(40, 10, "Sepetinizde ürün bulunmamaktadır.");
}

$pdf->Output();
?>
