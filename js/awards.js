'use strict'

var serverRoute = 'server/'

var awards_modal_btn = $('#awards_modal_btn')

var protocol = window.location.protocol
var hostname = window.location.hostname
var currentRoute = protocol + '//' + hostname

var awards_form = $('#awards_form')
var awards_table = $('#awards_table')
var loading_modal_btn = $('#loading_modal_btn')
var foto = $('#foto')


awards_form.submit(function(event){
	event.preventDefault()
	saveAward()
})

foto.change( ponerImagen )
getData()





function getData(){
	$.ajax({
		url: `${ serverRoute }awards.php?op=getData`,
		type: 'POST',
		dataType: 'JSON',
		beforeSend: () => {
			awards_table.find('tbody').html( `
				<tr>
					<td colspan="5" class="text-center font-weight-bold">
						Cargando...
					</td>
				</tr>
			`)
		}
	})
	.done(function(data) {
		let html = ''

		data.data.forEach((element, index) => {
			html += `
				<tr>
					<td style="vertical-align: middle;">${ element.producto }</td>
					<td style="vertical-align: middle; text-align: center;">${ element.rango }</td>
					<td class="text-center">
						<img class="img-thumbnail" style="max-width: 120px;" src="${ currentRoute }/img/awards/${ element.foto }" />
					</td>
					<td style="vertical-align: middle; text-align: center;">${ element.editar }</td>
					<td style="vertical-align: middle; text-align: center;">${ element.eliminar }</td>
				</tr>
			`
		})

		awards_table.find('tbody').html( html )

	})
	.fail(function(data) {
		console.log(data.responseText)
	})
	.always(function() {
	})
}

function crearPremio(){
	clearFields()
	awards_modal_btn.click()
}

function validaNumero(event){
	let character = String.fromCharCode( event.keyCode )
	let pattern = /\d/
	return pattern.test( character )
}

function ponerImagen(event){
	let urlImage = URL.createObjectURL(event.target.files[0]);
	$('#img_area img')[0].src = urlImage
}

function saveAward(){
	let formData = new FormData($('#awards_form')[0])
	$.ajax({
		url: `${ serverRoute }awards.php?op=saveAward`,
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
		beforeSend: () => {
			awards_form.find('button[type="submit"]').prop('disabled', true)
		}
	})
	.done(function(data) {
		getData()
		awards_modal_btn.click()
	})
	.fail(function(data) {
		console.log( data.responseText )
	})
	.always(function() {
		awards_form.find('button[type="submit"]').prop('disabled', false)
	})
}

function clearFields(){
	$('#producto, #rango_inicial, #rango_final, #foto').val('')
	$('#id').val('0')
	$('#img_area').html(`
		<img src="img/img_not_found.png" class="img-fluid" style="border-radius: 10px;" />
	`)
}

function editar(id){
	$.ajax({
		url: `${ serverRoute }awards.php?op=getById`,
		type: 'POST',
		dataType: 'JSON',
		data: {id: id}
		//beforeSend: () => loading_modal_btn.click()
	})
	.done(function(data) {
		$('#producto').val(data.producto)
		$('#rango_inicial').val(data.rango_inicial)
		$('#rango_final').val(data.rango_final)
		$('#id').val(data.id)

		if ( Number(data.id) == 0 ) {
			$('#img_area').html(`
				<img src="img/img_not_found.png" class="img-fluid" style="border-radius: 10px;" />
			`)
		} else {
			$('#img_area').html(`
				<img src="img/awards/${ data.foto }" class="img-fluid" style="border-radius: 10px;" />
			`)
		}

	})
	.fail(function(data) {
		console.log(data.responseText)
	})
	.always(function() {
		//loading_modal_btn.click()
		setTimeout(() => {
			awards_modal_btn.click()
		}, 1000)
	})
}

function eliminar(id){
	let isConfirm = window.confirm('¿Desea eliminar el registro?')
	if ( isConfirm ) {
		$.ajax({
			url: `${ serverRoute }awards.php?op=eliminar`,
			type: 'POST',
			data: {id: id}
			//beforeSend: () => loading_modal_btn.click()
		})
		.done(function(data) {
			getData()
		})
		.fail(function(data) {
			console.log(data.responseText)
		})
		.always(function() {
			//loading_modal_btn.click()
		})
	}
}