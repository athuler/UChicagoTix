<?php
	
// Load Show URL info
$json = json_decode(file_get_contents("show_urls.json"), true);

// Falsettos is the default URL
//$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=80CABB0D-1081-4DC3-BDEC-FFBD195F45E1";

// Check Whether a Show URL is Provided
if(isset($_GET["show"]) ){
	
	$_GET["show"] = strtolower($_GET["show"]);
	
	if(array_key_exists($_GET["show"], $json)){
		
		// Passed is a custom URL
		$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=" . $json[$_GET["show"]];
		
	} else if (in_array(strtoupper($_GET["show"]), $json)){
	
		// Passed is an ID which has a custom URL
		// Redirect to the custom URL
		header("Location: " . array_search(strtoupper($_GET["show"]), $json));
	
	} else {
	
		// Passed is not a Custom Show URL
		$url = "https://tickets.uchicago.edu/Online/default.asp?doWork::WScontent::loadArticle=Load&BOparam::WScontent::loadArticle::article_id=" . $_GET["show"];
	}
}

function getPageData($file) {
	$pattern = '~var articleContext = {(.*?)};~s';
	preg_match_all($pattern, $file, $matches);
	return($matches);
}


$displaying_show = isset($url);
if($displaying_show) {
	
	// Show is being shown

	// Scrape Ticketing Webpage
	$opts = array('http'=>array('header' => "User-Agent:" . $_SERVER['HTTP_USER_AGENT'])); 
	$context = stream_context_create($opts);
	$file = @file_get_contents($url,false,$context);

	// Check if content is valid
	if($file === FALSE) {
		header("Location: ./?error=invalidshow");
	}

	// Save Cookies
	$cookies = array();
	foreach ($http_response_header as $hdr) {
		if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
			parse_str($matches[1], $tmp);
			$cookies += $tmp;
		}
	}

	// Set Cookies
	foreach($cookies as $cookieName => $cookieValue) {
		setcookie($cookieName,$cookieValue);
	}

	// Get Page Data
	$matches = getPageData($file);

	// Get header image
	$pattern = '~<img alt="" src="/(.*?)"~s';
	preg_match_all($pattern, $file, $header_matches);
	if(sizeof($header_matches[1]) != 0) {
		$header_src = $header_matches[1][0];
	} else{
		$header_src = "";
	}

	// Get show title
	$pattern = '~<title>(.*?)</title>~s';
	preg_match_all($pattern, $file, $title_matches);
	$show_name = $title_matches[1][0];
} else {

	// Displaying the homepage
	
	
	$opts = array('http'=>array('header' => "User-Agent:" . $_SERVER['HTTP_USER_AGENT'])); 
	$context = stream_context_create($opts);
	$file = @file_get_contents("https://tickets.uchicago.edu",false,$context);

	// Check if content is valid
	if($file === FALSE) {
		exit("A major unexpected error occured.");
	}
	
	$home_data = getPageData($file);
}

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
		
		<?php if($displaying_show) { ?>		
			<title><?=$show_name;?> Tickets</title>
		<?php } else { ?><title>UChicago Ticketing System</title><?php } ?>
		
		<!-- Bootstrap -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body class="container">
		<!-- About This Project Modal -->
		<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="exampleModalLabel">About This Project</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						This is an <b>unofficial</b> ticketing site made by <a href="https://andreithuler.com/" target="__blank">Andrei Thüler</a> built on top of <a href="https://tickets.uchicago.edu/" target="__blank">UChicago's Ticketing System</a> which is powered by <i>AudienceView</i>.
					</div>
					<div class="modal-footer justify-content-center">
						<!--🧑‍💻 <a href="https://github.com/athuler/UChicagoTix/" target="__blank">Source Code</a> |-->
						🪲 <a href="https://github.com/athuler/UChicagoTix/issues" target="__blank">Report a bug</a> |
						📝 <a href="https://andreithuler.com/projects/" target="__blank">Other Projects</a> |
						📧 <a href="http://eepurl.com/gnbarf" target="__blank">Email List</a>
					</div>
				</div>
			</div>
		</div>
		<!-- End Modal -->
		
		<!-- Displaying a Show -->
		<?php if($displaying_show) { ?>
		<img src="https://tickets.uchicago.edu/" class="img-fluid rounded mx-auto d-block" alt="" id="headerImg" style="">
		<br/>
		<div class="row">
			<!-- Back to home -->
			<a href="./" class="col-auto"><button class="btn btn-outline-secondary">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
				</svg>
				Home
			</button></a>
			
			<div class="col-auto float-end ms-auto">
				<!-- About the project -->
				<button class="btn btn-outline-secondary col-auto" data-bs-toggle="modal" data-bs-target="#aboutModal">What's this?</button>
				
				<!-- Dark Mode Toggle -->
				<button id="darkModeSwitch" class="btn btn-outline-secondary col-auto">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-stars-fill" viewBox="0 0 16 16">
						<path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
						<path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
					</svg>
				</button>
			</div>
		</div>
		<h1><?=$show_name;?></h1>
		
		<p id="locationLine"><i id="show_location"></i> (<a id="location_map" href="" target="__blank">Map</a>)</p>
		
		<p id="showDescription"></p>
		
		<!-- All Performances -->
		<div class="card" id="performancesCard">
			<div class="card-header">
				<span id="num_performances"></span>
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
			var headerImgUrl = "<?=$header_src;?>";
			if(headerImgUrl != "") {
				document.getElementById("headerImg").src = "https://tickets.uchicago.edu/"+headerImgUrl;
			} else {
				document.getElementById("headerImg").src = "https://tickets.uchicago.edu/content/Images/UC%20Arts%20Images/UChicagoArts_Horizontal_GreyMaroonRGB.png";
			}
			
			// Set Template Element
			template = document.getElementById("performanceTemplate");
			
			// Check if show has performances
			if("searchResults" in jsObject) {
				
				// Set Show Location
				if(jsObject["searchResults"][0][63] != "") {
					document.getElementById("show_location").textContent = jsObject["searchResults"][0][63];
					
					// Set Map
					document.getElementById("location_map").href = "https://www.google.com/maps/search/?api=1&query="+jsObject["searchResults"][0][55]+" "+jsObject["searchResults"][0][56]+" "+jsObject["searchResults"][0][58];
				} else {
					document.getElementById("locationLine").remove();
				}
				
				
				
				// Set Number of Performances / Subscriptions / Merchandise
				var num_performances = 0;
				var num_subscriptions = 0;
				var num_merch = 0;
				for (const performance of jsObject["searchResults"]) {
					switch(performance[1]) {
						case "P":
							num_performances += 1;
							break;
						case "B":
							num_subscriptions += 1;
							break;
						case "M":
							num_merch += 1;
							break;
						default:
							console.log(performance[1]);
					}
				}
				var perf_string = "";
				if (num_performances > 0) {
					perf_string += num_performances + " Performances ";
				}
				if (num_subscriptions > 0) {
					perf_string += num_subscriptions + " Subscriptions ";
				}
				if (num_merch > 0) {
					perf_string += num_merch + " Merchandise ";
					console.log("---");
				}
				document.getElementById("num_performances").textContent = perf_string;
				
				
				// Set Show Description
				document.getElementById("showDescription").textContent = jsObject["searchResults"][0][5];
				
				// Display Each Performance
				for (const performance of jsObject["searchResults"]){
					console.debug(performance);
					
					// Create New Performance
					let thisPerformance = template.cloneNode(true);
					template.before(thisPerformance); // Set Position
					
					// Reset Performance ID
					thisPerformance.id = performance[0];
					
					// Set Performance Date & time
					if(performance[7] != "") {
						thisPerformance.querySelector('.perf_date').textContent = performance[7];
					} else {
						thisPerformance.querySelector('.perf_date').textContent = performance[6];
					}
					
					// Set Buy Button Session Token
					thisPerformance.querySelector('.sessionToken').value = jsObject["sToken"];
					
					// Set Buy Button Performance ID
					thisPerformance.querySelector('.perf_id1').value = performance[0];
					thisPerformance.querySelector('.perf_id2').value = performance[0];
					
					// Set Number of Tickets Left
					if(!isNaN(parseInt(performance[16]))) {
						if(performance[16] != 0) {
							thisPerformance.querySelector('.perf_tix_left').textContent = performance[16] + " tickets left";
						}
						if (performance[16] == 0){
							thisPerformance.querySelector('.perf_tix_left').textContent = "Sold Out!";
						}
					} else {
						thisPerformance.querySelector('.perf_tix_left').remove();
					}
					
					// Remove Buying form (buying mechanic currently unavailable)
					thisPerformance.querySelector('form').remove();
					
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
					}
					thisPerformance.querySelector('.btn').classList.add(btnClass);
					
				}
				
				// Delete Template
				template.remove();
				
			} else {
				// Delete performance Block
				document.getElementById("performancesCard").remove();
				
				// Remove Location Line
				document.getElementById("locationLine").remove();
			}
			
			
		</script>
		
		
		<?php } else { ?>
		
		<!-- Displaying the Homepage -->
		<br/>
		<div class="row">
			<!-- Home Title -->
			<h1 class="col-auto">UChicago Ticketing</h1><br/>
			
			<div class="col-auto float-end ms-auto">
				<!-- About This Project -->
				<button class="btn btn-outline-secondary col-auto" data-bs-toggle="modal" data-bs-target="#aboutModal">What's this?</button>
				
				<!-- Dark Mode Toggle -->
				<button id="darkModeSwitch" class="btn btn-outline-secondary col-auto">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-stars-fill" viewBox="0 0 16 16">
						<path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
						<path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
					</svg>
				</button>
			</div>
		</div>
		
		<br/>
		
		<div class="row row-cols-2 row-cols-md-4 g-4">
			<div id="performanceTemplate" class="col">
				<div class="card">
					<img src="" class="card-img-top" alt="">
					<div class="card-body">
						<!--<h5 class="card-title ">Show title</h5>-->
						<span class="card-text showName">Show Name</span>
						<a class="stretched-link"></a>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			
			// Load Show Data
			var jsObject = {<?=$home_data[1][0];?>};
			console.debug(jsObject);
			
			// Set Template Element
			template = document.getElementById("performanceTemplate");
			
			// Display Each Performance
			for (const show of jsObject["searchResults"]){
				console.debug(show);
				
				// Create New Performance
				let thisPerformance = template.cloneNode(true);
				template.before(thisPerformance); // Set Position
				
				// Reset Performance ID
				thisPerformance.id = show[0];
				
				
				// Set Performance Name
				if(show[5] != "") {
					thisPerformance.querySelector('.showName').textContent = show[5];
				} else {
					thisPerformance.querySelector('.showName').textContent = "No Name Provided";
				}
				
				// Set Performance Thumbnail
				if(show[20] != "") {
					thisPerformance.querySelector('.card-img-top').src = "https://tickets.uchicago.edu/"+show[20];
				} else {
				thisPerformance.querySelector('.card-img-top').src = "https://tickets.uchicago.edu/content/Images/UC%20Arts%20Images/UChicagoArts_Horizontal_GreyMaroonRGB.png";
				}
				
				// Set Performance Link
				thisPerformance.querySelector('a').href = show[0];
				
				// Set Performance Description
				//thisPerformance.querySelector('.card-text').textContent = show[1]+" - "+show[2];
			}
			
		</script>
		
		<?php } ?>
		
		<!-- Universal Scripts -->
		<script>
			function getCookie(cname) {
				let name = cname + "=";
				let decodedCookie = decodeURIComponent(document.cookie);
				let ca = decodedCookie.split(';');
				for(let i = 0; i <ca.length; i++) {
					let c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return "";
			}
			document.documentElement.setAttribute('data-bs-theme', getCookie("theme"));
			
			document.getElementById('darkModeSwitch').addEventListener('click',()=>{
				if (document.documentElement.getAttribute('data-bs-theme') == 'dark') {
					document.documentElement.setAttribute('data-bs-theme','light');
					document.cookie = "theme=light";
				}
				else {
					document.documentElement.setAttribute('data-bs-theme','dark');
					document.cookie = "theme=dark";
				}
			})
		</script>
		
		<!-- Universal Styles -->
		<style>
			#performanceTemplate {visibility: hidden;}
			#headerImg {height:100% !important;}
		</style>
		
		<!-- Bootstrap -->
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
	</body>
</html>