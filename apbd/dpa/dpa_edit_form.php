<?php
    
function dpa_edit_form(){
	drupal_add_css('files/css/kegiatancam.css');
	//drupal_add_css('files/css/hxradios.css');
	
	//$form['formdata']['desc']= array(
	//	'#type'         => 'markup', 
	//	'#value'=> '', 
	//); 
	
	$title = 'Penomoran DPA-SKPD';
	drupal_set_title($title);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');

	$pquery = "select uk.kodedinas, uk.kodeuk, uk.namasingkat, d.penno, d.pentgl, d.penok, d.btlno, d.btltgl, d.btlok, d.blno, d.bltgl, d.blok from {unitkerja} uk inner join dpanomor d on uk.kodeuk=d.kodeuk where uk.aktif=1 order by kodedinas" ;
	$resdetil = db_query($pquery);
	$weight = 0;
	$rows= array();
	if ($resdetil) {
		//drupal_set_message('res ok');
		while ($data = db_fetch_object($resdetil)) {
			$weight += 1;
			
			$rows[] = array (
							'id' => $weight,
							'nomor' => '<p align="right">' . $weight . '</p>',
							'kodeuk' => $data->kodeuk,
							'skpd' => $data->namasingkat,
							'penno' => $data->penno,
							'pentgl' => $data->pentgl,
							'penok' => $data->penok,
							'btlno' => $data->btlno,
							'btltgl' => $data->btltgl,
							'btlok' => $data->btlok,
							'blno' => $data->blno,
							'bltgl' => $data->bltgl,
							'blok' => $data->blok,
							'weight' => $weight,
						);

		}
	}
	
    // Tabledrag element
    foreach ($rows as $row) {

        $form['tabledragrows'][$row['id']]['nomor_' . $row['id']] = array(
			'#type' => 'markup',
			'#value' => $row['nomor'],
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size' => 5,
        );

        $form['tabledragrows'][$row['id']]['kodeuk_' . $row['id']] = array(
            '#type' => 'hidden',
            '#default_value' => $row['kodeuk'],
            '#size' => 10,
        );
		$form['tabledragrows'][$row['id']]['skpd_' . $row['id']] = array(
			'#type' => 'markup',
			'#value' => $row['skpd'],
			'#size' => 30,
		);

		$pentype = 'hidden';
		$penoktype = 'hidden';
		$sql = 'select kodeuk from anggperuk where {kodeuk}=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($row['kodeuk']));
		if ($res) {
			//drupal_set_message('res ok');
			if ($data = db_fetch_object($res)) {
				$pentype = 'textfield';
				$penoktype = 'checkbox';
			}
		}

		$form['tabledragrows'][$row['id']]['penno_' . $row['id']] = array(
			'#type' => $pentype,
			'#default_value' => $row['penno'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['pentgl_' . $row['id']] = array(
			'#type' => $pentype,
			'#default_value' => $row['pentgl'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['penok_' . $row['id']] = array(
			'#type' => $penoktype,
			'#value' => $row['penok'],
			'#size' => 10,
		);		
		$form['tabledragrows'][$row['id']]['btlno_' . $row['id']] = array(
			'#type' => 'textfield',
			'#default_value' => $row['btlno'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['btltgl_' . $row['id']] = array(
			'#type' => 'textfield',
			'#default_value' => $row['btltgl'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['btlok_' . $row['id']] = array(
			'#type' => 'checkbox',
			'#value' => $row['btlok'],
			'#size' => 10,
		);

		$form['tabledragrows'][$row['id']]['blno_' . $row['id']] = array(
			'#type' => 'textfield',
			'#default_value' => $row['blno'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['bltgl_' . $row['id']] = array(
			'#type' => 'textfield',
			'#default_value' => $row['bltgl'],
			'#size' => 15,
		);
		$form['tabledragrows'][$row['id']]['blok_' . $row['id']] = array(
			'#type' => 'checkbox',
			'#value' => $row['blok'],
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

	
	$form['submit'] = array(
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan',
		//'#weight' => 7,
	);
    return $form;
}
function dpa_edit_form_validate($form, &$form_state) {

}
function dpa_edit_form_submit($form, &$form_state) {

	//$maxdetil = $form_state['values']['maxdetil'];
	
	/*
	for ($x = 1; $x <= 2; $x++) {
		
		$kodeuk = $form_state['values']['kodeuk_' . $x];
		
		$penno = $form_state['values']['penno_' . $x];
		$pentgl = $form_state['values']['pentgl_' . $x];
		$penok = $form_state['values']['penok_' . $x];

		$btlno = $form_state['values']['btlno_' . $x];
		$btltgl = $form_state['values']['btltgl_' . $x];
		$btlok = $form_state['values']['btlok_' . $x];

		$blno = $form_state['values']['blno_' . $x];
		$bltgl = $form_state['values']['bltgl_' . $x];
		$blok = $form_state['values']['blok_' . $x];
	*/
		//Simpan 
		/*
		$sql = 'update {dpanomor} set penno=\'%s\', pentgl=\'%s\', penok=\'%s\', btlno=\'%s\', btltgl=\'%s\', btlok=\'%s\', blno=\'%s\', bltgl=\'%s\', blok=\'%s\' where kodeuk=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array($penno, $pentgl, $penok, $btlno, $btltgl, $btlok, $blno, $bltgl, $blok, $kodeuk));
		*/
		//$sql = 'update {dpanomor} set penno=\'%s\', pentgl=\'%s\', penok=\'%s\', btlno=\'%s\', btltgl=\'%s\', btlok=\'%s\', blno=\'%s\', bltgl=\'%s\', blok=\'%s\' where kodeuk=\'%s\'';
		//$res = db_query(db_rewrite_sql($sql), array('a', 'b', 1, 'c', 'd', 1, 'e', 'f', 1, '58'));
		//$sql = "update {dpanomor} set btlno='a' where kodeuk='58'";
		//$res = db_query($sql);
		

	//}	

	//if ($res)
	//	drupal_set_message('Penyimpanan data berhasil dilakukan');
	//else
	//	drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	//drupal_goto('apbd/kegiatanskpd');
	
	$form_state['redirect'] = 'apbd/kegiatanskpdsd';    
}


function dpa_edit_theme() {
    return array(
        'dpa_edit_form' => array(
            'arguments' => array(
                'form' => NULL
            ),
        ),
    );
}

function theme_dpa_edit_form($form) {
    $table_rows = array();
 
    if (is_array($form['tabledragrows'])) {
        //loop through each "row" in the table array
        foreach ($form['tabledragrows'] as $id => $row) {
            //we are only interested in numeric keys
            if (intval($id)) {
                $this_row = array();
				
				$this_row[] = drupal_render($form['tabledragrows'][$id]['nomor_' . $id]);
                $this_row[] = drupal_render($form['tabledragrows'][$id]['skpd_' . $id]);
				
                $this_row[] = drupal_render($form['tabledragrows'][$id]['penno_' . $id]);
				$this_row[] = drupal_render($form['tabledragrows'][$id]['pentgl_' . $id]);
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['penok_' . $id]);
				
                $this_row[] = drupal_render($form['tabledragrows'][$id]['btlno_' . $id]);
				$this_row[] = drupal_render($form['tabledragrows'][$id]['btltgl_' . $id]);
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['btlok_' . $id]);

                $this_row[] = drupal_render($form['tabledragrows'][$id]['blno_' . $id]);
				$this_row[] = drupal_render($form['tabledragrows'][$id]['bltgl_' . $id]);
				//$this_row[] = drupal_render($form['tabledragrows'][$id]['blok_' . $id]);

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
				array('data' => 'No.',  'width'=> '3px'),
				array('data' => 'SKPD',  'width'=> '200px'),
				
				array('data' => 'No. DPA Pendptn',  'width' => '50px'),
				array('data' => 'Tgl. DPA Pendptn',  'width' => '50px'),
				//array('data' => 'OK',  'width' => '10px'),
				
				array('data' => 'No. DPA BTL',  'width' => '50px'),
				array('data' => 'Tgl. DPA BTL',  'width' => '50px'),
				//array('data' => 'OK',  'width' => '10px'),

				array('data' => 'No. DPA BL',  'width' => '50px'),
				array('data' => 'Tgl. DPA BL',  'width' => '50px'),
				//array('data' => 'OK',  'width' => '10px'),

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
?>