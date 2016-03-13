<?php
/**
 * Need support: PHP5+, php mail, JSON
 */
//config
$email_to	= 'grin.ivan.ru@yandex.ru';
// $email_to	= 'ibinsad@gmail.com';
$email_from	= 'nuovoannuncio@motasem.com';
$subject	= 'New publication';

$word	= [
	'lavapezzi',
	// 'lavametalli',
	// 'lava pezzi',
];
//config end

$site	= [
	'kijiji'	=> 'http://www.kijiji.it/$find$/?entryPoint=sb',
	// 'subito'	=> 'http://www.subito.it/annunci-italia/vendita/usato/?q=$find$',
	// 'bakeca'	=> 'http://www.bakeca.it/annunci/tutte-le-categorie/?keyword=$find$',
];

$new_art	= [];
$temp_name	= 'temp.json';
// $json		= file_get_contents($temp_name);
// $res_url	= json_decode($json, 1);

if(!$res_url) $res_url = [];
echo '<pre>';

foreach($site as $name => $search_){
	foreach($word as $text_){
		$text	= urlencode($text_);
		$search	= str_replace('$find$', $text, $search_);
		$html	= file_get_contents($search);

		preg_match_all('/data-id="(.*?)"/i', $html, $res);

		foreach($res['1'] as $val){
			preg_match_all('/href="(http\:\/\/.*?'.$val.')/i', $html, $r);

			if(!(isset($res_url["$name"]) && isset($res_url["$name"]["$text_"]) && in_array($r['1']['0'], $res_url["$name"]["$text_"]))){
				preg_match_all('/"'.$val.'".*?src="\/\/(.*?)"/is', $html, $i);
				// var_dump($i['1']['0']);

				$res_url["$name"]["$text_"][] = $r['1']['0'];
				if(count($res_url["$name"]["$text_"]) > 70) array_shift($res_url["$name"]["$text_"]);
				$new_art[] = '<tr><td><img src="http://'.$i['1']['0'].'"/></td><td><a target="_blank" href="'.$r['1']['0'].'">'.$r['1']['0'].'</a></td></tr>';
			}
		}
	}
}


echo 'New publication';
var_dump($new_art);

echo 'All publication';
var_dump($res_url);


// $json = json_encode($res_url);
// file_put_contents($temp_name, $json);

$msg = (!empty($new_art))? '<table>'.implode('', $new_art).'</table>' : 'Not found';
// $msg = (!empty($new_art))? '<ol><li>'.implode('</li><li>', $new_art).'</li></ol>' : 'Not found';
mail($email_to, $subject, $msg, 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=utf-8'."\r\n".'From: '.$email_from);