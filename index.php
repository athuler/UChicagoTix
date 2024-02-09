<?php
	
// Load Show URL info
$json = json_decode(file_get_contents("show_urls.json"), true);

// Falsettos is the default URL
$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=80CABB0D-1081-4DC3-BDEC-FFBD195F45E1";

// Check Whether a Show is Provided
if(isset($_GET["show"]) ){
	
	$_GET["show"] = strtolower($_GET["show"]);
	
	
	// Check Whether the Passed Show is Valid
	if(array_key_exists($_GET["show"], $json)){
		$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=" . $json[$_GET["show"]];
	} else {
		echo("Invalid Show!<br/>");
	}
}

// Scrape Ticketing Webpage
$opts = array('http'=>array('header' => "User-Agent:" . $_SERVER['HTTP_USER_AGENT'])); 
$context = stream_context_create($opts);
$file = file_get_contents($url,false,$context);

$cookies = array();
foreach ($http_response_header as $hdr) {
	if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
		parse_str($matches[1], $tmp);
		$cookies += $tmp;
		//var_dump($tmp);
	}
}
foreach($cookies as $cookieName => $cookieValue) {
	setcookie($cookieName,$cookieValue);
}
//var_dump($cookies);


// Get Performances Data
$pattern = '~var articleContext = {(.*?)};~s';
preg_match_all($pattern, $file, $matches);
//var_dump($matches);

// Get header image
$pattern = '~<img alt="" src="/(.*?)"/>~s';
preg_match_all($pattern, $file, $header_matches);
//var_dump($header_matches);
if(sizeof($header_matches[1]) != 0) {
	$header_src = $header_matches[1][0];
} else{
	$header_src = "";
}


// Get show title
$pattern = '~<title>(.*?)</title>~s';
preg_match_all($pattern, $file, $title_matches);
//var_dump($title_matches);
$show_name = $title_matches[1][0];


?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-NBY0B8EGKP"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'G-NBY0B8EGKP');
		</script>

		<title><?=$show_name;?> Tickets</title>
		
		<!-- Bootstrap -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body class="container">
		<img src="https://tickets.uchicago.edu/<?=$header_src;?>" class="img-fluid" alt="" id="headerImg">
		<h1><?=$show_name;?></h1>
		
		<p><i id="show_location"></i> (<a id="location_map" href="" target="__blank">Map</a>)</p>
		
		<!-- All Performances -->
		<div class="card">
			<div class="card-header">
				<span id="num_performances"></span> Performances
			</div>
			
			<!-- Performance Template -->
			<ul class="list-group list-group-flush">
				<div></div>
				<li id="performanceTemplate" class="list-group-item">
					<div class="card-body row">
						
						<!-- Performance Date -->
						<span class="perf_date col-auto"></span>
						
						<!-- Number of Tickets Left -->
						<i class="perf_tix_left col-auto"></i>
						
						<!-- Buy Now -->
						<a href="<?=$url;?>" target="__blank" class="col-auto float-end buying"><button class="btn">Buy Now!</button></a>
						
						<form style="visibility:hidden;">
							<input class="sessionToken" type="hidden" name="sToken" value="" />
							<input class="perf_id1" type="hidden" name="BOparam::WSmap::loadBestAvailable::performance_ids" value="" />
							<input class="perf_id2" type="hidden" name="BOparam::WSmap::loadBestAvailable::performance_id" value="" />
							<input type="hidden" name="createBO::WSmap" value="1" />
						</form>
					</div>
				</li>
			</ul>
		</div>
		
		<!-- Footer -->
		<br/><hr/>
		Made by <a href="https://andreithuler.com/" target="__blank">Andrei Thüler</a>.
		
		<script>
			
			// Load Ticketing Data
			var jsObject = {<?=$matches[1][0];?>};
			console.debug(jsObject);
			
			// Set Header Image
			//document.getElementById("headerImg").src = "https://tickets.uchicago.edu"+jsObject["searchResults"][0][20];
			
			// Set Template Element
			template = document.getElementById("performanceTemplate");
			
			// Set Show Location
			document.getElementById("show_location").textContent = jsObject["searchResults"][0][63];
			
			// Set Map
			document.getElementById("location_map").href = "https://www.google.com/maps/search/?api=1&query="+jsObject["searchResults"][0][55]+" "+jsObject["searchResults"][0][56]+" "+jsObject["searchResults"][0][58];
			
			// Set Number of Performances
			document.getElementById("num_performances").textContent = jsObject["searchResults"].length;
			
			// Display Each Performance
			for (const performance of jsObject["searchResults"]){
				console.debug(performance);
				
				// Create New Performance
				let thisPerformance = template.cloneNode(true);
				template.before(thisPerformance); // Set Position
				
				// Reset Performance ID
				thisPerformance.id = performance[0];
				
				// Set Performance Date & time
				thisPerformance.querySelector('.perf_date').textContent = performance[7];
				
				// Set Buy Button Session Token
				thisPerformance.querySelector('.sessionToken').value = jsObject["sToken"];
				
				// Set Buy Button Performance ID
				thisPerformance.querySelector('.perf_id1').value = performance[0];
				thisPerformance.querySelector('.perf_id2').value = performance[0];
				
				// Set Number of Tickets Left
				if(performance[16] != 0) {
					thisPerformance.querySelector('.perf_tix_left').textContent = performance[16] + " tickets left";
				} else {
					thisPerformance.querySelector('.perf_tix_left').textContent = "Sold Out!";
					thisPerformance.querySelector('form').remove();
				}
				
				// Set Whether Buying is possible
				if(performance[14] != "S") {
					thisPerformance.querySelector('.buying').removeAttribute("href");
					thisPerformance.querySelector('.btn').disabled = true;
					thisPerformance.querySelector('.btn').textContent = "Not on Sale";
				}
				
				// Set Buying button Color
				switch(performance[15]) {
					case "G":
						var btnClass = "btn-warning";
						break;
					case "L":
						var btnClass = "btn-danger";
						break;
					case "E":
						var btnClass = "btn-success";
						break;
					default:
						var btnClass = "btn-secondary";
						break;
				}
				thisPerformance.querySelector('.btn').classList.add(btnClass);
				
				
				
				
				
			}
			
			// Delete Template
			template.remove();
			
		</script>
		
		<style>
			#performanceTemplate {visibility: hidden;}
		</style>
		
		<!-- Bootstrap -->
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
	</body>
</html>