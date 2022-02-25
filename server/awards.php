<?php

require_once './functions.php';

date_default_timezone_set('America/Guayaquil');

switch ($_GET['op']) {
	case 'saveAward':
		$id = intval( $_POST['id'] );

		$rango_inicial = $_POST['rango_inicial'];
		$rango_final = $_POST['rango_final'];
		$producto = $_POST['producto'];

		$foto = $_FILES['foto']; // JSON
		$imgNameFinal = '';

		if ( !empty($foto['name']) ) {
			$nombre_archivo = $foto['name'];
			$tmp_name = $foto['tmp_name'];
			header('Content-Type: '.$foto['type']);

			$mimeType = explode('/', $foto['type']);

			// Obtener nuevas dimensiones
			list($ancho, $alto) = getimagesize($tmp_name);
			$nuevo_ancho = 200;
			$nuevo_alto = ($alto * $nuevo_ancho) / $ancho;

			// Redimensionar y guardar imagen nueva
			$nuevaImagen = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);

			if ($mimeType[1] == 'png') {
				$imagen = imagecreatefrompng($tmp_name);
			} else {
				$imagen = imagecreatefromjpeg($tmp_name);
			}

			imagecopyresampled($nuevaImagen, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

			$imgNameFinal = uniqid().'_'.$foto['name'];
			if ($mimeType[1] == 'png') {
				imagepng($nuevaImagen, '../img/awards/'.$imgNameFinal);
			} else {
				imagejpeg($nuevaImagen, '../img/awards/'.$imgNameFinal);
			}
		}



		$dataForSave = [
			'rango_inicial' => $rango_inicial,
			'rango_final' => $rango_final,
			'producto' => $producto,
			'estado' => 1
		];

		if ( !empty($imgNameFinal) ) {
			$dataForSave['foto'] = $imgNameFinal;
		}

		if ($id == 0) { // create
			create('rango_premios', $dataForSave);
		} else {
			update('rango_premios', $dataForSave, ['id' => $id]);
		}


		echo json_encode([
			'temp' => $foto
		]);
		break;

	case 'getData':

		$dataOfServer = getOrder('rango_premios', ['estado' => 1], ['rango_inicial']);

		$data = ['data' => []];
		foreach ($dataOfServer as $row) {
			$subdata = [];
			$subdata['producto'] = $row['producto'];
			$subdata['rango'] = $row['rango_inicial'].' - '.$row['rango_final'];
			$subdata['foto'] = $row['foto'];
			$subdata['editar'] = '
				<button type="button" class="btn btn-warning" onclick="editar('.$row['id'].')">
					Editar
				</button>
			';
			$subdata['eliminar'] = '
				<button type="button" class="btn btn-danger" onclick="eliminar('.$row['id'].')">
					Eliminar
				</button>
			';

			$data['data'][] = $subdata;
		}

		echo json_encode($data);
		break;

	case 'getById':
		$id = $_POST['id'];

		$dataOfServer = get('rango_premios', ['id' => $id, 'estado' => 1]);

		$data = [
			'id' => '0',
			'producto' => '',
			'rango_inicial' => '',
			'rango_final' => '',
			'foto' => ''
		];

		if ( count($dataOfServer) > 0 ) {
			$data['id'] = $dataOfServer[0]['id'];
			$data['producto'] = $dataOfServer[0]['producto'];
			$data['rango_inicial'] = $dataOfServer[0]['rango_inicial'];
			$data['rango_final'] = $dataOfServer[0]['rango_final'];
			$data['foto'] = $dataOfServer[0]['foto'];
		}

		echo json_encode($data);
		break;

	case 'eliminar':
		$id = $_POST['id'];
		update('rango_premios', ['estado' => 0], ['id' => $id]);
		break;

	default:
		# code...
		break;
}

?>