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
    var url = $('#linkdetalle').attr('href');
    var urlnueva = url.replace("comodinIdCotizacion", $(this).data('idcotizacion'));
    $('#linkdetalle').attr('href', urlnueva);
    $('#linkdetalle').data('id', $(this).data('idcotizacion'));

    if ($(this).data('idcotizacion') == 0) {
      $('#linkdetalle').hide();
    } else {
      $('#linkdetalle').show();
    }
    $('#modalinfobarco').modal('toggle');
  });
  $('#modalinfobarco').on('hidden.bs.modal', function (e) {
    console.log($('#linkdetalle').data('id'));
    var url = $('#linkdetalle').attr('href');
    var urlnueva = url.replace($('#linkdetalle').data('id'), "comodinIdCotizacion");
    $('#linkdetalle').attr('href', urlnueva);
  });

  var $selectBuscador = $('.select-buscador');

  if ($selectBuscador.length) {
  $selectBuscador.select2();
  }

  if ($.fn.datepicker) {
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
      autoclose: true
    });

    $('.datepicker-solo').datepicker({
      format: 'yyyy-mm-dd',
      language: "es",
      orientation: "bottom auto",
      autoclose: true
    });
  }

  var $editorWSY = $('.editorwy');

  if ($editorWSY.length) {
    $editorWSY.wysihtml5({
      toolbar: {
        "image": false,
        "color": false,
        "link": false,
        "html": false
      }
    });
  }

  $('.cuadro-zona').on('click', function () {
    var direc = $(this).data('direccion');
    window.location.href = direc;
  });

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
  $("#appbundle_marinahumedacotizacion_validacliente_0").click(function () {
    $('#notarechazado').hide();
  });
  $("#appbundle_marinahumedacotizacion_validacliente_1").click(function () {
    $('#notarechazado').show();
  });
  $("#appbundle_astillerocotizacion_validanovo_0").click(function () {
    $('#notarechazado').hide();
  });
  $("#appbundle_astillerocotizacion_validanovo_1").click(function () {
    $('#notarechazado').show();
  });
  $("#appbundle_astillerocotizacion_validacliente_0").click(function () {
    $('#notarechazado').hide();
  });
  $("#appbundle_astillerocotizacion_validacliente_1").click(function () {
    $('#notarechazado').show();
  });
  $('.opcionrechazar').click(function () {
    $('#notarechazado').show();
  });
  $('.opcionaceptar').click(function () {
    $('#notarechazado').hide();
  });
  $('.limite100').on('input', function () {
    var value = $(this).val();
    if ((value !== '') && (value.indexOf('.') === -1)) {
      $(this).val(Math.max(Math.min(value, 100), 0));
    }
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
(function () {
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
    $('.select-buscador').select2();
    newLi.before(newLi);
  });

  $(document).on("change", ".selectclientebuscar", function () {
    var precio = $(this).find(':selected').data('precio');
    var subtotalHolder = $(this.parentNode.parentNode.parentNode).find('.subtotal');
    var can = $(this.parentNode.parentNode.parentNode).find('.cantidad');
    calculateProducto(can, precio, subtotalHolder);

    can.on('input', function () {
      calculateProducto(this, precio, subtotalHolder);
      console.log($(this).parent().parent().find('.entregado'));
        $(this).parent().parent().find('.entregado').val($(this).val());
    });
  });

  $("#appbundle_tienda_solicitud_preciosolespecial").on("input", function () {
    suma();
  });

  function suma() {
    var sumar = $("#appbundle_tienda_solicitud_preciosolespecial").val();
    var subesptotal = $("#appbundle_tienda_solicitud_subtotal").val();
    var valfinal = Math.abs(sumar) + Math.abs(subesptotal);
    $("#appbundle_tienda_solicitud_total").val(valfinal.toFixed(2));
  }

  function calculateProducto(can, precio, subtotalHolder) {
    var cantidad = $(can).val();
    var subtotal = cantidad / 100 * precio;
    $(subtotalHolder).val(subtotal.toFixed(2));
    calculateGrandTotal();
  }

  function calculateGrandTotal() {
    var grandTotal = 0;
    $(document).find(".subtotal").each(function () {
      grandTotal += +$(this).val();
    });
    $("#appbundle_tienda_solicitud_subtotal").val(grandTotal.toFixed(2));
    suma();
  }

  // $("#appbundle_tienda_solicitud_solicitudEspecial").on("input", function () {
  //     $("#appbundle_tienda_solicitud_preciosolespecial").removeAttr("readonly");
  // });

  $('.lista-productos').on('click', '.remove-producto', function (e) {
    e.preventDefault();
    var resta = $(this.parentNode.parentNode).find('.subtotal').val();
    var subrestatotal = $("#appbundle_tienda_solicitud_subtotal").val();
    var restatotal = Math.abs(subrestatotal - resta);
    $("#appbundle_tienda_solicitud_subtotal").val(restatotal.toFixed(2));
    suma();
    $(this.parentNode.parentNode.remove());
    return false;
  });
})();

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
  $(this).parent().parent().parent().remove();
  return false;
});

//collectio al agregar servicios adicionales marina humeda
jQuery('.add-another-servicio-adicional').click(function (e) {
  e.preventDefault();
  var totServicios = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var servicioListPrimero = jQuery('#servicio-adicional-fields-list' + lista);
  var newWidget = $(servicioListPrimero).data('prototype');
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-producto', 'hide');
  totServicios++;
  $(this).data('cantidad', totServicios);
  var newLi = jQuery('<tr class="servicio-agregado"></tr>').html(newWidget);
  newLi.appendTo(servicioListPrimero);
  newLi.before(newLi);
});

$('.lista-servicios-adicionales').on('click', '.remove-servicio-adicional', function (e) {
  e.preventDefault();
  $(this).parent().parent().remove();
  calculaTotalesAdicionales();
  return false;
});

//collectio al agregar pagos a una cotización marina húmeda
jQuery('.add-another-pago').click(function (e) {
  e.preventDefault();
  var totPagos = $(this).data('cantidad');
  var lista = $(this).data('idlista');
  var pagoListPrimero = jQuery('#pago-fields-list' + lista);
  var newWidget = $(pagoListPrimero).data('prototype');
  newWidget = newWidget.replace(/__name__/g, totPagos);
  totPagos++;
  $(this).data('cantidad', totPagos);
  var newLi = jQuery('<tr class="pago-agregado"></tr>').html(newWidget);
  newLi.appendTo(pagoListPrimero);
  newLi.find('.input-calendario').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    orientation: 'bottom',
  });
  newLi.before(newLi);
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

/* Marina Húmeda */
function diasEntreFechas(inicio, fin) {
  var start = new Date(inicio.toString());
  var end = new Date(fin.toString());
  var diff = new Date(end - start);
  var days = (diff / 1000 / 60 / 60 / 24);
  return days;
}

/* VARIABLES DE MARINA HÚMEDA */
var de_cantidad = 0;
var de_precio = 0;
var de_precio_mxn = 0;
var e_cantidad = 0;
var e_precio = 0;
var e_precio_mxn = 0;
var descuento = 0;
var dolar = 0;
//var dolar = $('#valdolar').data('valor');
var descuento_estadia = $('#appbundle_marinahumedacotizacion_descuentoEstadia');
var descuento_electricidad = $('#appbundle_marinahumedacotizacion_descuentoElectricidad');
var estadiaOtroPrecio = document.getElementById('appbundle_marinahumedacotizacion_mhcservicios_0_precioOtro');
var electricidadOtroPrecio = document.getElementById('appbundle_marinahumedacotizacion_mhcservicios_1_precioOtro');
var estadiaSelectPrecios = document.getElementById('appbundle_marinahumedacotizacion_mhcservicios_0_precio');
var electricidadSelectPrecios = document.getElementById('appbundle_marinahumedacotizacion_mhcservicios_1_precioAux');
var electricityDays = $("#appbundle_marinahumedacotizacion_diasElectricidad");

/* Marina Húmeda */
// Evento para la actualizacion de las tablas y precios por dias de electricidad.
electricityDays.on("change",function () {
  var dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();

  // Si los dias de electricidad superan los dias de estadia, lo reduce a los dias de estadia.
  if(dias_estadia < electricityDays.val()) {
    electricityDays.val(dias_estadia);
  }
  // Setea los dias de electricidad
  $('#e_cantidad').html(electricityDays.val());
  $('#e_cantidad_mxn').html(electricityDays.val());

  // Obtiene los precios
  e_precio = electricidadOtroPrecio.value ?
  electricidadOtroPrecio.value :
  ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;

  // Recalcula todo
  calculaSubtotales(electricityDays.val(), e_precio, descuento_electricidad.val(),$('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(electricityDays.val(), e_precio_mxn, descuento_electricidad.val(),$('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
});

/* Marina Húmeda */
$('#appbundle_marinahumedacotizacion_fechaLlegada').on("change", function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var llegada = $(this).val();
  var salida = $('#appbundle_marinahumedacotizacion_fechaSalida').val();
  dias_estadia = diasEntreFechas(llegada, salida);
  // $('#dias_estadia_cantidad').html(dias_estadia);
  // $('#dias_estadia_cantidad').data('valor', dias_estadia);
  $('#appbundle_marinahumedacotizacion_diasEstadia').val(dias_estadia);

  de_precio = estadiaOtroPrecio.value ?
    estadiaOtroPrecio.value :
    ($('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100);
  de_precio_mxn = de_precio * dolar;

  // Si los dias de electricidad superan los dias de estadia, lo reduce a los dias de estadia.
  if(dias_estadia < electricityDays.val()) {
    electricityDays.val(dias_estadia);
  }
  $('#de_cantidad').html(dias_estadia);
  $('#de_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(dias_estadia, de_precio_mxn, descuento_estadia.val(), $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  // Setea los dias de electricidad
  $('#e_cantidad').html(electricityDays.val());
  $('#e_cantidad_mxn').html(electricityDays.val());

  // Obtiene los precios
  e_precio = electricidadOtroPrecio.value ?
  electricidadOtroPrecio.value :
  ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;

  // Recalcula todo
  calculaSubtotales(electricityDays.val(), e_precio, descuento_electricidad.val(),$('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(electricityDays.val(), e_precio_mxn, descuento_electricidad.val(),$('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
});

/* Marina Húmeda */
$('#appbundle_marinahumedacotizacion_fechaSalida').on("change", function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var llegada = $('#appbundle_marinahumedacotizacion_fechaLlegada').val();
  var salida = $(this).val();
  dias_estadia = diasEntreFechas(llegada, salida);
  // $('#dias_estadia_cantidad').html(dias_estadia);
  // $('#dias_estadia_cantidad').data('valor', dias_estadia);
  $('#appbundle_marinahumedacotizacion_diasEstadia').val(dias_estadia);

  de_precio = estadiaOtroPrecio.value ?
    estadiaOtroPrecio.value :
    ($('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100);
  de_precio_mxn = de_precio * dolar;

  // Si los dias de electricidad superan los dias de estadia, lo reduce a los dias de estadia.
  if(dias_estadia < electricityDays.val()) {
    electricityDays.val(dias_estadia);
  }
  $('#de_cantidad').html(dias_estadia);
  $('#de_cantidad_mxn').html(dias_estadia);

  calculaSubtotales(dias_estadia, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(dias_estadia, de_precio_mxn, descuento_estadia.val(),$('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  $('#e_cantidad').html(electricityDays.val());
  $('#e_cantidad_mxn').html(electricityDays.val());

  // Obtiene los precios
  e_precio = electricidadOtroPrecio.value ?
  electricidadOtroPrecio.value :
  ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;

  // Recalcula todo
  calculaSubtotales(electricityDays.val(), e_precio, descuento_electricidad.val(),$('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(electricityDays.val(), e_precio_mxn, descuento_electricidad.val(),$('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
});

/* Marina Húmeda */
//-- Días estadía --
$('#appbundle_marinahumedacotizacion_diasEstadia').keyup(function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
  de_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val() / 100);
  de_precio_mxn = de_precio * dolar;
  $('#de_cantidad').html(dias_estadia);
  $('#de_cantidad_mxn').html(dias_estadia);
  calculaSubtotales(dias_estadia, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(dias_estadia, de_precio_mxn, descuento_estadia.val(), $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));
  e_precio = ($('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').val() / 100);
  e_precio_mxn = e_precio * dolar;
  $('#e_cantidad').html(dias_estadia);
  $('#e_cantidad_mxn').html(dias_estadia);
  calculaSubtotales(dias_estadia, e_precio, descuento_electricidad.val(), $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(dias_estadia, e_precio_mxn, descuento_electricidad.val(), $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
});

/* Marina Húmeda */
function recalculaCantidadYprecio (){
   dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
   dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
   $('#de_cantidad').html(dias_estadia);
   $('#de_cantidad_mxn').html(dias_estadia);

   $('#e_cantidad').html(dias_estadia);
   $('#e_cantidad_mxn').html(dias_estadia);

   let de_precio = estadiaOtroPrecio.value ? estadiaOtroPrecio.value : (estadiaSelectPrecios.value/100);
   let de_precio_mxn = (de_precio * dolar).toFixed(2);
   $('#de_precio').html('$ ' + de_precio);
   $('#de_precio_mxn').html('$ ' + de_precio_mxn);

   let e_precio = electricidadOtroPrecio.value ? electricidadOtroPrecio.value : (electricidadSelectPrecios.value/100);
   let e_precio_mxn = (e_precio * dolar).toFixed(2);
   $('#e_precio').html('$ ' + e_precio);
   $('#e_precio_mxn').html('$ ' + e_precio_mxn);
   recalculaSubtotalesYtotal();
}

/* Marina Húmeda */
$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').on('change', function () {
    estadiaOtroPrecio.value = '';
    dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
    de_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
    de_precio = ($(this).val() / 100);
    de_precio_mxn = (de_precio * dolar).toFixed(2);

    $('#de_precio').html('$ ' + de_precio);
    $('#de_precio_mxn').html('$ ' + de_precio_mxn);
    calculaSubtotales(de_cantidad, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
    calculaSubtotales(de_cantidad, de_precio_mxn, descuento_estadia.val(), $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));
    calculaTotales();

});

/* Marina Húmeda */
if(estadiaOtroPrecio){
  estadiaOtroPrecio.addEventListener('keyup',() => {
    estadiaSelectPrecios.value = '';
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  de_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
  de_precio = estadiaOtroPrecio.value ? estadiaOtroPrecio.value : 0;
  de_precio_mxn = (de_precio * dolar).toFixed(2);
  $('#de_precio').html('$ ' + de_precio);
  $('#de_precio_mxn').html('$ ' + de_precio_mxn);
  calculaSubtotales(de_cantidad, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(de_cantidad, de_precio_mxn, descuento_estadia.val(), $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));
  calculaTotales();
  });
}

/* Marina Húmeda */
$('#appbundle_marinahumedacotizacion_mhcservicios_1_precioAux').on('change', function () {
    electricidadOtroPrecio.value = '';
    dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
    e_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
    e_precio = ($(this).val() / 100);
    e_precio_mxn = (e_precio * dolar).toFixed(2);

    $('#e_precio').html('$ ' + e_precio);
    $('#e_precio_mxn').html('$ ' + e_precio_mxn);
    calculaSubtotales(e_cantidad, e_precio, descuento_electricidad.val(), $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
    calculaSubtotales(e_cantidad, e_precio_mxn, descuento_electricidad.val(), $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
    calculaTotales();

});

/* Marina Húmeda */
if(electricidadOtroPrecio){
  electricidadOtroPrecio.addEventListener('keyup',() => {
    electricidadSelectPrecios.value = '';
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  e_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
  e_precio = electricidadOtroPrecio.value ? electricidadOtroPrecio.value : 0;
  e_precio_mxn = (e_precio * dolar).toFixed(2);
  $('#e_precio').html('$ ' + e_precio);
  $('#e_precio_mxn').html('$ ' + e_precio_mxn);
  calculaSubtotales(e_cantidad, e_precio, descuento_electricidad.val(), $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(e_cantidad, e_precio_mxn, descuento_electricidad.val(), $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));
  calculaTotales();
  });
}

/* Marina Húmeda */
//-- Descuentos --
$('#appbundle_marinahumedacotizacion_descuentoEstadia').keyup(function () {
  recalculaSubtotalesYtotal();
});
//-- Electricidad --
$('#appbundle_marinahumedacotizacion_descuentoElectricidad').keyup(function () {
  recalculaSubtotalesYtotal();
});
//-- Dolar --
$('#appbundle_marinahumedacotizacion_dolar').keyup(function () {
  $('.valdolar').html(parseFloat($(this).val()).toFixed(2) + ' MXN');
  recalculaSubtotalesYtotal();
});

/* Marina Húmeda */
function recalculaSubtotalesYtotal() {

  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
  de_cantidad = dias_estadia;
  de_precio = estadiaOtroPrecio.value ? estadiaOtroPrecio.value : (estadiaSelectPrecios.value/100);
  de_precio_mxn = de_precio * dolar;
  calculaSubtotales(de_cantidad, de_precio, descuento_estadia.val(), $('#de_subtotal'), $('#de_iva'), $('#de_descuento'), $('#de_total'));
  calculaSubtotales(de_cantidad, de_precio_mxn,descuento_estadia.val(), $('#de_subtotal_mxn'), $('#de_iva_mxn'), $('#de_descuento_mxn'), $('#de_total_mxn'));

  e_cantidad = dias_estadia;
  e_precio = electricidadOtroPrecio.value ? electricidadOtroPrecio.value : (electricidadSelectPrecios.value/100);
  e_precio_mxn = e_precio * dolar;
  calculaSubtotales(e_cantidad, e_precio, descuento_electricidad.val(), $('#e_subtotal'), $('#e_iva'), $('#e_descuento'), $('#e_total'));
  calculaSubtotales(e_cantidad, e_precio_mxn, descuento_electricidad.val(), $('#e_subtotal_mxn'), $('#e_iva_mxn'), $('#e_descuento_mxn'), $('#e_total_mxn'));

  calculaTotales();
}

/* Marina Húmeda */
function calculaSubtotales(cantidad, precio,descuento, tdsubtot, tdiva, tddesc, tdtot) {
    var eslora = 0;
    if ($('#de_eslora').data('valor')) {
        eslora = $('#de_eslora').data('valor');
    }
    var iva = ($('#valiva').data('valor')) / 100;
    var subtotal = cantidad * precio * eslora;
    var desctot = (subtotal * descuento) / 100;
    var subtotal_descuento = subtotal - desctot;
    var ivatot = subtotal_descuento * iva;
    var total = (subtotal_descuento + ivatot).toFixed(2);

  tdsubtot.html('$ ' + (subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  tdiva.html('$ ' + (ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  tddesc.html('$ ' + (desctot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' ('+descuento+'%)');
  tdtot.html('$ ' + total.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

  tdsubtot.data('valor', subtotal);
  tdiva.data('valor', ivatot);
  tddesc.data('valor', desctot);
  tdtot.data('valor', total);
}

/* Marina Húmeda */
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

  $('#gransubtot').html(gransubtotal.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva').html(graniva.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grandecuento').html(grandescuento.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot').html(grantotal.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var gransubtotalmxn = (gransubtotal * dolar).toFixed(2);
  var granivamxn = (graniva * dolar).toFixed(2);
  var grandescuentomxn = (grandescuento * dolar).toFixed(2);
  var grantotalmxn = (grantotal * dolar).toFixed(2);
  $('#gransubtot_mxn').html(gransubtotalmxn.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva_mxn').html(granivamxn.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grandecuento_mxn').html(grandescuentomxn.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot_mxn').html(grantotalmxn.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
}

/* Marina Húmeda */
$('#servicioAdicional').on('keyup', 'input', function () {
  var fila = $(this).parent().parent();
  calculaSubtotalesAdicionales(fila);
});

/* Marina Húmeda */
$('#appbundle_marinahumedacotizacionadicional_iva').on('keyup', function () {
    document.querySelectorAll('.servicio-agregado').forEach(fila =>{calculaSubtotalesAdicionales($(fila))});
});

/* ALERT! -> esta función se consume por astillero. Cuidado. */
/* Marina Húmeda */
function calculaSubtotalesAdicionales(fila) {
  console.log(fila);
  var iva = document.getElementById('appbundle_marinahumedacotizacionadicional_iva').value();
  var cantidadAd = fila.children('.valorcantidad').children('input').val();
  var precioAd = fila.children('.valorprecio').children('.input-group').children('input').val();
  var subtotalAd = cantidadAd * precioAd;
  var ivaAd = (subtotalAd * iva) / 100;
  var totalAd = subtotalAd + ivaAd;
  fila.children('.valorsubtotal').children('.input-group').children('input').val((subtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valoriva').children('.input-group').children('input').val((ivaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valortotal').children('.input-group').children('input').val((totalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  calculaTotalesAdicionales();
}

/* ALERT! -> esta función se consume por astillero. Cuidado. */
/* Marina Húmeda */
function calculaTotalesAdicionales() {
  var granSubtotalAd = 0;
  let granIvaAd = 0;
  let granTotalAd = 0;
  document.querySelectorAll('.servicio-agregado').forEach(fila => {
    granSubtotalAd += Number((fila.children[4].children[0].children[1].value).split(',').join(''));
    granIvaAd += Number((fila.children[5].children[0].children[1].value).split(',').join(''));
    granTotalAd += Number((fila.children[6].children[0].children[1].value).split(',').join(''));
  });
  $('#appbundle_marinahumedacotizacionadicional_subtotal').val(granSubtotalAd.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#appbundle_marinahumedacotizacionadicional_ivatotal').val(granIvaAd.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#appbundle_marinahumedacotizacionadicional_total').val(granTotalAd.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
}

/** ********************************************************************
 * FUNCIONES VARIAS
 * 
 * Funciones varias, aún no se detecta donde se utilizan, como o porque.
 ***********************************************************************/

/* Mostrar nombre de archivos en input file */
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

/* CONFIGURACIÓN BÁSICA PARA DATATABLES */
const datatablesSettings = {
  serverSide: true,
  processing: true,
  responsive: true,
  language: {
    lengthMenu: 'Mostrar _MENU_ registros',
    zeroRecords: 'No hay registros',
    info: 'Mostrando la pagina _PAGE_ de _PAGES_',
    infoEmpty: 'No hay registros disponibles',
    infoFiltered: '(filtados de _MAX_ total de registros)',
    processing: 'Procesando...',
    loadingRecords: 'Cargando registros...',
    search: 'Buscar',
    paginate: {
      first: 'Primera',
      last: 'Ultima',
      next: 'Siguiente',
      previous: 'Anterior',
    }
  },
  searchDelay: 500,
  columnDefs: [
    {targets: 'no-sort', orderable: false},
    {targets: 'no-show', visible: false, searchable: false}
  ],
  initComplete: function () {
    this.api().columns('.with-choices').every(function () {
      const column = this;
      const columnHeader = column.header();
      const select = document.createElement('select');
      let columnName = columnHeader.innerHTML.toLowerCase().normalize('NFD').replace(/#/g, "").replace(/[\u0300-\u036f]/g, "");

      select.add(new Option(columnHeader.innerHTML, ''));
      columnHeader.innerHTML = '';
      columnHeader.appendChild(select);

      select.addEventListener('click', e => e.stopPropagation());
      select.addEventListener('change', function () {
        let val = $.fn.dataTable.util.escapeRegex(this.value);
        column.search(val, true, true).draw();
      });

      $.ajax({
        url: `${location.protocol + '//' + location.host + location.pathname}${columnName}.json`,
        success: function (options) {
          options
              .map(opt => opt.nombre || opt.name)
              .filter((item, index, array) => array.indexOf(item || '') === index)
              .sort()
              .forEach(optionValue => select.add(new Option(optionValue, optionValue)))
          ;
        }
      });
      // LIMITADO A LOS DATOS QUE RECIBE EN EL PRIMER QUERY
      // this.data().unique().sort().map((optionValue) => { select.add(new Option(optionValue, optionValue)) });
    });
  },
};

/* MOSTRAR TAB ACTIVA DONDE HAY ERRORES */
(function ($) {
  const fooForm = document.querySelector('form') || undefined;
  const tabContent = document.querySelector('.tab-content') || undefined;
  const firstError = tabContent ? tabContent.querySelector('.has-error') : undefined;
  const helpBlocks = tabContent ? tabContent.querySelector('.help-block') : undefined;
  const tabPanes = tabContent ? tabContent.querySelectorAll('.tab-pane') : undefined;
  const tabs = document.querySelectorAll('.nav-tabs > li') || undefined;

  if (fooForm && tabPanes) {
    fooForm.addEventListener('invalid', e => {
      tabPanes.forEach(pane => pane.querySelector(`#${e.target.getAttribute('id')}`) ? showErrors(pane) : false);
    }, true);
  }

  if (fooForm && tabContent && (firstError || helpBlocks) && tabPanes && tabs) {
    let errorElement = firstError || helpBlocks;
    const paneWithError = $(tabPanes).has(errorElement);
    showErrors(paneWithError[0]);
  }

  function showErrors(pane) {
    if (!pane) return;
    const tabId = pane.getAttribute('id');
    const tab = document.querySelector(`[href="#${tabId}"]`).parentNode;

    tabs.forEach(elem => elem.classList.remove('active'));
    tabPanes.forEach(elem => elem.classList.remove('active'));
    tab.classList.add('active');
    pane.classList.add('active');

    return false;
  }
})(jQuery);

/* ALERTAS PARA INVENTARIO */
let $ErrorContainer = $('#errors');

if (!$ErrorContainer.length) {
  $('.content-wrapper > .content').prepend('<div id="errors"></div>');
  $ErrorContainer = $('#errors');
}

function throwAlert(message, type) {
  const html = `
       <div class="alert alert-dismissable alert-${type || 'info'}" role="alert">
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
         </button>
         ${message}
       </div>
   `;

  $ErrorContainer.append(html);

  if ($ErrorContainer.children().length > 2) {
    $ErrorContainer.children().first().remove();
  }
}

const formularioGeneral = document.querySelector('form');
if( formularioGeneral ) {
    if(!$(':input[type="submit"]').hasClass('no-loading')){
        $(':input[type="submit"]').data('loading-text', 'Cargando...');
        formularioGeneral.addEventListener('submit', function () {
            $(':input[type="submit"]').button('loading');
        });
    }
}

/** ********************************************************************
 * FUNCIONES OBSOLETAS
 * 
 * Funciones que no se utilizan más o no se consumen.
 ***********************************************************************/

/* Función obsoleta, es llamada dentro de un twig llamado "dias-adicionales.html.twig"
que nunca se ejecuta en código. */
$('#a_nuevacotizacion').on('keyup', '.valorprecio>.input-group>input', function () {
  $(this).parent().parent().data('valor', $(this).val());
  var fila = $(this).parent().parent().parent();
  calculaSubtotalesAstillero(fila);
  var idfila = $(this).parent().parent().parent().data('id');
  var dolar = $('#appbundle_astillerocotizacion_dolar').val();
  var valormxn = $(this).val() / dolar;
  $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').data('valor', valormxn);
  $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').html('$ ' + parseFloat(valormxn).toFixed(2));
  fila = $('#a_nuevacotizacion_mxn>tbody>#' + idfila);
  calculaSubtotalesAstillero(fila);
});

/* Bloque de código comentado por antiguos desarrolladores. No se remueve por si acaso. */
/* $('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').on('click',function () {
    if($('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').is(':checked')) {
        $('#cotizagasolina').removeClass('hidden');
    } else {
        $('#cotizagasolina').addClass('hidden');
    }
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').on('click',function () {
   if($('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').is(':checked')){
       $('#cotizadezasolve').removeClass('hidden');
   }else{
       $('#cotizadezasolve').addClass('hidden');
   }
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').on('click',function () {
    if($('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').is(':checked')){
        $('#cotizalimpieza').removeClass('hidden');
    }else{
        $('#cotizalimpieza').addClass('hidden');
    }
    calculaTotales();
}); */

