<?php
	

$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=80CABB0D-1081-4DC3-BDEC-FFBD195F45E1";


// Scrape Ticketing Webpage
$opts = array('http'=>array('header' => "User-Agent:AndreiThulerDotCom/1.0\r\n")); 
$context = stream_context_create($opts);
$file = file_get_contents($url,false,$context);


// Get Data
$pattern = '~var articleContext = {(.*?)};~s';
preg_match_all($pattern, $file, $matches);
//var_dump($matches);

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

		<title>Falsettos Tickets</title>
		
		<!-- Bootstrap -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	</head>
	<body>
		<h1>Falsettos Tickets</h1>
		
		<!-- All Performances -->
		<div class="row">
			<!-- Performance Template -->
			<div id="performanceTemplate" class="row">
				<span class="perf_date"></span>
			</div>
		</div>
		
		
		<script>
			
			// Load Ticketing Data
			var jsObject = {<?=$matches[1][0];?>};
			console.debug(jsObject);
			
			// Display Each Performance
			for (const performance of jsObject["searchResults"]){
				console.debug(performance);
				
				// Create New Performance
				template = document.getElementById("performanceTemplate");
				let thisPerformance = template.cloneNode(true);
				template.before(thisPerformance); // Set Position
				
				// Reset Performance ID
				thisPerformance.id = performance[0];
				
				// Set Performance Date & time
				thisPerformance.querySelector('.perf_date').textContent = performance[7];
			}
			
		</script>
		
		<!-- Bootstrap -->
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
	</body>
</html>