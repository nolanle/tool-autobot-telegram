<?php
	require __DIR__ . '/vendor/autoload.php';

	/**
	* 
	*/
	class AureusPrice
	{
		private $bot_api_key;
		private $bot_username;
		
		function __construct($bot_api_key, $bot_username)
		{
			$this->$bot_api_key  = $bot_api_key;
			$this->$bot_username = $bot_username;
		}


		public function sendMessage($chatId, $text)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot448676978:AAGrOr_pNbbMD5z7c0xZHGX5SmQb3aKTruw/sendMessage");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 
				http_build_query([
					'chat_id' 		=> $chatId,
					'text' 			=> $text,
					'parse_mode'	=> 'html'
				])
			); //curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=@aureus_price_ccex&text=Hello Members");

			// receive server response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);

			// further processing
			//if ($server_output == "OK") { } else { }
			$myfile = file_put_contents('logs.txt', $server_output.PHP_EOL , FILE_APPEND | LOCK_EX);
		}

		public function getPrice($apiUrl)
		{
			$client = new GuzzleHttp\Client;
			try {
			    $response = $client->get($apiUrl);
			    return $body = json_decode((string) $response->getBody(), true);    
			}
			catch (GuzzleHttp\Exception\ClientException $e) {
			    //$response = $e->getResponse();
			    //$responseBodyAsString = $response->getBody()->getContents();
			    return null;
			}
		}


	}

	$bot_api_key  = '448676978:AAGrOr_pNbbMD5z7c0xZHGX5SmQb3aKTruw';
	$bot_username = 'aureus_price_bot';

	$api_url = "https://c-cex.com/t/aurs-btc.json";
	$chat_id = '@aureus_price_ccex';
	// load config
	$aureusPrice = new AureusPrice($bot_api_key, $bot_username);
	
	$usdVND = "22.5";
	
	// build message for aurs in btc
	$result = $aureusPrice->getPrice($api_url)['ticker'];
	$message = "💰📌 <b>AUREUS PRICE</b> 📌💰
	
\xE2\x9C\x85<b>1 AURS = ".$result['lastbuy'] . " BTC</b>";
	//$aureusPrice->sendMessage($chat_id, $message);

	// build message for aurs in usd
	$api_url = 'https://api.coinmarketcap.com/v1/ticker/bitcoin/';
	$btc_price_usd = $aureusPrice->getPrice($api_url)[0]['price_usd'];
	
	$aureusUSD = (double)$result['lastbuy']*$btc_price_usd;
	$message .= "

\xE2\x9C\x85<b>1 AURS = ".number_format($aureusUSD, 2, '.', ',')." USD</b> 🇺🇸

\xE2\x9C\x85<b>1 AURS = ".number_format($aureusUSD * 0.85, 2, '.', ',')." EUR</b> 🇪🇺

\xE2\x9C\x85<b>1 AURS = ".number_format($aureusUSD * 4.28, 2, '.', ',')." MYR</b> 🇲🇾

\xE2\x9C\x85<b>1 AURS = ". number_format($aureusUSD * 22500, 0, '.', ',') ." VND</b> 🇻🇳

🔜 Updated : ".date('d-m-Y H:i:s A')."

👉 Follow @aureus_price_ccex 📌";
	$aureusPrice->sendMessage($chat_id, $message);

	//echo "<pre>";
	//print_r($result);
	//echo "</pre>";

	//number_format((double)$result['lastsell']*$btc_price_usd, 2, '.', '');

?>
