<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="x-ua-compatible" content="id-edge" />
	<title>Premios</title>
	<link rel="stylesheet" type="text/css" href="css/index.css?v=<?php echo uniqid(); ?>" />

	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-grid.min.css?v=<?php echo uniqid(); ?>">
	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap-reboot.min.css?v=<?php echo uniqid(); ?>">
	<link rel="stylesheet" type="text/css" href="node_modules/bootstrap/dist/css/bootstrap.min.css?v=<?php echo uniqid(); ?>">

	<script src="js/jquery-3.5.1.min.js?v=<?php echo uniqid(); ?>"></script>
	<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?php echo uniqid(); ?>"></script>
	<script src="node_modules/bootstrap/dist/js/bootstrap.min.js?v=<?php echo uniqid(); ?>"></script>

	<script src="js/awards.js?v=<?php echo uniqid(); ?>" defer></script>
</head>
<body>
	<div class="container-fluid">
		<div id="app" class="d-flex flex-column justify-content-between pt-3">
			<header>
			</header>
			<main class="py-3">
				<div class="row mb-3">
					<div class="col-8 mx-auto">
						<div class="d-flex justify-content-between align-items-center">
							<h1 class="mb-0">Premios</h1>
							<a href="#" onclick="crearPremio();" onclick="event.preventDefault();" class="btn btn-primary">
								Nuevo
							</a>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-8 mx-auto">
						<div>
							<table class="table table-bordered table-hover table-sm" id="awards_table">
								<thead>
									<tr>
										<th>Producto</th>
										<th>Rango</th>
										<th>Foto</th>
										<th>Editar</th>
										<th>Eliminar</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</main>
			<footer>
			</footer>
		</div>
	</div>


	<!-- Button trigger modal -->
	<button type="button" class="d-none" id="awards_modal_btn" data-toggle="modal" data-target="#awards_modal">
	</button>

	<!-- Modal -->
	<div class="modal fade" id="awards_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="awards_modal_title" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	    	<form id="awards_form" method="POST" enctype="multipart/form-data">
		      <div class="modal-header">
		        <h5 class="modal-title" id="awards_modal_title">Premios</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		      	<div class="row">
			        <div class="col-12 form-group">
			        	<label class="col-form-label" for="producto">Producto:</label>
			        	<input type="text" required name="producto" id="producto" class="form-control" />
			        </div>
		      	</div>

		        <div class="row">
			        <div class="col-6 form-group">
			        	<label class="col-form-label" for="rango_inicial">Rango Inicial:</label>
			        	<input type="text" required onkeypress="return validaNumero(event);" name="rango_inicial" id="rango_inicial" class="form-control" />
			        </div>
			        <div class="col-6 form-group">
			        	<label class="col-form-label" for="rango_final">Rango Final:</label>
			        	<input type="text" required onkeypress="return validaNumero(event);" name="rango_final" id="rango_final" class="form-control" />
			        </div>
		      	</div>

		        <div class="row">
			        <div class="col-12 form-group">
			        	<label class="col-form-label" for="foto">Foto:</label>
			        	<input type="file" name="foto" id="foto" class="form-control" accept=".png,.jpg,.jpeg" />
			        </div>
		      	</div>

		      	<div class="row">
		      		<div class="col-4 mx-auto">
		      			<div id="img_area">
		      			</div>
		      		</div>
		      	</div>
		      </div>
		      <div class="modal-footer d-flex justify-content-between">
		      	<input type="hidden" name="id" id="id" value="0" />
		        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
		        <button type="submit" class="btn btn-primary">Guardar</button>
		      </div>
	      	</form>
	    </div>
	  </div>
	</div>

	<?php require_once 'loading_modal.php'; ?>

</body>
</html>