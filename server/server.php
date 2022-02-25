<?php

require_once './general.php';
require_once './functions.php';

date_default_timezone_set('America/Guayaquil');

$usuario_id = 85;

switch ($_GET['op']) {
	case 'verificationOrders':
		$newOrders = getNewOrders($usuario_id);
		foreach ($newOrders as $row) {
			$order_id = $row['ID'];
			$monto = getMonto($order_id);
			$puntos_ganados = floor($monto / 7);
			$puntos_utilizados = 0;
			$fecha_compra = $row['post_date'];

			$fecha_variable = new DateTime($fecha_compra, new DateTimeZone('America/Guayaquil'));

			$fecha_caducidad = $fecha_variable->modify('+6 month')->format('Y-m-d H:i:s');

			$dataToSave = [
				'monto' => $monto,
				'puntos_ganados' => $puntos_ganados,
				'puntos_utilizados' => $puntos_utilizados,
				'fecha_compra' => $fecha_compra,
				'fecha_caducidad' => $fecha_caducidad,
				'orden_id' => $order_id,
				'usuario_id' => $usuario_id,
				'estado' => 1
			];
			create('puntos', $dataToSave);
		}
		break;

	case 'verDetalle':
		$puntosDisponibles = getPuntosDisponibles($usuario_id, date('Y-m-d H:i:s'));
		$data = ['data' => []];
		foreach ($puntosDisponibles as $row) {
			$subdata = [];

			$puntos_ganados = intval( $row['puntos_ganados'] );
			$puntos_utilizados = intval( $row['puntos_utilizados'] );

			$subdata['fecha_compra'] = date('d-m-Y', strtotime($row['fecha_compra']) );
			$subdata['monto'] = $row['monto'];
			$subdata['puntos_disponibles'] = $puntos_ganados - $puntos_utilizados;
			$subdata['fecha_caducidad'] = date('d-m-Y', strtotime($row['fecha_caducidad']) );

			$data['data'][] = $subdata;
		}
		echo json_encode($data);
		break;

	case 'verPremios':
		$puntos = intval( $_POST['puntos'] );
		//$dataOfServer = getPremiosParaCanjear($puntos);
		$dataOfServer = getOrder('rango_premios', ['estado' => 1], ['rango_inicial']);

		$data = ['data' => [], 'puntos' => $puntos];
		foreach ($dataOfServer as $row) {
			$subdata = [];

			$subdata['id'] = $row['id'];
			$subdata['producto'] = $row['producto'];
			$subdata['rango_inicial'] = $row['rango_inicial'];
			$subdata['rango_final'] = $row['rango_final'];
			$subdata['foto'] = $row['foto'];
			$subdata['isAvailable'] = $puntos >= intval( $row['rango_inicial'] );

			$data['data'][] = $subdata;
		}

		echo json_encode($data);
		break;

	case 'canjear':
		$premio_id = $_POST['premio_id'];
		$puntos = $_POST['puntos'];
		$routeForImage = $_POST['routeForImage'];
		$acumular_puntos = 0;

		$dataOfServer = get('rango_premios', ['id' => $premio_id]);

		$premios_data = [
			'id' => $dataOfServer[0]['id'],
			'rango_inicial' => intval( $dataOfServer[0]['rango_inicial'] ),
			'rango_final' => intval( $dataOfServer[0]['rango_final'] ),
			'foto' => $dataOfServer[0]['foto'],
			'producto' => $dataOfServer[0]['producto']
		];
		$rango_inicial = $premios_data['rango_inicial'];
		$foto = $premios_data['foto'];
		$producto = $premios_data['producto'];


		$puntosDisponibles = getPuntosDisponibles($usuario_id, date('Y-m-d H:i:s'));
		foreach ($puntosDisponibles as $row) {
			$puntos_ganados = intval( $row['puntos_ganados'] );
			$puntos_utilizados = intval( $row['puntos_utilizados'] );

			$puntos_a_consumir_de_orden = $puntos_ganados - $puntos_utilizados;

			if ($acumular_puntos + $puntos_a_consumir_de_orden > $rango_inicial ) {
				$puntos_a_consumir_de_orden = $rango_inicial - $acumular_puntos;
			}

			$acumular_puntos += $puntos_a_consumir_de_orden;

			update('puntos', ['puntos_utilizados' => $puntos_a_consumir_de_orden + $puntos_utilizados], [ 'id' => intval( $row['id'] ) ]);

			if ($acumular_puntos == $rango_inicial) { // YA ALCANCÉ LOS PUNTOS MÍNIMOS PARA RETIRAR MI PREMIO
				break;
			}
		}

		create('canjear', [
			'puntos_utilizados' => $acumular_puntos,
			'premio_id' => $premio_id,
			'usuario_id' => $usuario_id,
			'estado' => 1
		]);


		// ENVIAR MAIL
		$imageUrl = $routeForImage.$foto;

		$dataOfServer = get('wp_usermeta', ['user_id' => $usuario_id, 'meta_key' => 'billing_email']);
		$email_usuario = $dataOfServer[0]['meta_value'];

		$dataOfServer = get('wp_usermeta', ['user_id' => $usuario_id, 'meta_key' => 'first_name']);
		$first_name_usuario = $dataOfServer[0]['meta_value'];

		$dataOfServer = get('wp_usermeta', ['user_id' => $usuario_id, 'meta_key' => 'last_name']);
		$last_name_usuario = $dataOfServer[0]['meta_value'];

		$nombre_usuario = trim($first_name_usuario).' '.trim($last_name_usuario);

		/*notify($fromEmail, $fromName, [
			'correo' => $email_usuario,
			'nombre' => $nombre_usuario,
		], $producto, $imageUrl);

		notifyToAdmin($fromEmail, $fromName, [
			'correo' => 'info@my-presto.com',
			'nombre' => 'MY PRESTO',
		], $nombre_usuario, $producto, $imageUrl);*/


		echo json_encode(['response' => null]);
		break;

	default:
		# code...
		break;
}

function notify($fromEmail, $fromName, $data, $producto, $routeOfImage){
	$subject = 'MY PRESTO - CANJEO DE PREMIO';
	$htmlContent = '
		<div style="font-family: Helvetica; text-align: justify; display: inline-block; padding: 10px 20px; background: #EEEEEE; border-radius: 10px; border: 2px solid #0E7934; font-size: 16px;">
			<p>
				Le notificamos que se generó el canje por el producto "'. mb_strtoupper( $producto ) .'".
			</p>
			<p>
				<div style="margin-top: 40px; margin-bottom: 40px;">
					<img style="max-width: 90px;" src="'.$routeOfImage.'" />
				</div>
			</p>
			<p>
				Saludos,
			</p>
			<p>
				MY PRESTO
			</p>
		</div>
	';
	sendEmail($fromEmail, $fromName, $data['correo'], $data['nombre'], $subject, $htmlContent);
}

function notifyToAdmin($fromEmail, $fromName, $data, $usuario, $producto, $routeOfImage){
	$subject = 'MY PRESTO - CANJEO DE PREMIO - '. mb_strtoupper($usuario);
	$htmlContent = '
		<div style="font-family: Helvetica; text-align: justify; display: inline-block; padding: 10px 20px; background: #EEEEEE; border-radius: 10px; border: 2px solid #0E7934; font-size: 16px;">
			<p>
				El usuario "'. mb_strtoupper( $usuario ) .'" acabó de canjear el producto "'. mb_strtoupper( $producto ) .'"
			</p>
			<p>
				<div style="margin-top: 40px; margin-bottom: 40px;">
					<img style="max-width: 90px;" src="'.$routeOfImage.'" />
				</div>
			</p>
		</div>
	';
	sendEmail($fromEmail, $fromName, $data['correo'], $data['nombre'], $subject, $htmlContent);
}

?>