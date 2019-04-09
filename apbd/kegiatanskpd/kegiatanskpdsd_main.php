<?php
function kegiatanskpdsd_main($arg=NULL, $nama=NULL) {
	
	$kodeuk = arg(2);	
	
	//drupal_set_message('1-' . arg(1));
	//drupal_set_message('2-' . arg(2));
	//drupal_set_message('3-' . arg(3));
	//drupal_set_message('4-' . arg(4));
	//drupal_set_message('5-' . arg(5));
	
	$output = GenDataView($kodeuk);
	return $output;
}

function GenDataView($kodeuk) {
	drupal_add_css('files/css/table.css');
	drupal_add_css('files/css/kegiatancam.css');

	//$total = 2113666000000;
	
	//drupal_set_message($kodeuk);

	$strinfo = "<table style='width:100%'>";

	if ($kodeuk != '') {
		$where = sprintf(' and kodeuk=\'%s\'', $kodeuk);
	}
	$sql = 'select count(kodekeg) jumlahkeg, sum(total) penetapansd, sum(totalp) totalsd from {kegiatanperubahan} where inaktif=0 ' . $where;
	$res = db_query($sql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumlahkeg = $data->jumlahkeg;
			$total = $data->totalsd;
			$penetapansd = $data->penetapansd;
		}
	}

	$sql = 'select sumberdana1, count(kodekeg) jumlahkeg, sum(totalp) jumlahsd from {kegiatanperubahan} where inaktif=0 ' . $where . ' group by sumberdana1';
		
	$res = db_query($sql);

	$totalp = 0;
	if ($res) {
		while ($data = db_fetch_object($res)) {
			$persen = ($data->jumlahsd/$total) * 100;
			$totalp += $persen;
		  $strinfo .= "<tr>" .
						"<td width='35%'>- " . $data->sumberdana1 . "</td>" .
						"<td width='20%'; align='right'>" . apbd_fn($data->jumlahkeg) . "</td>" .
						"<td width='25%'; align='right'>" . apbd_fn($data->jumlahsd) . "</td>" .
						"<td width='20%'; align='right'>" . apbd_fn1($persen) . "%</td>" .
					  "</tr>";
		}
	}

	$strinfo .= "<tr class='blue'>" .
		"<td>TOTAL</td>" .
		"<td align='right'>" . apbd_fn($jumlahkeg) . "</td>" .
		"<td align='right'>" . apbd_fn($total) . "</td>" .
		"<td align='right'>" . apbd_fn1($totalp) . "%</td>" .
	  "</tr>" .
	"</table>";

	$strinfo .= "<tr>" .
		"<td></td>" .
		"<td></td>" .
		"<td></td>" .
		"<td></td>" .
	  "</tr>" .
	"</table>";
	
    $btn = l("Sumber Dana", 'apbdsumberdana' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
    $btn .= "&nbsp;" . l("Daftar Kegiatan", 'apbd/kegiatanskpd' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	
	$strinfo .= $btn;
    return $strinfo;	
}



?>