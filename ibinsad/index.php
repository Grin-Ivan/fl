<?php
/**
 * Need support: PHP5.4 or PHP7, php mail, JSON
 */
error_reporting(E_ALL | E_STRICT);
//config
// $email_from	= 'nuovoannuncio@motasem.com';
// $email_to	= (isset($_GET['email']))? $_GET['email'] : 'sekretlog@yandex.ru';
$email_to	= (isset($_GET['email']))? $_GET['email'] : 'ibinsad@gmail.com';
$email_from	= 'nuovoannuncio-motasem-com@autodrive02.ru';
$subject	= 'New publication';

$word	= [
	'lavapezzi',
	'lavametalli',
	'lava pezzi',
];
//config end

$site	= [
	'kijiji'	=> 'http://www.kijiji.it/$find$/?entryPoint=sb',
	'subito'	=> 'http://www.subito.it/annunci-italia/vendita/usato/?q=$find$',
	'bakeca'	=> 'http://www.bakeca.it/annunci/tutte-le-categorie/?keyword=$find$',
];

$new_art	= [];
$temp_name	= 'temp.json';

if(!file_exists($temp_name)) file_put_contents($temp_name, '');
$json		= file_get_contents($temp_name);
$res_url	= json_decode($json, 1);

if(!$res_url) $res_url = [];

foreach($site as $name => $search_){
	foreach($word as $text_){
		$text	= urlencode($text_);
		$search	= str_replace('$find$', $text, $search_);
		$html	= file_get_contents($search);

		preg_match_all('/data-id="(.*?)"/i', $html, $res);														//id

		foreach($res['1'] as $val){
			preg_match('/href="(http\:\/\/.*?'.$val.'.*?)"/i', $html, $r);										//links

			if(!(isset($res_url["$name"]) && isset($res_url["$name"]["$text_"]) && in_array($r['1'], $res_url["$name"]["$text_"]))){
				$ii = preg_match('/"'.$val.'".{1,600}?original=.*?\/\/(.*?)"/is', $html, $i);					//images
				if(!$ii) $ii = preg_match('/"'.$val.'".{1,600}?src=.*?\/\/(.*?)"/is', $html, $i);				//images
				if(!$ii) $i['1'] = 'img04.rl0.ru/pgc/432x288/5464d095-a7a3-5926-a7a3-5929fe43c077.photo.0.jpg';	//images

				$res_url["$name"]["$text_"][] = $r['1'];
				if(count($res_url["$name"]["$text_"]) > 70) array_shift($res_url["$name"]["$text_"]);
				$new_art[] = '<tr><td><img width="180" src="http://'.$i['1'].'"/></td><td><a target="_blank" href="'.$r['1'].'">'.$r['1'].'</a></td></tr>';
			}
		}
	}
}


$json = json_encode($res_url);
file_put_contents($temp_name, $json);

$msg = (!empty($new_art))? '<table>'.implode('', $new_art).'</table>' : 'Not found';
mail($email_to, $subject, $msg, 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=utf-8');//."\r\n".'From: '.$email_from);


?><html>
<head></head>
<body>

<form method="get">
	<p>Enter your email address and click Check to send an email with new offers in the mail.
	<input name="email" type="email" placeholder="E-mail for result" title="E-mail for result" value="<?=$email_to ?>">
	<input type="submit" value="Check"></p>
</form>


<h2>New publication</h2>
<table><?=implode('', $new_art); ?></table>

<h2>All publication</h2>
<pre>
<?php var_dump($res_url); ?>
</pre>
</body>
</html>