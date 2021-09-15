<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email enrichment app</title>
    <!-- Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <!-- Own css -->
    <link rel="stylesheet" href="assets/css/style.css">
	<!-- PAPA Parse json to csv handler-->
    <script src="assets/libs/papaparse.min.js"></script>
</head>
<body class="d-flex h-100 text-white bg-dark">
    
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
		<header class="mb-auto">
			<div>
				<h3 class="float-md-start mb-0">Email enrichment tool</h3>
			</div>
		</header>

		<main class="px-3">
			<div class="content">

				<div class="first-block">
					<label for="input" class='text-left mb-2'>Please upload a .csv file with leads</label> <br>
					<input type="file" id="selectFiles_leads" value="Import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /> <br />
					<button id="import">Import leads</button>
				</div>


				<div class="first-block">
					<label for="input" class='text-left mb-2'>Please upload a .csv file with companies</label> <br>
					<input type="file" id="selectFiles_companies" value="Import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /> <br />
					<button id="import_companies">Import companies</button>
				</div>

				<div class="second-block">
					<button class="mt-5" id="enrich_leads" onclick="enrich_leads()" disabled>Enrich leads</button>
                    <button class="mt-5" id="enrich_companies" onclick="enrich_companies()" disabled>Enrich companies</button>
					
					<h2 class="total-processed mt-5"></h2>
					<h2 class="total-enriched"></h2>
					<h4 class="execution-time"></h4>
				</div>

			</div>
		</main>

		<footer class="mt-auto text-white-50">
		<a href="https://github.com/jvicensfarrus" target="_blank" class="developers">
					<span>Developed with <svg class="svg-inline--fa fa-heart fa-w-18" aria-hidden="true" data-prefix="fas" data-icon="heart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M414.9 24C361.8 24 312 65.7 288 89.3 264 65.7 214.2 24 161.1 24 70.3 24 16 76.9 16 165.5c0 72.6 66.8 133.3 69.2 135.4l187 180.8c8.8 8.5 22.8 8.5 31.6 0l186.7-180.2c2.7-2.7 69.5-63.5 69.5-136C560 76.9 505.7 24 414.9 24z"></path></svg> by Jordi Vicens Farrus</span>
				</a>
		</footer>
    </div>

    <div class="popup " style="display:none;">
        <div class="loading">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <h2> This may take some minutes.. ☕️ <br> 
        Please don't close the window. <br> 
        You can minimise this window or work in other tabs. <br> 
        Once it's ready it will download the file automatically.</h2>

    </div>
<script src="assets/js/scripts.js"></script>
</body>
</html>