<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="x-ua-compatible" content="id-edge" />
	<title>Mis Puntos</title>
	<link rel="stylesheet" type="text/css" href="css/index.css?v=<?php echo uniqid(); ?>" />

	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-grid.min.css?v=<?php echo uniqid(); ?>">
	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-reboot.min.css?v=<?php echo uniqid(); ?>">
	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css?v=<?php echo uniqid(); ?>">

	<script src="js/jquery-3.5.1.min.js?v=<?php echo uniqid(); ?>"></script>
	<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?php echo uniqid(); ?>"></script>
	<script src="node_modules/bootstrap/dist/js/bootstrap.min.js?v=<?php echo uniqid(); ?>"></script>

	<script src="js/index.js?v=<?php echo uniqid(); ?>" defer></script>
</head>
<body>
	<main>
		<div class="content-1" style="justify-content: center !important;">
			<h1>Mis Puntos</h1>
		</div>
		<div class="content-1">
			<h2>Puntos Disponibles:</h2>
			<h2 class="points" id="points"></h2>
			<a href="#" onclick="event.preventDefault(); verPremios();">Canjear</a>
		</div>
		<div class="content-1-1">
			<a href="#" onclick="event.preventDefault(); verDetalle();">Ver Detalle</a>
		</div>
		<div class="content-2" id="data_div">
		</div>
	</main>

	<!-- Button trigger modal -->
	<button type="button" class="d-none" id="canjear_modal_btn" data-toggle="modal" data-target="#canjear_modal">
	</button>

	<!-- Modal -->
	<div class="modal fade" id="canjear_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="canjear_modal_title" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="canjear_modal_title">Premios Disponibles</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <div class="table-responsive" id="table_div">
	        </div>
	      </div>
	      <div class="modal-footer d-flex justify-content-between align-items-center" id="premios_footer">
	      </div>
	    </div>
	  </div>
	</div>

	<?php require_once 'loading_modal.php'; ?>

</body>
</html>