<?php
 
/**
 * For more informaiton about the different API controls available see:
 * http://api.drupal.org/api/drupal/developer--topics--forms_api_reference.html/6
 */
 
function apbdsumberdana_menu() {
    $items['apbdsumberdana'] = array(
        'title' => 'Sumber Dana',
        'page callback' => 'drupal_get_form',
        'page arguments' => array('apbdsumberdana_form'),
        'access callback' => TRUE,
    );
    return $items;
}
 
/**
 * This is form containing every form element type available.
 */
function apbdsumberdana_form() {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_css('files/css/hxradios.css');
	
	$kodeuk = arg(1);

	//$kodeuk = $_SESSION['kodeuk'];
	//$_SESSION['kodeuk'] = $kodeuk;
	if (isset($kodeuk)) 
		$_SESSION['kodeuksd'] = $kodeuk;
	else
		$kodeuk = $_SESSION['kodeuksd'];
	
	if ($kodeuk=='') $kodeuk = '81';
	//drupal_set_message($kodeuk);
		
	$title = 'Sumber Dana';
	$sql = 'select namauk from {unitkerja} where {kodeuk}=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodeuk));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$skpd = $data->namauk;
		}
	}

	$sql = 'select count(kodekeg) jumlah,sum(total) nominal from {kegiatanskpd} where inaktif=0 and {kodeuk}=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodeuk));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$jumlah = $data->jumlah;
			$nominal = $data->nominal;
		}
	}
	
	
	
	//$title =l($title, 'apbd/kegiatanskpd/rekening/' . $kodeuk, array('html'=>true));
	drupal_set_title($title);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');

	$pquery = "select kodedinas, kodeuk, namasingkat from {unitkerja} where aktif=1 order by kodedinas" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
	}
	
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
	
	$form['formdata']['jumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah banyaknya kegiatan',  
		'#size'         => 25, 
		'#default_value'=> $jumlah, 
	); 
	$form['formdata']['nominal']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Anggaran',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah anggaran',  
		'#size'         => 25, 
		'#default_value'=> apbd_fn($nominal), 
	); 
	
	$where = sprintf(" and kodeuk='%s'", db_escape_string($kodeuk));
    $sql = 'select kodekeg,kegiatan,total,sumberdana1 from {kegiatanskpd} where total>0 and inaktif=0 ' . $where  . ' order by jenis,kegiatan';

	//drupal_set_message($sql);
	$resdetil = db_query($sql);
	$weight = 0;
	$rows= array();
	if ($resdetil) {
		//drupal_set_message('res ok');
		while ($data = db_fetch_object($resdetil)) {
			$weight += 1;
			//drupal_set_message($data->uraian . ' - ' . $weight);
			
			$rows[] = array (
							'id' => $weight,
							'nomor' => '<p align="right">' . $weight . '</p>',
							'kodekeg' => $data->kodekeg,
							'kegiatan' => $data->kegiatan . ' (' .  apbd_fn($data->total) . ')',
							//'total' => '<p align="right">' . apbd_fn($data->total) . '</p>',
							'sumberdana1' => $data->sumberdana1,
							'e_sumberdana1' => $data->sumberdana1,
							'weight' => $weight,
						);

		}
	}
	

	$pquery = "select sumberdana,singkatan from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdana = array();
	while ($data = db_fetch_object($pres)) {
		$sumberdana[$data->sumberdana] = $data->singkatan;
	}
	
    // Tabledrag element
    foreach ($rows as $row) {

        $form['tabledragrows'][$row['id']]['nomor_' . $row['id']] = array(
			'#type' => 'markup',
			'#value' => $row['nomor'],
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size' => 5,
        );

        $form['tabledragrows'][$row['id']]['kodekeg_' . $row['id']] = array(
            '#type' => 'hidden',
            '#default_value' => $row['kodekeg'],
            '#size' => 10,
        );

        $form['tabledragrows'][$row['id']]['e_sumberdana1_' . $row['id']] = array(
            '#type' => 'hidden',
            '#default_value' => $row['e_sumberdana1'],
            '#size' => 10,
        );

		$form['tabledragrows'][$row['id']]['kegiatan_' . $row['id']] = array(
			'#type' => 'markup',
			'#value' => $row['kegiatan'],
			'#size' => 45,
		);

		/*
		$form['tabledragrows'][$row['id']]['total_' . $row['id']] = array(
			'#type' => 'markup',
			'#value' => $row['total'],
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size' => 15,
		);
		*/
		
        $form['tabledragrows'][$row['id']]['sumberdana1_' . $row['id']] = array(
            '#type' => 'radios',
			'#options'		=> $sumberdana,
            '#default_value' => $row['sumberdana1'],
            '#size' => 10,
        );
		
        // the weight form element.
		
        $form['tabledragrows'][$row['id']]['weight_' . $row['id']] = array(
            '#type' => 'weight',
            '#delta' => 50,
            '#default_value' => $row['weight'],
            '#attributes' => array('class' => 'weight'),
        );
    }
 
 	$form['maxdetil']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $weight, 
	); 

	$form['formdata']['submitshow'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		//'#weight' => 6,
	);
	$form['formdata']['submitrekap'] = array (
		'#type' => 'submit',
		'#value' => 'Rekap',
		//'#weight' => 6,
	);
	$form['formdata']['submitrekapskpd'] = array (
		'#type' => 'submit',
		'#value' => 'Rekap SKPD',
		//'#weight' => 6,
	);
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan',
		//'#weight' => 7,
	);
	$form['submitshow'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		//'#weight' => 6,
	);
	$form['submitrekap'] = array (
		'#type' => 'submit',
		'#value' => 'Rekap',
		//'#weight' => 6,
	);
	$form['submitrekapskpd'] = array (
		'#type' => 'submit',
		'#value' => 'Rekap SKPD',
		//'#weight' => 6,
	);
	$form['submit'] = array(
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan',
		//'#weight' => 7,
	);
    return $form;
}

/**
 * Implements hook_validate() for the apbdsumberdana_form() form.
 */
function apbdsumberdana_form_validate($form, &$form_state) {

} 
/**
 * Implements hook_submit() for the apbdsumberdana_form() form.
 */
function apbdsumberdana_form_submit($form, &$form_state) {
    
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitshow']) {
		$kodeuk = $form_state['values']['kodeuk'];
        $form_state['redirect'] = 'apbdsumberdana/' .  $kodeuk ;
	
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitrekap']) {
        $form_state['redirect'] = 'apbd/kegiatanskpdsd';
	
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitrekapskpd']) {
		$kodeuk = $form_state['values']['kodeuk'];
        $form_state['redirect'] = 'apbd/kegiatanskpdsd/' .  $kodeuk ;
	
	} else { 
		$maxdetil = $form_state['values']['maxdetil'];

		for ($x = 1; $x <= $maxdetil; $x++) {
			
			$sumberdana1 = $form_state['values']['sumberdana1_' . $x];
			$e_sumberdana1 = $form_state['values']['e_sumberdana1_' . $x];

			//Simpan detilnya
				
			if ($sumberdana1 != $e_sumberdana1) {
				$kodekeg = $form_state['values']['kodekeg_' . $x];

				$sql = 'update {kegiatanskpd} set sumberdana1=\'%s\' where kodekeg=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array($sumberdana1, $kodekeg));

			} 		
		}	

		if ($res)
			drupal_set_message('Penyimpanan data berhasil dilakukan');
		else
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	}	
}
 
/**
 * Implementation of hook_theme().
 */
function apbdsumberdana_theme() {
    return array(
        'apbdsumberdana_form' => array(
            'arguments' => array(
                'form' => NULL
            ),
        ),
    );
}
 
/**
 * Theme for form_element_form. Used to create the tabledrag element and then
 * render the rest of the form.
 */
function theme_apbdsumberdana_form($form) {
    $table_rows = array();
 
    if (is_array($form['tabledragrows'])) {
        //loop through each "row" in the table array
        foreach ($form['tabledragrows'] as $id => $row) {
            //we are only interested in numeric keys
            if (intval($id)) {
                $this_row = array();
				
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['iddetil_' . $id]);
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['space_' . $id]);
				$this_row[] = drupal_render($form['tabledragrows'][$id]['nomor_' . $id]);
                $this_row[] = drupal_render($form['tabledragrows'][$id]['kegiatan_' . $id]);
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['total_' . $id]);
                $this_row[] = drupal_render($form['tabledragrows'][$id]['sumberdana1_' . $id]);
				
                //Add the weight field to the row
                $this_row[] = drupal_render($form['tabledragrows'][$id]['weight_' . $id]);
 
                //Add the row to the array of rows
                $table_rows[] = array('data' => $this_row);
            }
        }
    }
 
    //Make sure the header count matches the column count
    //$header = array(
     //   "Person",
    //    "Email",
    //    "Weight"
    //);
	$header = array (
				//array('data' => '',  'width'=> '5px'),
				array('data' => 'No.',  'width'=> '3px'),
				array('data' => 'Kegiatan',  'width'=> '150px'),
				//array('data' => 'Anggaran',  'width' => '10px'),
				array('data' => 'Sumber Dana',  'width' => '250px'),
				array('data' => 'Weight'),
			); 
 
    $form['tabledragrows'] = array(
        '#value' => theme('table', $header, $table_rows, array('id' => 'id'))
    );
 
    $output = drupal_render($form);
 
    // Call add_tabledrag to add and setup the JavaScript
    // The key thing here is the first param - the table ID
    // and the 4th param, the class of the form item which holds the weight
    drupal_add_tabledrag('id', 'order', 'sibling', 'weight');
 
    return $output;
}