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
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body class="container">
		<img src="" class="img-fluid" alt="" id="headerImg">
		<h1>Falsettos Tickets</h1>
		
		<!-- All Performances -->
		<div class="card">
			<div class="card-header">
				Performances
			</div>
			
			<!-- Performance Template -->
			<ul class="list-group list-group-flush">
				<div></div>
				<li id="performanceTemplate" class="list-group-item">
					<div class="card-body row">
						
						<!-- Performance Date -->
						<span class="perf_date col-auto"></span>
						
						<!-- Number of Tickets Left -->
						<i class="perf_tix_left col"></i>
						
						<!-- Buy Now -->
						<!--<button class="btn btn-danger col">Buy Now!</button>-->
					</div>
				</li>
			</ul>
		</div>
		
		
		<script>
			
			// Load Ticketing Data
			var jsObject = {<?=$matches[1][0];?>};
			console.debug(jsObject);
			
			// Set Header Image
			document.getElementById("headerImg").src = "https://tickets.uchicago.edu"+jsObject["searchResults"][0][20];
			
			// Set Template Element
			template = document.getElementById("performanceTemplate");
			
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
				
				// Set Number of Tickets Left
				thisPerformance.querySelector('.perf_tix_left').textContent = performance[16] + " tickets left";
				
				
				
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