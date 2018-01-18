$(document).ready(function () {
  $('#loading').hide();
  $('.loadpage').on('click', function () {
    $('#loading').show();
  });
  $('.barcoespacio').on('click', function () {
    $('#numSlip').html($(this).attr('id'));
    $('#barcoNombre').html($(this).data('embarcacion'));
    $('#clienteNombre').html($(this).data('cliente'));
    $('#esloraInfo').html($(this).data('eslora'));
    $('#llegadaInfo').html($(this).data('llegada'));
    $('#salidaInfo').html($(this).data('salida'));
    $('#modalinfobarco').modal('toggle');
  });
  $('.select-buscador').select2();
  $.fn.datepicker.dates['es'] = {
    days: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
    daysShort: ["Dom", "Lun", "Mar", "Mi", "Ju", "Vi", "Sab"],
    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    today: "Hoy",
    clear: "Quitar",
    // format: "dd-mm-yyyy",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
    weekStart: 0
  };
  $('.input-daterange').datepicker({
    format: 'yyyy-mm-dd',
    language: "es",
    orientation: "bottom auto",
    autoclose: true,
    startDate: "0d"
  });
  $('.datepicker-solo').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    orientation: "bottom auto",
  });

  $('.editorwy').wysihtml5({
    toolbar: {
      "image": false,
      "color": false,
      "link": false,
      "html": true,
    }
  });

    $('.lista-pagos').on('click', '.input-calendario', function (e) {
        console.log('click calendario');
        $(this).datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            orientation: "auto",
        })
    });
    $('.cuadro-zona').on('click', function () {
        var direc = $(this).data('direccion');
        window.location.href = direc;
    });

//---- seleccionar choice al recotizar------
  var diasestadiaprecio = $('#de_precio').data('valor');
  var electricidadprecio = $('#e_precio').data('valor');
  $("#appbundle_marinahumedacotizacion_mhcservicios_0_precio>option").each(function () {
    if ($(this).val() == diasestadiaprecio) {
      $(this).attr("selected", "selected");
    }
  });
  $("#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux>option").each(function () {
    if ($(this).val() == electricidadprecio) {
      $(this).attr("selected", "selected");
    }
  });
//-- fin seleccionar choice al recotizar----
  $(".esnumero").keypress(function () {
    return isNumberKey(event);
  });
  $(".esdecimal").keypress(function () {
    return esNumeroDecimal(event, this);
  });
  $("#appbundle_marinahumedacotizacion_validanovo_0").click(function () {
    $('#notarechazado').hide();
  });
  $("#appbundle_marinahumedacotizacion_validanovo_1").click(function () {
    $('#notarechazado').show();
  });
  $("#appbundle_astillerocotizacion_validanovo_0").click(function () {
    $('#notarechazado').hide();
  });
  $("#appbundle_astillerocotizacion_validanovo_1").click(function () {
    $('#notarechazado').show();
  });
});

function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
  return true;
}

function esNumeroDecimal(e, field) {
  key = e.keyCode ? e.keyCode : e.which
  // backspace
  if (key == 8)
    return true
  // 0-9
  if (key > 47 && key < 58) {
    if (field.value == "")
      return true
    regexp = /.[0-9]{23}$/
    return !(regexp.test(field.value))
  }
  // .
  if (key == 46) {
    if (field.value == "")
      return false
    regexp = /^[0-9]+$/
    return regexp.test(field.value)
  }
  // other key
  return false

}

//////////////////////////////////////////////////////////////////
//Collection al agregar productos a una solicitud
jQuery('.add-another-producto').click(function (e) {
    e.preventDefault();
    var totMotores = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var motorListPrimero = jQuery('#motor-fields-list' + lista);
    var newWidget = $(motorListPrimero).data('prototype');
    newWidget = newWidget.replace(/__name__/g, totMotores);
    totMotores++;
    $(this).data('cantidad', totMotores);
    var newLi = jQuery('<div class="row"></div>').html(newWidget);
    newLi.appendTo(motorListPrimero);
    newLi.before(newLi);
});

$('.lista-productos').on('click', '.remove-producto', function (e) {
    e.preventDefault();
    $(this).parent().parent().parent().remove();
    return false;
});
////////////////////////////////////////////////////////////////////

//collectio al agregar motores a un barco
jQuery('.add-another-motor').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totMotores = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var motorListPrimero = jQuery('#motor-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(motorListPrimero).data('prototype');
  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totMotores);
  totMotores++;
  $(this).data('cantidad', totMotores);
  // create a new list element and add it to the list
  var newLi = jQuery('<div class="row"></div>').html(newWidget);
  newLi.appendTo(motorListPrimero);

  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
});
// handle the removal, just for this example
$('.lista-motores').on('click', '.remove-motor', function (e) {
  e.preventDefault();
  //console.log('quitar motor');
  $(this).parent().parent().parent().remove();

  return false;
});

//collectio al agregar servicios en cotización astillero
jQuery('.add-another-servicio').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totServicios = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var servicioListPrimero = jQuery('#servicio-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(servicioListPrimero).data('prototype');

  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-producto', 'hide');
  newWidget = newWidget.replace('td-servicio', 'hide');
  //newWidget = newWidget.replace('td-precio', 'hide');
  totServicios++;
  $(this).data('cantidad', totServicios);
  // create a new list element and add it to the list
  var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);
  newLi.appendTo(servicioListPrimero);

  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
  $('#a_nuevacotizacion_mxn > tbody').append('<tr class="servicio-agregado_mxn" id="' + (totServicios - 1) + '">' +
      '<td class="valorcantidad" data-valor="0">0</td>' +
      '<td class="td-otroservicio"></td>' +
      '<td class="valorprecio" data-valor="0">$ 0.00</td>' +
      '<td  class="valorsubtotal" data-valor="0">$ 0.00</td>' +
      '<td class="valoriva" data-valor="0">$ 0.00</td>' +
      '<td class="valortotal" data-valor="0">$ 0.00</td>' +
      '</tr>');
});

$('.lista-servicios').on('click', '.remove-servicio', function (e) {
  e.preventDefault();
  //console.log('quitar motor');
  $(this).parent().parent().remove();
  calculaTotalesAstillero();

  var idfila = $(this).parent().parent().data('id');
  console.log(idfila);
  $('#a_nuevacotizacion_mxn>tbody>#' + idfila).remove();
  calculaTotalesAstilleroMXN();
  return false;
});

//---- aparecer form collection con select de productos ----
$('.add-producto').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totServicios = $('.add-another-servicio').data('cantidad');
  var lista = $('.add-another-servicio').data('idlista');
  var servicioListPrimero = jQuery('#servicio-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(servicioListPrimero).data('prototype');


  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-otroservicio', 'hide');
  newWidget = newWidget.replace('td-servicio', 'hide');
  newWidget = newWidget.replace('input-group', 'hide');
  totServicios++;
  $('.add-another-servicio').data('cantidad', totServicios);
  // create a new list element and add it to the list
  var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);

  newLi.appendTo(servicioListPrimero);
  $('.select-buscador').select2();
  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
  //$('.select-busca-producto').select2();
  $('#a_nuevacotizacion_mxn > tbody').append('<tr class="servicio-agregado_mxn" id="' + (totServicios - 1) + '">' +
      '<td class="valorcantidad" data-valor="0">0</td>' +
      '<td class="td-producto"></td>' +
      '<td class="valorprecio" data-valor="0">$ 0.00</td>' +
      '<td  class="valorsubtotal" data-valor="0">$ 0.00</td>' +
      '<td class="valoriva" data-valor="0">$ 0.00</td>' +
      '<td class="valortotal" data-valor="0">$ 0.00</td>' +
      '</tr>');
});
//---- aparecer form collection con select de productos ----
$('.add-servicio').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totServicios = $('.add-another-servicio').data('cantidad');
  var lista = $('.add-another-servicio').data('idlista');
  var servicioListPrimero = jQuery('#servicio-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(servicioListPrimero).data('prototype');


  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-otroservicio', 'hide');
  newWidget = newWidget.replace('td-producto', 'hide');
  newWidget = newWidget.replace('input-group', 'hide');
  totServicios++;
  $('.add-another-servicio').data('cantidad', totServicios);
  // create a new list element and add it to the list
  var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);

  newLi.appendTo(servicioListPrimero);
  $('.select-buscador').select2();
  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
  //$('.select-busca-producto').select2();
  $('#a_nuevacotizacion_mxn > tbody').append('<tr class="servicio-agregado_mxn" id="' + (totServicios - 1) + '">' +
      '<td class="valorcantidad" data-valor="0">0</td>' +
      '<td class="td-servicio"></td>' +
      '<td class="valorprecio" data-valor="0">$ 0.00</td>' +
      '<td  class="valorsubtotal" data-valor="0">$ 0.00</td>' +
      '<td class="valoriva" data-valor="0">$ 0.00</td>' +
      '<td class="valortotal" data-valor="0">$ 0.00</td>' +
      '</tr>');
});

//collectio al agregar servicios adicionales marina humeda
jQuery('.add-another-servicio-adicional').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totServicios = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var servicioListPrimero = jQuery('#servicio-adicional-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(servicioListPrimero).data('prototype');

  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-producto', 'hide');
  totServicios++;
  $(this).data('cantidad', totServicios);
  // create a new list element and add it to the list
  var newLi = jQuery('<tr class="servicio-agregado"></tr>').html(newWidget);
  newLi.appendTo(servicioListPrimero);

  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
  //$('.select-busca-producto').select2();
});
$('.lista-servicios-adicionales').on('click', '.remove-servicio-adicional', function (e) {
  e.preventDefault();
  //console.log('quitar motor');
  $(this).parent().parent().remove();
  calculaTotalesAdicionales();
  return false;
});

//collectio al agregar pagos a una cotización marina húmeda
jQuery('.add-another-pago').click(function (e) {
  e.preventDefault();
  // var elementoMotor = document.getElementsByClassName(this);
  var totPagos = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var pagoListPrimero = jQuery('#pago-fields-list' + lista);
  //var motorListOtros = jQuery('.lista-motores'+lista);
  // grab the prototype template
  var newWidget = $(pagoListPrimero).data('prototype');
  // replace the "__name__" used in the id and name of the prototype
  // with a number that's unique to your emails
  // end name attribute looks like name="contact[emails][2]"
  newWidget = newWidget.replace(/__name__/g, totPagos);
  totPagos++;
  $(this).data('cantidad', totPagos);
  // create a new list element and add it to the list
  var newLi = jQuery('<tr class="pago-agregado"></tr>').html(newWidget);
  newLi.appendTo(pagoListPrimero);

  // also add a remove button, just for this example
  //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

  newLi.before(newLi);
});
$('.lista-pagos').on('click', '.remove-pago', function (e) {
  e.preventDefault();
  //console.log('quitar motor');
  $(this).parent().parent().remove();

  return false;
});

//--- select dependiente para marina humeda cotización ---
var elclientemh = $('#appbundle_marinahumedacotizacion_cliente');
elclientemh.change(function () {
  // ... retrieve the corresponding form.
  var form = $(this).closest('form');
  // Simulate form data, but only include the selected elcliente value.
  var data = {};
  data[elclientemh.attr('name')] = elclientemh.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url: form.attr('action'),
    type: form.attr('method'),
    data: data,
    success: function (html) {
      // Replace current position field ...
      $('#appbundle_marinahumedacotizacion_barco').replaceWith(
          // ... with the returned one from the AJAX response.
          $(html).find('#appbundle_marinahumedacotizacion_barco')
      );
      // barco field now displays the appropriate barcos.
    }
  });
});
//--- fin select dependiente para marina humeda cotización ---

//--- select dependiente para marina humeda cotización adicional ---
var elcliente = $('#appbundle_marinahumedacotizacionadicional_cliente');
elcliente.change(function () {
  // ... retrieve the corresponding form.
  var form = $(this).closest('form');
  // Simulate form data, but only include the selected elcliente value.
  var data = {};
  data[elcliente.attr('name')] = elcliente.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url: form.attr('action'),
    type: form.attr('method'),
    data: data,
    success: function (html) {
      // Replace current position field ...
      $('#appbundle_marinahumedacotizacionadicional_barco').replaceWith(
          // ... with the returned one from the AJAX response.
          $(html).find('#appbundle_marinahumedacotizacionadicional_barco')
      );
      // barco field now displays the appropriate barcos.
    }
  });
});
//--- fin select dependiente para marina humeda cotización adicional ---

//--- select dependiente para astillero cotización ---
var elclienteastillero = $('#appbundle_astillerocotizacion_cliente');
elclienteastillero.change(function () {

  // ... retrieve the corresponding form.
  var form = $(this).closest('form');
  // Simulate form data, but only include the selected elcliente value.
  var data = {};
  data[elclienteastillero.attr('name')] = elclienteastillero.val();
  // Submit data via AJAX to the form's action path.

  $.ajax({
    url: form.attr('action'),
    type: form.attr('method'),
    data: data,
    success: function (html) {
      // Replace current position field ...
      $('#appbundle_astillerocotizacion_barco').replaceWith(
          // ... with the returned one from the AJAX response.
          $(html).find('#appbundle_astillerocotizacion_barco')
      );
      // barco field now displays the appropriate barcos.
    }
  });
});

//--- fin select dependiente para astillero cotización ---

//-- fin aparecer form collection con select de productos ---

function diasEntreFechas(inicio, fin) {
  var start = new Date(inicio.toString());
  var end = new Date(fin.toString());
  var diff = new Date(end - start);
  var days = (diff / 1000 / 60 / 60 / 24);
  return days;
}

//--- para marina humeda nueva cotización estadia ---

var de_cantidad = 0;
var de_precio = 0;
var de_precio_mxn = 0;
var e_cantidad = 0;
var e_precio = 0;
var e_precio_mxn = 0;
var descuento = 0;
var dolar = 0;
//var dolar = $('#valdolar').data('valor');

$('#appbundle_marinahumedacotizacion_fechaLlegada').on("change", function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var llegada = $(this).val();
  var salida = $('#appbundle_marinahumedacotizacion_fechaSalida').val();
  dias_estadia = diasEntreFechas(llegada, salida);
  $('#dias_estadia_cantidad').html(dias_estadia);
  $('#dias_estadia_cantidad').data('valor', dias_estadia);


  de_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100);
  de_precio_mxn = de_precio * dolar;

  $('#de_cantidad').html(dias_estadia);
  $('#de_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, de_precio, $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(dias_estadia, de_precio_mxn, $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  e_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;

  $('#e_cantidad').html(dias_estadia);
  $('#e_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, e_precio, $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(dias_estadia, e_precio_mxn, $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));

  calculaTotales();
});

$('#appbundle_marinahumedacotizacion_fechaSalida').on("change", function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var llegada = $('#appbundle_marinahumedacotizacion_fechaLlegada').val();
  var salida = $(this).val();
  dias_estadia = diasEntreFechas(llegada, salida);
  $('#dias_estadia_cantidad').html(dias_estadia);
  $('#dias_estadia_cantidad').data('valor', dias_estadia);

  de_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100);
  de_precio_mxn = de_precio * dolar;

  $('#de_cantidad').html(dias_estadia);
  $('#de_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, de_precio, $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(dias_estadia, de_precio_mxn, $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  e_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;

  $('#e_cantidad').html(dias_estadia);
  $('#e_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, e_precio, $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(dias_estadia, e_precio_mxn, $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));

  calculaTotales();
});

//-- Días estadía --
$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').on('change', function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  de_cantidad = $('#dias_estadia_cantidad').data('valor');
  de_precio = ($(this).val() / 100);
  de_precio_mxn = (de_precio * dolar).toFixed(2);

  $('#de_precio').html('$ ' + de_precio);
  $('#de_precio_mxn').html('$ ' + de_precio_mxn);
  calculaSubtotales(de_cantidad, de_precio, $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(de_cantidad, de_precio_mxn, $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));
  calculaTotales();
});

//-- Electricidad --
// $('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').keyup(function () {
//     e_cantidad = $(this).val();
//     e_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val();
//     e_precio_mxn = (e_precio * dolar).toFixed(2);
//
//     $('#e_cantidad').html(e_cantidad);
//     $('#e_cantidad_mxn').html(e_cantidad);
//
//     calculaSubtotales(e_cantidad,e_precio,$('#e_subtotal'),$('#e_iva'),$('#e_descuento'),$('#e_total'));
//     calculaSubtotales(e_cantidad,e_precio_mxn,$('#e_subtotal_mxn'),$('#e_iva_mxn'),$('#e_descuento_mxn'),$('#e_total_mxn'));
//     calculaTotales();
// });

$('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').on('change', function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  e_cantidad = $('#dias_estadia_cantidad').data('valor');
  e_precio = ($(this).val() / 100);
  e_precio_mxn = (e_precio * dolar).toFixed(2);

  $('#e_precio').html('$ ' + e_precio);
  $('#e_precio_mxn').html('$ ' + e_precio_mxn);

  calculaSubtotales(e_cantidad, e_precio, $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(e_cantidad, e_precio_mxn, $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
});

//-- Descuento --
$('#appbundle_marinahumedacotizacion_descuento').keyup(function () {
  recalculaSubtotalesYtotal();
});

//-- Dolar --
$('#appbundle_marinahumedacotizacion_dolar').keyup(function () {
  $('.valdolar').html(parseFloat($(this).val()).toFixed(2) + ' MXN');
  recalculaSubtotalesYtotal();
});

function recalculaSubtotalesYtotal() {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();

  dias_estadia = $('#dias_estadia_cantidad').data('valor');

  de_cantidad = dias_estadia;
  de_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100;
  de_precio_mxn = de_precio * dolar;
  //$('#de_cantidad').html(de_cantidad);
  calculaSubtotales(de_cantidad, de_precio, $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(de_cantidad, de_precio_mxn, $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  e_cantidad = dias_estadia;
  e_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100;
  e_precio_mxn = e_precio * dolar;
  calculaSubtotales(e_cantidad, e_precio, $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(e_cantidad, e_precio_mxn, $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));

  calculaTotales();
}

function calculaSubtotales(cantidad, precio, tdsubtot, tdiva, tddesc, tdtot) {


  var eslora = 0;
  if ($('#de_eslora').data('valor')) {
    eslora = $('#de_eslora').data('valor');
  }
  var iva = ($('#valiva').data('valor')) / 100;
  var descuento = $('#appbundle_marinahumedacotizacion_descuento').val();
  var subtotal = cantidad * precio * eslora;
  var ivatot = subtotal * iva;
  var desctot = (subtotal * descuento) / 100;
  var total = (subtotal + ivatot - desctot).toFixed(2);

  tdsubtot.html('$ ' + (subtotal).toFixed(2));
  tdiva.html('$ ' + (ivatot).toFixed(2));
  tddesc.html('$ ' + (desctot).toFixed(2));
  tdtot.html('$ ' + total);

  tdsubtot.data('valor', subtotal);
  tdiva.data('valor', ivatot);
  tddesc.data('valor', desctot)
  tdtot.data('valor', total);
}

function calculaTotales() {

  var gransubtotal = (
      parseFloat($('#de_subtotal').data('valor')) +
      parseFloat($('#e_subtotal').data('valor'))
  ).toFixed(2);

  var graniva = (
      parseFloat($('#de_iva').data('valor')) +
      parseFloat($('#e_iva').data('valor'))
  ).toFixed(2);

  var grandescuento = (
      parseFloat($('#de_descuento').data('valor')) +
      parseFloat($('#e_descuento').data('valor'))
  ).toFixed(2);

  var grantotal = (
      parseFloat($('#de_total').data('valor')) +
      parseFloat($('#e_total').data('valor'))
  ).toFixed(2);

  $('#gransubtot').html(gransubtotal);
  $('#graniva').html(graniva);
  $('#grandecuento').html(grandescuento);
  $('#grantot').html(grantotal);

  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var gransubtotalmxn = (gransubtotal * dolar).toFixed(2);
  var granivamxn = (graniva * dolar).toFixed(2);
  var grandescuentomxn = (grandescuento * dolar).toFixed(2);
  var grantotalmxn = (grantotal * dolar).toFixed(2);
  $('#gransubtot_mxn').html(gransubtotalmxn);
  $('#graniva_mxn').html(granivamxn);
  $('#grandecuento_mxn').html(grandescuentomxn);
  $('#grantot_mxn').html(grantotalmxn);
}

// $('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').on('click',function () {
//     if($('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').is(':checked')) {
//         $('#cotizagasolina').removeClass('hidden');
//     } else {
//         $('#cotizagasolina').addClass('hidden');
//     }
//     calculaTotales();
// });
//
// $('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').on('click',function () {
//    if($('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').is(':checked')){
//        $('#cotizadezasolve').removeClass('hidden');
//    }else{
//        $('#cotizadezasolve').addClass('hidden');
//    }
//     calculaTotales();
// });
//
// $('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').on('click',function () {
//     if($('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').is(':checked')){
//         $('#cotizalimpieza').removeClass('hidden');
//     }else{
//         $('#cotizalimpieza').addClass('hidden');
//     }
//     calculaTotales();
// });

//-------- fin metodos marina humeda cotizacion --------

//---- para marina humeda servicio adicional -----------
$('#servicioAdicional').on('keyup', 'input', function () {
  //var cantidadAd = $(this).val();
  $(this).parent().data('valor', $(this).val());

  var fila = $(this).parent().parent();

  calculaSubtotalesAdicionales(fila);
  //console.log('escribe cantidad ' +fila.children('.valorcantidad').data('valor'));

});

function calculaSubtotalesAdicionales(fila) {
  var iva = $('#valorsistemaiva').data('valor');
  var cantidadAd = fila.children('.valorcantidad').data('valor');
  var precioAd = fila.children('.valorprecio').data('valor');
  var subtotalAd = cantidadAd * precioAd;
  var ivaAd = (subtotalAd * iva) / 100
  var totalAd = subtotalAd + ivaAd;

  fila.children('.valorsubtotal').html('$ ' + parseFloat(subtotalAd).toFixed(2));
  fila.children('.valorsubtotal').data('valor', subtotalAd);

  fila.children('.valoriva').html('$ ' + parseFloat(ivaAd).toFixed(2));
  fila.children('.valoriva').data('valor', ivaAd);

  fila.children('.valortotal').html('$ ' + parseFloat(totalAd).toFixed(2));
  fila.children('.valortotal').data('valor', totalAd);
  calculaTotalesAdicionales();
}

function calculaTotalesAdicionales() {
  var granSubtotalAd = 0;
  var granIvaAd = 0;
  var granTotalAd = 0;

  $("#servicioAdicional tbody tr").each(function () {
    granSubtotalAd += $(this).children('.valorsubtotal').data('valor');
    granIvaAd += $(this).children('.valoriva').data('valor');
    granTotalAd += $(this).children('.valortotal').data('valor');
  });

  $('#gransubtot').html(parseFloat(granSubtotalAd).toFixed(2));
  $('#graniva').html(parseFloat(granIvaAd).toFixed(2));
  $('#grantot').html(parseFloat(granTotalAd).toFixed(2));

}

//---- fin marina humeda servicio adicional -----------

//--- para marina humeda nueva cotización gasolina ---
$('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').keyup(function () {
  $('#g_cantidad').data('valor', $(this).val());
  $('#g_cantidad').html($(this).val());
  $('#g_cantidad_mxn').data('valor', $(this).val());
  $('#g_cantidad_mxn').html($(this).val());
  gasolinaCalculaSubtotales();
  gasolinaCalculaSubtotalesMxn();
});
$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').keyup(function () {
    var iva = $('#valiva').data('valor');
    var dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
    var cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
    var precioConIvaMXN = $(this).val();
    var precioConIvaUSD = precioConIvaMXN/dolar;
    var totalConIvaUSD =  cantidad * precioConIvaUSD;
    var ivaEquivalente = 100 + iva;
    var totalSinIvaUSD = (100*totalConIvaUSD)/ivaEquivalente;
    var ivaDelTotalUSD = totalConIvaUSD - totalSinIvaUSD;
    var precioSinIvaUSD = totalSinIvaUSD/cantidad;

    $('#g_precio').data('valor', precioSinIvaUSD * dolar);
    $('#g_precio').html('$ ' + parseFloat(precioSinIvaUSD * dolar).toFixed(2));
    $('#g_precio_mxn').data('valor', precioSinIvaUSD);
    $('#g_precio_mxn').html('$ ' + parseFloat(precioSinIvaUSD).toFixed(2));
    gasolinaCalculaSubtotales();
    gasolinaCalculaSubtotalesMxn();
});

$('#appbundle_marinahumedacotizacion_dolar').keyup(function () {
     var dolar = $(this).val();

     $('#g_precio_mxn').data('valor', $('#g_precio').data('valor') / dolar);
     $('#g_precio_mxn').html('$ ' + parseFloat( $('#g_precio').data('valor') / dolar).toFixed(2));
     gasolinaCalculaSubtotalesMxn();
});

function gasolinaCalculaSubtotales() {
    var iva = $('#valiva').data('valor');
    var cantidad = $('#g_cantidad').data('valor');
    var precio = $('#g_precio').data('valor');
    var subtotal = cantidad * precio;
    var ivatot = (subtotal * iva) / 100;
    var total = subtotal + ivatot;

    $('#g_subtotal').html('$ ' + parseFloat(subtotal).toFixed(2));
    $('#g_iva').html('$ ' + parseFloat(ivatot).toFixed(2));
    $('#g_total').html('$ ' + parseFloat(total).toFixed(2));

    $('#gransubtot_g').html(parseFloat(subtotal).toFixed(2));
    $('#graniva_g').html(parseFloat(ivatot).toFixed(2));
    $('#grantot_g').html(parseFloat(total).toFixed(2));
}

function gasolinaCalculaSubtotalesMxn() {
    var iva = $('#valiva').data('valor');
    var cantidad = $('#g_cantidad_mxn').data('valor');
    var precio = $('#g_precio_mxn').data('valor');
    var subtotal = cantidad * precio;
    var ivatot = (subtotal * iva) / 100;
    var total = subtotal + ivatot;

    $('#g_subtotal_mxn').html('$ ' + parseFloat(subtotal).toFixed(2));
    $('#g_iva_mxn').html('$ ' + parseFloat(ivatot).toFixed(2));
    $('#g_total_mxn').html('$ ' + parseFloat(total).toFixed(2));

    $('#gransubtot_g_mxn').html(parseFloat(subtotal).toFixed(2));
    $('#graniva_g_mxn').html(parseFloat(ivatot).toFixed(2));
    $('#grantot_g_mxn').html(parseFloat(total).toFixed(2));
}
//--- fin marina humeda nueva cotización gasolina ---


//--- para astillero nueva cotización ---
//--dolar--
$('#appbundle_astillerocotizacion_dolar').keyup(function () {
  var dolar = $(this).val();
  $('.dolarval').html('$ ' + dolar);
  var precio = 0;
  var auxprecio = 0;

    precio = $('#grua_precio').data('valor');
    auxprecio = precio / dolar;
    $('#grua_precio_mxn').data('valor',auxprecio);
    $('#grua_precio_mxn').html('$ '+parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#fila_grua_mxn'));

    precio = $('#suelo_precio').data('valor');
    auxprecio = precio / dolar;
    $('#suelo_precio_mxn').data('valor',auxprecio);
    $('#suelo_precio_mxn').html('$ '+parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#fila_suelo_mxn'));

    precio = $('#rampa_precio').data('valor');
    auxprecio = precio / dolar;
    $('#rampa_precio_mxn').data('valor',auxprecio);
    $('#rampa_precio_mxn').html('$ '+parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#cotizarampa_mxn'));

    precio = $('#karcher_precio').data('valor');
    auxprecio = precio / dolar;
    $('#karcher_precio_mxn').data('valor',auxprecio);
    $('#karcher_precio_mxn').html('$ '+parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#cotizakarcher_mxn'));

    precio = $('#varada_precio').data('valor');
    auxprecio = precio / dolar;
    $('#varada_precio_mxn').data('valor',auxprecio);
    $('#varada_precio_mxn').html('$ '+parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#cotizavarada_mxn'));

    var idfila = 0;
    $("#a_nuevacotizacion tbody .servicio-agregado").each(function () {
        precio = $(this).children('.valorprecio').data('valor');
        auxprecio = precio / dolar;
        idfila = $(this).data('id');
        $('#a_nuevacotizacion_mxn>tbody>#'+idfila+'>.valorprecio').data('valor',auxprecio);
        $('#a_nuevacotizacion_mxn>tbody>#'+idfila+'>.valorprecio').html('$ '+parseFloat(auxprecio).toFixed(2));
        calculaSubtotalesAstillero($('#a_nuevacotizacion_mxn>tbody>#'+idfila));
    });
});
$('#appbundle_astillerocotizacion_fechaLlegada').on("change", function () {
  var llegada = $(this).val();
  var salida = $('#appbundle_astillerocotizacion_fechaSalida').val();
  var dias = diasEntreFechas(llegada, salida);
  $('#appbundle_astillerocotizacion_diasEstadia').val(dias);
  $('#suelo_cantidad').html(dias);
  $('#suelo_cantidad').data('valor', dias);
  var fila = $('#fila_suelo');
  calculaSubtotalesAstillero(fila);

  $('#suelo_cantidad_mxn').html(dias);
  $('#suelo_cantidad_mxn').data('valor', dias);

  fila = $('#fila_suelo_mxn');
  calculaSubtotalesAstillero(fila);

});

$('#appbundle_astillerocotizacion_fechaSalida').on("change", function () {
  var llegada = $('#appbundle_astillerocotizacion_fechaLlegada').val();
  var salida = $(this).val();
  var dias = diasEntreFechas(llegada, salida);
  $('#appbundle_astillerocotizacion_diasEstadia').val(dias);
  $('#suelo_cantidad').html(dias);
  $('#suelo_cantidad').data('valor', dias);
  var fila = $('#fila_suelo');
  calculaSubtotalesAstillero(fila);

  $('#suelo_cantidad_mxn').html(dias);
  $('#suelo_cantidad_mxn').data('valor', dias);

  fila = $('#fila_suelo_mxn');
  calculaSubtotalesAstillero(fila);
});

//-- Uso de grua --
$('#appbundle_astillerocotizacion_acservicios_0_precio').keyup(function () {
  var grua_precio = $(this).val();
  $('#grua_precio').html('$ ' + grua_precio);
  $('#grua_precio').data('valor', grua_precio);
  var fila = $('#fila_grua');
  calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var grua_precio_mxn = grua_precio / dolar;
    $('#grua_precio_mxn').html('$ '+parseFloat(grua_precio_mxn).toFixed(2));
    $('#grua_precio_mxn').data('valor',grua_precio_mxn);

  fila = $('#fila_grua_mxn');
  calculaSubtotalesAstillero(fila);
});
//-- Uso de suelo --
$('#appbundle_astillerocotizacion_acservicios_1_precio').keyup(function () {
  var suelo_precio = $(this).val();
  $('#suelo_precio').html('$ ' + suelo_precio);
  $('#suelo_precio').data('valor', suelo_precio);
  var fila = $('#fila_suelo');
  calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var suelo_precio_mxn = suelo_precio / dolar;
    $('#suelo_precio_mxn').html('$ ' + parseFloat(suelo_precio_mxn).toFixed(2));
    $('#suelo_precio_mxn').data('valor', suelo_precio_mxn);

  fila = $('#fila_suelo_mxn');
  calculaSubtotalesAstillero(fila);
});
//-- Uso de rampa
$('#appbundle_astillerocotizacion_acservicios_2_estatus').on('click', function () {
  if ($('#appbundle_astillerocotizacion_acservicios_2_estatus').is(':checked')) {
    $('#cotizarampa').removeClass('hidden');
    $('#cotizarampa_mxn').removeClass('hidden');
  } else {
    $('#cotizarampa').addClass('hidden');
    $('#cotizarampa_mxn').addClass('hidden');
  }
  calculaTotalesAstillero();
  calculaTotalesAstilleroMXN()
});
$('#appbundle_astillerocotizacion_acservicios_2_precio').keyup(function () {
  var rampa_precio = $(this).val();
  $('#rampa_precio').html('$ ' + rampa_precio);
  $('#rampa_precio').data('valor', rampa_precio);
  var fila = $('#cotizarampa');
  calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var rampa_precio_mxn = rampa_precio / dolar;
    $('#rampa_precio_mxn').html('$ ' + parseFloat(rampa_precio_mxn).toFixed(2));
    $('#rampa_precio_mxn').data('valor', rampa_precio_mxn);

  fila = $('#cotizarampa_mxn');
  calculaSubtotalesAstillero(fila);
});
//--Uso de karcher
$('#appbundle_astillerocotizacion_acservicios_3_estatus').on('click', function () {
  if ($('#appbundle_astillerocotizacion_acservicios_3_estatus').is(':checked')) {
    $('#cotizakarcher').removeClass('hidden');
    $('#cotizakarcher_mxn').removeClass('hidden');
  } else {
    $('#cotizakarcher').addClass('hidden');
    $('#cotizakarcher_mxn').addClass('hidden');
  }
  calculaTotalesAstillero();
  calculaTotalesAstilleroMXN();
});
$('#appbundle_astillerocotizacion_acservicios_3_precio').keyup(function () {
  var karcher_precio = $(this).val();
  $('#karcher_precio').html('$ ' + karcher_precio);
  $('#karcher_precio').data('valor', karcher_precio);
  var fila = $('#cotizakarcher');
  calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var karcher_precio_mxn = karcher_precio / dolar;
    $('#karcher_precio_mxn').html('$ ' + parseFloat(karcher_precio_mxn).toFixed(2));
    $('#karcher_precio_mxn').data('valor', karcher_precio_mxn);

  fila = $('#cotizakarcher_mxn');
  calculaSubtotalesAstillero(fila);
});
//-- Sacar varada y botadura
// $('#appbundle_astillerocotizacion_acservicios_4_estatus').on('click', function () {
//     if ($('#appbundle_astillerocotizacion_acservicios_4_estatus').is(':checked')) {
//         $('#cotizavarada').removeClass('hidden');
//         $('#cotizavarada_mxn').removeClass('hidden');
//     } else {
//         $('#cotizavarada').addClass('hidden');
//         $('#cotizavarada_mxn').addClass('hidden');
//     }
//     calculaTotalesAstillero();
//     calculaTotalesAstilleroMXN();
// });
// $('#appbundle_astillerocotizacion_acservicios_4_precio').keyup(function () {
//     var varada_precio = $(this).val();
//     $('#varada_precio').html('$ ' + varada_precio);
//     $('#varada_precio').data('valor', varada_precio);
//     var fila = $('#cotizavarada');
//     calculaSubtotalesAstillero(fila);
//
//     var dolar = $('#appbundle_astillerocotizacion_dolar').val();
//     var varada_precio_mxn = varada_precio * dolar;
//     $('#varada_precio_mxn').html('$ ' + parseFloat(varada_precio_mxn).toFixed(2));
//     $('#varada_precio_mxn').data('valor', varada_precio_mxn);
//
//     fila = $('#cotizavarada_mxn');
//     calculaSubtotalesAstillero(fila);
// });
$('#a_nuevacotizacion').on('keyup', 'input', function () {

  $(this).parent().data('valor', $(this).val());
  var fila = $(this).parent().parent();
  calculaSubtotalesAstillero(fila);

  var idfila = $(this).parent().parent().data('id');
  var clasecelda = $(this).parent().attr('class');

  if (clasecelda == 'td-otroservicio') {
    $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.td-otroservicio').html($(this).val());
  } else {
    $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorcantidad').data('valor', $(this).val());
    $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorcantidad').html($(this).val());
    fila = $('#a_nuevacotizacion_mxn>tbody>#' + idfila);
    calculaSubtotalesAstillero(fila);
  }
});
$('#a_nuevacotizacion').on('keyup', '.valorprecio>.input-group>input', function () {

  $(this).parent().parent().data('valor', $(this).val());
  var fila = $(this).parent().parent().parent();
  calculaSubtotalesAstillero(fila);

    var idfila =  $(this).parent().parent().parent().data('id');
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var valormxn = $(this).val() / dolar;
    $('#a_nuevacotizacion_mxn>tbody>#'+idfila+'>.valorprecio').data('valor',valormxn);
    $('#a_nuevacotizacion_mxn>tbody>#'+idfila+'>.valorprecio').html('$ '+parseFloat(valormxn).toFixed(2));

  fila = $('#a_nuevacotizacion_mxn>tbody>#' + idfila);
  calculaSubtotalesAstillero(fila);
});

function calculaSubtotalesAstillero(fila) {
  var iva = $('#valorsistemaiva').data('valor');
  var cantidadAd = fila.children('.valorcantidad').data('valor');
  var precioAd = fila.children('.valorprecio').data('valor');
  var subtotalAd = cantidadAd * precioAd;
  var ivaAd = (subtotalAd * iva) / 100;
  var totalAd = subtotalAd + ivaAd;

  fila.children('.valorsubtotal').html('$ ' + parseFloat(subtotalAd).toFixed(2));
  fila.children('.valorsubtotal').data('valor', subtotalAd);

  fila.children('.valoriva').html('$ ' + parseFloat(ivaAd).toFixed(2));
  fila.children('.valoriva').data('valor', ivaAd);

  fila.children('.valortotal').html('$ ' + parseFloat(totalAd).toFixed(2));
  fila.children('.valortotal').data('valor', totalAd);
  calculaTotalesAstillero();
  calculaTotalesAstilleroMXN();
}

function calculaTotalesAstillero() {
  var granSubtotalAd = 0;
  var granIvaAd = 0;
  var granTotalAd = 0;

  $("#a_nuevacotizacion tbody tr").each(function () {
    if (!$(this).hasClass('hidden')) {
      granSubtotalAd += $(this).children('.valorsubtotal').data('valor');
      granIvaAd += $(this).children('.valoriva').data('valor');
      granTotalAd += $(this).children('.valortotal').data('valor');
    }
  });

  $('#gransubtot').html(parseFloat(granSubtotalAd).toFixed(2));
  $('#graniva').html(parseFloat(granIvaAd).toFixed(2));
  $('#grantot').html(parseFloat(granTotalAd).toFixed(2));

}

function calculaTotalesAstilleroMXN() {
  var granSubtotalAd = 0;
  var granIvaAd = 0;
  var granTotalAd = 0;

  $("#a_nuevacotizacion_mxn tbody tr").each(function () {
    if (!$(this).hasClass('hidden')) {
      granSubtotalAd += $(this).children('.valorsubtotal').data('valor');
      granIvaAd += $(this).children('.valoriva').data('valor');
      granTotalAd += $(this).children('.valortotal').data('valor');
    }
  });

  $('#gransubtot_mxn').html(parseFloat(granSubtotalAd).toFixed(2));
  $('#graniva_mxn').html(parseFloat(granIvaAd).toFixed(2));
  $('#grantot_mxn').html(parseFloat(granTotalAd).toFixed(2));

}

/*
    Mostrar nombre de archivos en input file
 */
const inputFiles = document.querySelectorAll('input[type="file"]');
if (inputFiles.length) { // Hay que asegurarse que hay inputs en la pagina para no mandar errores en la consola
  [...inputFiles
  ].map(input => {
    let label = document.querySelector(`label[for="${input.getAttribute('id')}"]`);
    input.addEventListener('change', function (e) {
      if (!label) return; // El input no tiene una label adyacente
      label.innerHTML = this.files.length > 1 ? `${this.files.length} archivos seleccionados` : e.target.value.split('\\').pop();
    });
  })
  ;
}

/*
  Configuracion basica para datatables
 */

const datatablesSettings = {
  serverSide: true,
  processing: true,
  responsive: true,
  language: {
    lengthMenu: 'Mostrar _MENU_ entradas',
    zeroRecords: 'No hay entradas',
    info: 'Mostrando la pagina _PAGE_ of _PAGES_',
    infoEmpty: 'No hay entradas disponibles',
    infoFiltered: '(filtados de _MAX_ total de entradas)',
    processing: 'Procesando...',
    thousands: 'Millones',
    loadingRecords: 'Cargando entradas...',
    search: 'Buscar',
    paginate: {
      first: 'Primera',
      last: 'Ultima',
      next: 'Siguiente',
      previous: 'Anterior',
    }
  },
  searchDelay: 500,
  columnDefs: [{targets: 'no-sort', orderable: false}],
  initComplete: function () {
    this.api().columns('.with-choices').every(function () {
      const column = this;
      const columnHeader = column.header();
      const select = document.createElement('select');
      let columnName = columnHeader.innerHTML.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");

      select.add(new Option(columnHeader.innerHTML, ''));
      columnHeader.innerHTML = '';
      columnHeader.appendChild(select);

      select.addEventListener('click', e => e.stopPropagation());
      select.addEventListener('change', function () {
        let val = $.fn.dataTable.util.escapeRegex(this.value);
        column.search(val, true, true).draw();
      });

      $.ajax({
        url: `${location.href}${columnName}.json`,
        success: function (options) {
          options
              .map(opt => opt.nombre || opt.name)
              .filter((item, index, array) => array.indexOf(item || '') === index)
              .sort()
              .forEach(optionValue => select.add(new Option(optionValue, optionValue)));
        }
      });

      // LIMITADO A LOS DATOS QUE RECIBE EN EL PRIMER QUERY
      // this.data().unique().sort().map((optionValue) => { select.add(new Option(optionValue, optionValue)) });
    });
  },
};
