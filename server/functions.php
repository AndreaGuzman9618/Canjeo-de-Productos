<?php

function getConn()
{
	$conn = new PDO("mysql:host=ls-038607958141d7d885028252ccd3fa0efbd43aa4.cudiy4yaxyef.us-east-1.rds.amazonaws.com;port=3306;dbname=bitnami_mypresto", 'dbmasteruser', 't2_C0]Mig,tbi5pQbSdoRVX5SLW6a~Ys');
	$conn->query("SET NAMES 'utf8'");
	return $conn;
}

function create($table, $data){
	$conn = getConn();
	$columns = [];
	$temporal = [];
	foreach ($data as $column => $value) {
		$columns[] = $column;
		$temporal[] = ':'.$column;
	}

	$sql="INSERT INTO $table (". implode(',', $columns) .") VALUES (". implode(',', $temporal) .")";
	$stmt = $conn->prepare($sql);
	foreach ($data as $column => $value) {
		$stmt->bindValue(':'.$column, $value);
	}
	$isExecute = $stmt->execute();
	$conn = null;
	return $isExecute;
}

function update($table, $data, $filters){
	$conn = getConn();
	$temporal = [];
	foreach ($data as $column => $value) {
		$temporal[] = $column.' = :'.$column;
	}

	$sql ="UPDATE $table SET ". implode(',', $temporal);

	$filtersTemp = [];
	foreach ($filters as $column => $value) {
		$filtersTemp[] = $column.' = :f_'.$column;
	}
	if ( count($filters) > 0 ) {
		$sql .= " WHERE ". implode(' AND ', $filtersTemp);
	}
	$stmt = $conn->prepare($sql);


	foreach ($data as $column => $value) {
		$stmt->bindValue(':'.$column, $value);
	}
	foreach ($filters as $column => $value) {
		$stmt->bindValue(':f_'.$column, $value);
	}
	$stmt->execute();
	$conn = null;
}

function get($table, $filters){
	$conn = getConn();

	$sql = "SELECT * FROM $table";

	$filtersTemp = [];
	foreach ($filters as $column => $value) {
		$filtersTemp[] = $column.' = :f_'.$column;
	}
	if ( count($filters) > 0 ) {
		$sql .= " WHERE ". implode(' AND ', $filtersTemp);
	}
	$stmt = $conn->prepare($sql);

	foreach ($filters as $column => $value) {
		$stmt->bindValue(':f_'.$column, $value);
	}

	$stmt->execute();
	$response = $stmt->fetchAll();
	$conn = null;
	return $response;
}

function getOrder($table, $filters, $filtersOrder){
	$conn = getConn();

	$sql = "SELECT * FROM $table";

	$filtersTemp = [];
	foreach ($filters as $column => $value) {
		$filtersTemp[] = $column.' = :f_'.$column;
	}
	if ( count($filters) > 0 ) {
		$sql .= " WHERE ". implode(' AND ', $filtersTemp);
	}


	$filtersTemp2 = [];
	foreach ($filtersOrder as $p) {
		$filtersTemp2[] = $p;
	}
	if ( count($filtersOrder) >= 0 ) {
		$sql .= " ORDER BY ". implode(' , ', $filtersTemp2);
	}

	$stmt = $conn->prepare($sql);

	foreach ($filters as $column => $value) {
		$stmt->bindValue(':f_'.$column, $value);
	}

	$stmt->execute();
	$response = $stmt->fetchAll();
	$conn = null;
	return $response;
}





function getNewOrders($usuario_id){
	$conn = getConn();

	$sql = "SELECT * FROM wp_posts WHERE post_type = 'shop_order' AND post_status = 'wc-completed' AND ID IN (
				SELECT DISTINCT(post_id) FROM wp_postmeta WHERE meta_key = '_customer_user' AND meta_value = :usuario_id
			) AND NOT(ID IN (SELECT orden_id FROM puntos WHERE usuario_id = :usuario_id AND estado = 1))";

	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':usuario_id', $usuario_id);

	$stmt->execute();
	$data = $stmt->fetchAll();

	$conn = null;
	return $data;
}

function getMonto($order_id){
	$conn = getConn();

	$sql = "SELECT SUM( meta_value ) as 'value' FROM wp_woocommerce_order_itemmeta WHERE order_item_id IN (
    			SELECT order_item_id FROM wp_woocommerce_order_items WHERE order_id = :order_id
			) AND meta_key IN ('_line_subtotal');";

	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':order_id', $order_id);

	$stmt->execute();
	$data = $stmt->fetchAll();

	$conn = null;
	if ( count($data) > 0 ) {
		return floatval( str_replace(',', '.', $data[0]['value']) );
	}
	return 0;
}

function getPuntosDisponibles($usuario_id, $fecha_actual){
	$conn = getConn();

	$sql = "SELECT * FROM puntos
			WHERE usuario_id = :usuario_id AND estado = 1 AND fecha_caducidad >= :fecha_actual AND (puntos_ganados - puntos_utilizados <> 0)
			ORDER BY fecha_compra";

	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':usuario_id', $usuario_id);
	$stmt->bindValue(':fecha_actual', $fecha_actual);

	$stmt->execute();
	$data = $stmt->fetchAll();

	$conn = null;
	return $data;
}


function getPremiosParaCanjear($puntos){
	$conn = getConn();

	//$sql = "SELECT * FROM rango_premios WHERE estado = 1 AND :puntos >= rango_inicial";
	$sql = "SELECT * FROM rango_premios WHERE estado = 1";

	$stmt = $conn->prepare($sql);
	//$stmt->bindValue(':puntos', $puntos);

	$stmt->execute();
	$data = $stmt->fetchAll();

	$conn = null;
	return $data;
}


?>