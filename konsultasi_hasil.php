<?php
$rows = $db->get_results("SELECT * FROM tb_gejala WHERE kode_gejala IN (SELECT kode_gejala FROM tb_konsultasi WHERE jawaban='Ya')");
if( !$rows ) :
    print_msg('Belum ada gejala terpilih!', 'warning');
    echo '<p><a class="btn btn-primary" href="aksi.php?m=konsultasi&act=new"><span class="glyphicon glyphicon-refresh"></span> Konsultasi Lagi</a></p>';
else:

?>
<div class="panel panel-primary">
    <div class="panel-heading">        
        <h3 class="panel-title">Biodata Konsultasi</h3>  
    </div>
    <table class="table table-bordered table-hover">
    <thead>
                <th>Nama</th>
                <th>Umur</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Tanggal</th>
            </thead>
            <?php
            $q = esc_field($_GET['q']);
            $rowss = $db->get_results("SELECT * FROM tb_hasil  order by id desc limit 1");
            $no=0;
            foreach($rowss as $rowd):
			$IDHASIL=$rowd->id;
			?>
            <tr>
                <td><?=$rowd->nama ?></td>
                <td><?=$rowd->umur?></td>
                <td><?=$rowd->jk?></td>
                <td><?=$rowd->alamat?></td>
                <td><?=$rowd->tgl?></td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
<div class="panel panel-primary">
    <div class="panel-heading">        
        <h3 class="panel-title">Gejala Terpilih</h3>
    </div>
    <table class="table table-bordered table-hover table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Gejala</th>
        </tr>
    </thead>
    <?php
    $no=1;
    foreach($rows as $row):?>
    <tr>
        <td><?=$no++?></td>
        <td><?=$row->nama_gejala?></td>
    </tr>
    <?php endforeach;
    ?>
    </table>
</div>
<div class="panel panel-primary">
  
	
    
   <?php
   $total=100;
   $no=1;
   $rec="
     <div class='panel-heading'>  <h3 class='panel-title'>Hasil Analisa</h3> </div>
   <table class='table table-bordered table-hover table-striped'>
    <thead>
        <tr>
            <th>No</th>
            <th>Diagnosa</th>
			<th>Gejala Terpilih</th>
            <th>Bobot</th>
        </tr>
    </thead>";
	
            $rowss = $db->get_results("SELECT * FROM tb_diagnosa  order by kode_diagnosa asc");
            foreach($rowss as $rowd):
			
			$i=$no-1;
			$ng=$rowd->nama_diagnosa;
			$solusi=$rowd->keterangan;
			$gab=get_hitung($rowd->kode_diagnosa);
			$bbt=get_hitung2($rowd->kode_diagnosa);
			
				$arrND[$i]=$ng;
				$arrGAB[$i]=$gab;
				$arrBBT[$i]=$bbt;
				$arrSOL[$i]=$solusi;

			$rec.="<tr>
			<td>$no</td>
                <td>$ng</td>
                <td>$gab</td>
				<td>$bbt %</td>
            </tr>";
			
			$no++;
			endforeach;
			$rec.="</table>";
		//	echo $rec;

$MAX=max($arrBBT);

		$array_count = count($arrBBT);
        for($x = 0; $x < $array_count; $x++){
            for($a = 0 ;  $a < $array_count - 1 ; $a++){
                if($a < $array_count ){
                    if($arrBBT[$a] < $arrBBT[$a + 1] ){
                            swap($arrBBT, $a, $a+1);
							 swap($arrND, $a, $a+1);
							  swap($arrGAB, $a, $a+1);
							   swap($arrSOL, $a, $a+1);
							   
                    }
				}
            }
		}
		
		  $rec=" <div class='panel-heading'>  <h3 class='panel-title'>Hasil Analisa</h3> </div>
   <table class='table table-bordered table-hover table-striped'>
    <thead>
        <tr>
            <th>No</th>
            <th>Diagnosa</th>
			<th>Gejala Terpilih</th>
            <th>Kemungkinan</th>
        </tr>
    </thead>";
	
	
    $hasil="";
	$ambang=50;
	$ada=0;
	$bbt0=$arrBBT[0];
	
$kepercayaan="";	
$hasil_konsultasi="";
		for($j=0;$j<count($arrBBT);$j++){
			$no=$j+1;
			$ng=$arrND[$j];
			$gab=$arrGAB[$j];
			$bbt=$arrBBT[$j];
			$solusi=$arrSOL[$j];
			
			//
			if($bbt >= ($bbt0-0.01)  && $bbt>=$ambang ){
				$ada++;
				$kepercayaan.="|$ng ($bbt%)  ";
				$hasil_konsultasi.="|$ng ";
				$hasil.="<tr>
				<td>$ada</td>
					<td>$ng</td>
					<td>$solusi</td>
				</tr>";
			}
			
			$rec.="<tr>
			<td>$no</td>
                <td>$ng</td>
                <td>$gab</td>
				<td>$bbt%</td>
            </tr>";
			
		}
		
$rec.="</table>";


$hasil0=" <div class='panel-heading'>  <h3 class='panel-title'>Hasil $ada Diagnosa</h3> </div>
   <table class='table table-bordered table-hover table-striped'>
    <thead>
        <tr>
            <th>No</th>
            <th>Diagnosa</th>
            <th>Solusi</th>
        </tr>
    </thead>";
	
$hasil.="</table>";
$hasil=$hasil0.$hasil;

echo $rec;
?>
	
    

	
	
<?php


if($ada<1){
	$hasil="<tr><td colspan='2'><font color='green'><strong>Anda Tidak Terdiagnosa Penyakit ISPA</strong></font></tr>";
}



?>	
    <div class="panel-body">
        <table class="table table-bordered">
           <?php

				echo $hasil;
				
				
			 $SQL="UPDATE  tb_hasil set `hasil_konsultasi`='$hasil_konsultasi',`kepercayaan`='$kepercayaan' where `id`='$IDHASIL'";	
			$db->query($SQL);
			
		   ?>
        </table>
        
        <p>
            <a class="btn btn-primary" href="aksi.php?m=konsultasi&act=new"><span class="glyphicon glyphicon-refresh"></span> Konsultasi Lagi</a>
            <a class="btn btn-default" href="cetak.php?m=konsultasi" target="_blank"><span class="glyphicon glyphicon-print"></span> Cetak</a>
		</p>
		
		<div class="panel-body">
		<?php
if($ada>1){
	echo "<tr><td colspan='2'><font color='red'><strong>*Untuk Mendapatkan Diagnosa Yang Lebih Pasti, Silahkan Konsultasikan Hasil Diagnosa Tersebut Ke Pakar ISPA Atau Dokter Spesialis Paru</strong></font></tr>";
}



?>	
    </div>
</div>
<?php endif;?>