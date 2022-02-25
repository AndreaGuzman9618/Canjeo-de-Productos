'use strict'

var serverRoute = 'server/'

var data_div = $('#data_div')
var points = $('#points')

var loading_modal_btn = $('#loading_modal_btn')
var canjear_modal_btn = $('#canjear_modal_btn')

var table_div = $('#table_div')

var isShowDetail = false

var protocol = window.location.protocol
var hostname = window.location.hostname
var currentRoute = protocol + '//' + hostname


verificationOrders()

function verificationOrders(){
	points.html('<span style="font-size: 14px !important; display: flex;">Calculando...</span>')
	$.ajax({
		url: `${ serverRoute }server.php?op=verificationOrders`,
		type: 'POST'
	})
	.done(function(data) {
		ponerPuntosDisponibles()
	})
	.fail(function(data) {
		console.log(data.responseText)
	})
	.always(function() {
	})
}

function ponerPuntosDisponibles(){
	getPuntosDisponibles( (data) => {
		let totalPuntosDisponibles = 0
		data.data.forEach( (element) => {
			totalPuntosDisponibles += element.puntos_disponibles
		})
		points.html(totalPuntosDisponibles)
	})
}

function verDetalle(){
	isShowDetail = true
	data_div.html('<h4 style="margin: auto;">Cargando...</h4>')
	getPuntosDisponibles( (data) => {
		let html = `
			<div class="table-div">
				<table border="1" id="points_table">
					<thead>
						<tr>
							<th>Fecha Compra</th>
							<th width="20%">Monto</th>
							<th>Puntos Disponibles</th>
							<th>Fecha Caducidad</th>
						</tr>
					</thead>
					<tbody>
		`

		let totalPuntosDisponibles = 0
		data.data.forEach( (element) => {
			html += `
				<tr>
					<td style="text-align: center; font-size: 14px;">${ element.fecha_compra }</td>
					<td style="text-align: center; font-size: 14px;">${ element.monto }</td>
					<td style="text-align: center; font-size: 14px;">${ element.puntos_disponibles }</td>
					<td style="text-align: center; font-size: 14px;">${ element.fecha_caducidad }</td>
				</tr>
			`
			totalPuntosDisponibles += element.puntos_disponibles
		})

		html += `
					</tbody>
				</table>
			</div>
		`
		data_div.html( html )
		points.html(totalPuntosDisponibles)
	})
}

function getPuntosDisponibles(action){
	$.ajax({
		url: `${ serverRoute }server.php?op=verDetalle`,
		type: 'POST',
		dataType: 'JSON'
	})
	.done(function(data) {
		action( data )
	})
	.fail(function(data) {
		console.log(data.responseText);
	})
	.always(function() {
	});
}

function verPremios(){
	//loading_modal_btn.click()
	getPuntosDisponibles( (data) => {
		let totalPuntosDisponibles = 0
		data.data.forEach( (element) => {
			totalPuntosDisponibles += element.puntos_disponibles
		})

		if (totalPuntosDisponibles > 0 || true) {
			$.ajax({
				url: `${ serverRoute }server.php?op=verPremios`,
				type: 'POST',
				dataType: 'JSON',
				data: {puntos: totalPuntosDisponibles}
			})
			.done(function(data) {
				let html = ''

				if ( data.data.length == 0 ) {
					html = `
						<div class="font-weight-bold text-center">
							Puntos Insuficientes
						</div>
					`
					$('#premios_footer').html(`
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					`)
				} else {
					html = `
						<table class="table table-bordered table-hover table-sm">
							<thead>
								<tr>
									<th></th>
									<th>Rango</th>
									<th>Foto</th>
								</tr>
							</thead>
							<tbody>

					`
					data.data.forEach((element,index) => {
						let disabled = !element.isAvailable ? 'disabled' : ''
						html += `
							<tr>
								<td style="text-align: center; vertical-align: middle;">
									<input type="radio" ${ disabled } name="premio_producto" value="${ element.id }" />
								</td>
								<td style="text-align: center; vertical-align: middle;">
									${ element.rango_inicial } - ${ element.rango_final }
								</td>
								<td class="text-center">
									<figure class="font-weight-bold text-center m-0" style="font-size: 12px;">
										<img class="img-thumbnail" style="max-width: 90px;" src="${ currentRoute }/img/awards/${ element.foto }" />
										<figcaption>${ element.producto }</figcaption>
									</figure>
								</td>
							</tr>
						`
					})
					html += `
							</tbody>
						</table>
					`
					$('#premios_footer').html(`
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary" onclick="canjear(${ data.puntos });">Canjear</button>
					`)
				}

				table_div.html( html )

			})
			.fail(function(data) {
				console.log(data.responseText)
			})
			.always(function() {
				//loading_modal_btn.click()
				canjear_modal_btn.click()
			})
		} else {
			//loading_modal_btn.click()
			window.alert('No tiene puntos disponibles')
		}
	})
}

function canjear(puntos){
	let valueSelected = 0
	$('input[name="premio_producto"]').each((index, element) => {
		if ( $().add(element).is(':checked') ) {
			valueSelected = element.value
		}
	})

	if (valueSelected == 0) {
		window.alert('Seleccione un producto por favor.')
	} else {
		canjear_modal_btn.click()
		//loading_modal_btn.click()
		$.ajax({
			url: `${ serverRoute }server.php?op=canjear`,
				type: 'POST',
				dataType: 'JSON',
				data: {
					premio_id: valueSelected,
					puntos: puntos,
					routeForImage: `${ currentRoute }/img/awards/`
				}
		})
		.done(function(data) {
			console.log(data)
			//loading_modal_btn.click()
			canjear_modal_btn.click()
			window.alert('Premio Canjeado')
			verificationOrders()
			if (isShowDetail) {
				verDetalle()
			}
		})
		.fail(function(data) {
			//loading_modal_btn.click()
			console.log( data.responseText )
		})
		.always(function() {
		})
	}
}