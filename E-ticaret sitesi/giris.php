<form action="" method="post"><center>
  <table>
    <tr>
      <td>Adınız</td>
      <td><input type="text" name="kadi"></td>
    </tr>
    
    <tr>
      <td>Şifreniz</td>
      <td><input type="password" name="parola"></td>
    </tr>
    
    <tr>
      <td colspan="2"><input type="submit" name="giris" value="Giriş"></td>
    </tr>
    
    </center>
  </table>
</form>

<?php
if(isset($_POST['giris']))
{
    $kullanici_adi = $_POST['kadi'];
    $sifre = $_POST['parola'];

    $list = new PDO("mysql:host=localhost;dbname=oturum",'root','');
    $listele = $list->query("SELECT * FROM kullanici");

    while($row = $listele->fetch())
    {
        if($kullanici_adi == $row['kullaniciAdi'] && $sifre == $row['sifre'])
        {
            session_start();
            
            $_SESSION["kullaniciadi"] = $kullanici_adi;
            $_SESSION["kullanicisifresi"] = $sifre;
            $_SESSION["tur"] = $row['kullanici_turu'];
            $_SESSION["id"] = $row['kullanici_id'];
            
            if($row['kullanici_turu'] == 'admin') {
                header('location:admin.php');
            } elseif($row['kullanici_turu'] == 'kullanici') {
                header('location:kullanici.php');
            }
            exit; 
        }
    }

    echo "<center><p>Hatalı kullanıcı adı veya şifre.</p></center>";
}
?>
