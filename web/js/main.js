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
        "html": true,
      }
    });
  }

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

    // $(".limite100").keypress(function () {
    //     if(parseFloat($(this).val())<=100){
    //         return true;
    //     }else{
    //         return false;
    //     }
    // });
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

  newLi.find('.input-calendario').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd',
    orientation: 'bottom',
  });
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

//---------- colection al agregar contratista a ODT -----------------
jQuery('.add-another-proveedor').click(function (e) {
    e.preventDefault();
    // var elementoMotor = document.getElementsByClassName(this);
    var totProveedor = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var proveedorListPrimero = jQuery('#proveedor-fields-list' + lista);
    //var motorListOtros = jQuery('.lista-motores'+lista);
    // grab the prototype template
    var newWidget = $(proveedorListPrimero).data('prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, totProveedor);
    totProveedor++;
    $(this).data('cantidad', totProveedor);
    // create a new list element and add it to the list
    var newLi = jQuery('<div class="row"></div>').html(newWidget);
    newLi.appendTo(proveedorListPrimero);
    newLi.find('.input-calendario').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        orientation: "auto",
    });
    // also add a remove button, just for this example
    //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

    newLi.before(newLi);
});
$('.lista-proveedores').on('click', '.remove-proveedor', function (e) {
    e.preventDefault();
    //console.log('quitar motor');
    $(this).parent().parent().remove();

    return false;
});

//---------- colection al agregar bancos a un proveedor -----------------
jQuery('.add-another-banco').click(function (e) {
    e.preventDefault();
    var totBanco = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var bancoListPrimero = jQuery('#banco-fields-list' + lista);
    var newWidget = $(bancoListPrimero).data('prototype');
    newWidget = newWidget.replace(/__name__/g, totBanco);
    totBanco++;
    $(this).data('cantidad', totBanco);
    var newLi = jQuery('<div class="row"></div>').html(newWidget);
    newLi.appendTo(bancoListPrimero);
    newLi.before(newLi);
});
$('.lista-bancos').on('click', '.remove-banco', function (e) {
    e.preventDefault();
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
  // $('#dias_estadia_cantidad').html(dias_estadia);
  // $('#dias_estadia_cantidad').data('valor', dias_estadia);
  $('#appbundle_marinahumedacotizacion_diasEstadia').val(dias_estadia);


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
  // $('#dias_estadia_cantidad').html(dias_estadia);
  // $('#dias_estadia_cantidad').data('valor', dias_estadia);
  $('#appbundle_marinahumedacotizacion_diasEstadia').val(dias_estadia);

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
$('#appbundle_marinahumedacotizacion_diasEstadia').keyup(function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
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
$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').on('change', function () {
  dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  //de_cantidad = $('#dias_estadia_cantidad').data('valor');
  de_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
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
  //e_cantidad = $('#dias_estadia_cantidad').data('valor');
  e_cantidad = $('#appbundle_marinahumedacotizacion_diasEstadia').val();
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

  // dias_estadia = $('#dias_estadia_cantidad').data('valor');
  dias_estadia = $('#appbundle_marinahumedacotizacion_diasEstadia').val();

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
    var desctot = (subtotal * descuento) / 100;
    var subtotal_descuento = subtotal - desctot;
    var ivatot = subtotal_descuento * iva;
    var total = (subtotal_descuento + ivatot).toFixed(2);

  tdsubtot.html('$ ' + (subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  tdiva.html('$ ' + (ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  tddesc.html('$ ' + (desctot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  tdtot.html('$ ' + total.replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

  tdsubtot.data('valor', subtotal);
  tdiva.data('valor', ivatot);
  tddesc.data('valor', desctot);
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

  fila.children('.valorsubtotal').html('$ ' + (subtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valorsubtotal').data('valor', subtotalAd);

  fila.children('.valoriva').html('$ ' + (ivaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valoriva').data('valor', ivaAd);

  fila.children('.valortotal').html('$ ' + (totalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
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

  $('#gransubtot').html((granSubtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva').html((granIvaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot').html((granTotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

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
var cantidadmhc = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
$('#g_cantidad').data('valor', cantidadmhc);
$('#g_cantidad').html(cantidadmhc);
$('#g_cantidad_mxn').data('valor', cantidadmhc);
$('#g_cantidad_mxn').html(cantidadmhc);

$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').keyup(function () {
  var iva = $('#valiva').data('valor');
  var dolar = $('#appbundle_marinahumedacotizacion_dolar').val();
  var cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
  var precioConIvaMXN = $(this).val();
  var precioConIvaUSD = precioConIvaMXN / dolar;
  var totalConIvaUSD = cantidad * precioConIvaUSD;
  var ivaEquivalente = 100 + iva;
  var totalSinIvaUSD = (100 * totalConIvaUSD) / ivaEquivalente;
  var ivaDelTotalUSD = totalConIvaUSD - totalSinIvaUSD;
  var precioSinIvaUSD = totalSinIvaUSD / cantidad;

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
  $('#g_precio_mxn').html('$ ' + parseFloat($('#g_precio').data('valor') / dolar).toFixed(2));
  gasolinaCalculaSubtotalesMxn();
});

function gasolinaCalculaSubtotales() {
  var iva = $('#valiva').data('valor');
  var cantidad = $('#g_cantidad').data('valor');
  var precio = Number($('#g_precio').text().replace(/\$/g, ''));
  var subtotal = cantidad * precio;
  var ivatot = (subtotal * iva) / 100;
  var total = subtotal + ivatot;

  $('#g_subtotal').html('$ ' + parseFloat(subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#g_iva').html('$ ' + parseFloat(ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#g_total').html('$ ' + parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

  $('#gransubtot_g').html(parseFloat(subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva_g').html(parseFloat(ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot_g').html(parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
}

function gasolinaCalculaSubtotalesMxn() {
  var iva = $('#valiva').data('valor');
  var cantidad = $('#g_cantidad_mxn').data('valor');
  var precio = Number($('#g_precio_mxn').text().replace(/\$/g, ''));
  var subtotal = cantidad * precio;
  var ivatot = (subtotal * iva) / 100;
  var total = subtotal + ivatot;

  $('#g_subtotal_mxn').html('$ ' + parseFloat(subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#g_iva_mxn').html('$ ' + parseFloat(ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#g_total_mxn').html('$ ' + parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

  $('#gransubtot_g_mxn').html(parseFloat(subtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva_g_mxn').html(parseFloat(ivatot).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot_g_mxn').html(parseFloat(total).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
}

//--- fin marina humeda nueva cotización gasolina ---


//--- para astillero nueva cotización ---
//--dolar--
$('#appbundle_astillerocotizacion_dolar').keyup(function () {
  var dolar = $(this).val();
  $('.dolarval').html('$ ' + dolar);
  var precio = 0;
  var auxprecio = 0;
  // precio = $('#grua_precio').data('valor');
  // auxprecio = precio / dolar;
  // $('#grua_precio_mxn').data('valor', auxprecio);
  // $('#grua_precio_mxn').html('$ ' + parseFloat(auxprecio).toFixed(2));
  // calculaSubtotalesAstillero($('#fila_grua_mxn'));
  //
  // precio = $('#suelo_precio').data('valor');
  // auxprecio = precio / dolar;
  // $('#suelo_precio_mxn').data('valor', auxprecio);
  // $('#suelo_precio_mxn').html('$ ' + parseFloat(auxprecio).toFixed(2));
  // calculaSubtotalesAstillero($('#fila_suelo_mxn'));
  //
  // precio = $('#rampa_precio').data('valor');
  // auxprecio = precio / dolar;
  // $('#rampa_precio_mxn').data('valor', auxprecio);
  // $('#rampa_precio_mxn').html('$ ' + parseFloat(auxprecio).toFixed(2));
  // calculaSubtotalesAstillero($('#cotizarampa_mxn'));
  //
  // precio = $('#karcher_precio').data('valor');
  // auxprecio = precio / dolar;
  // $('#karcher_precio_mxn').data('valor', auxprecio);
  // $('#karcher_precio_mxn').html('$ ' + parseFloat(auxprecio).toFixed(2));
  // calculaSubtotalesAstillero($('#cotizakarcher_mxn'));
  //
  // precio = $('#varada_precio').data('valor');
  // auxprecio = precio / dolar;
  // $('#varada_precio_mxn').data('valor', auxprecio);
  // $('#varada_precio_mxn').html('$ ' + parseFloat(auxprecio).toFixed(2));
  // calculaSubtotalesAstillero($('#cotizavarada_mxn'));
    var estadia_precio = $('#appbundle_astillerocotizacion_acservicios_1_precio').val();
    var estadia_precio_mxn = estadia_precio * dolar;
    $('#estadia_precio').data('valor', estadia_precio_mxn);
    $('#estadia_precio').html('$ ' + parseFloat(estadia_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    var fila = $('#fila_estadia');
    calculaSubtotalesAstillero(fila);

  var idfila = 0;
  $("#a_nuevacotizacion tbody .servicio-agregado").each(function () {
    precio = $(this).children('.valorprecio').data('valor');
    auxprecio = (precio / dolar).toFixed(2);
    idfila = $(this).data('id');
    $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').data('valor', auxprecio);
    $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').html('$ ' + parseFloat(auxprecio).toFixed(2));
    calculaSubtotalesAstillero($('#a_nuevacotizacion_mxn>tbody>#' + idfila));
  });
});

// -- fecha llegada --
$('#appbundle_astillerocotizacion_fechaLlegada').on("change", function () {
    var llegada = $(this).val();
    var salida = $('#appbundle_astillerocotizacion_fechaSalida').val();
    var dias = diasEntreFechas(llegada, salida);
    $('#appbundle_astillerocotizacion_diasEstadia').val(dias);
    var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
    $("#estadia_cantidad").data('dias', dias);
    $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($("#fila_estadia"));
    $("#estadia_cantidad_mxn").data('dias', dias);
    $("#estadia_cantidad_mxn").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad_mxn").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($("#fila_estadia_mxn"));

    $("#electricidad_cantidad").data('valor', dias);
    $("#electricidad_cantidad").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad"));

    $("#electricidad_cantidad_mxn").data('valor', dias);
    $("#electricidad_cantidad_mxn").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad_mxn"));
});

// -- fecha salida --
$('#appbundle_astillerocotizacion_fechaSalida').on("change", function () {
    var llegada = $('#appbundle_astillerocotizacion_fechaLlegada').val();
    var salida = $(this).val();
    var dias = diasEntreFechas(llegada, salida);
    $('#appbundle_astillerocotizacion_diasEstadia').val(dias);
    var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
    $("#estadia_cantidad").data('dias', dias);
    $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_estadia'));
    $("#estadia_cantidad_mxn").data('dias', dias);
    $("#estadia_cantidad_mxn").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad_mxn").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_estadia_mxn'));

    $("#electricidad_cantidad").data('valor', dias);
    $("#electricidad_cantidad").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad"));

    $("#electricidad_cantidad_mxn").data('valor', dias);
    $("#electricidad_cantidad_mxn").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad_mxn"));
});

$('#appbundle_astillerocotizacion_diasEstadia').keyup(function () {
    var dias = $(this).val();
    var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
    $("#estadia_cantidad").data('dias', dias);
    $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_estadia'));
    $("#estadia_cantidad_mxn").data('dias', dias);
    $("#estadia_cantidad_mxn").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad_mxn").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_estadia_mxn'));

    $("#electricidad_cantidad").data('valor', dias);
    $("#electricidad_cantidad").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad"));

    $("#electricidad_cantidad_mxn").data('valor', dias);
    $("#electricidad_cantidad_mxn").html(dias);
    calculaSubtotalesAstillero($("#cotizaelectricidad_mxn"));
});

//-- uso de grua (sacar varada y botadura)
$('#appbundle_astillerocotizacion_acservicios_0_estatus').on('click', function () {
    astilleroOcultaMuestraFila(this,$('#fila_grua'),$('#fila_grua_mxn'));
});
//-- estadia
$('#appbundle_astillerocotizacion_acservicios_1_estatus').on('click', function () {
    astilleroOcultaMuestraFila(this,$('#fila_estadia'),$('#fila_estadia_mxn'));
});
//-- Uso de rampa
$('#appbundle_astillerocotizacion_acservicios_2_estatus').on('click', function () {
    astilleroOcultaMuestraFila(this,$('#cotizarampa'),$('#cotizarampa_mxn'));
});
//--Uso de karcher
$('#appbundle_astillerocotizacion_acservicios_3_estatus').on('click', function () {
    astilleroOcultaMuestraFila(this,$('#cotizakarcher'),$('#cotizakarcher_mxn'));
});
// -- Uso de explanada
$('#appbundle_astillerocotizacion_acservicios_4_estatus').on('click',function () {
    astilleroOcultaMuestraFila(this,$('#cotizaexplanada'),$('#cotizaexplanada_mxn'));
});
// -- Conexión a electricidad
$('#appbundle_astillerocotizacion_acservicios_5_estatus').on('click',function () {
    astilleroOcultaMuestraFila(this,$('#cotizaelectricidad'),$('#cotizaelectricidad_mxn'));
});
// -- limpieza de locación
$('#appbundle_astillerocotizacion_acservicios_6_estatus').on('click',function () {
    astilleroOcultaMuestraFila(this,$('#cotizalimpieza'),$('#cotizalimpieza_mxn'));
});
// -- sacar para inspeccionar
$('#appbundle_astillerocotizacion_acservicios_7_estatus').on('click',function () {
    astilleroOcultaMuestraFila(this,$('#cotizainspeccionar'),$('#cotizainspeccionar_mxn'));
});

//-- Uso de grua (sacar varada y botadura) --
$('#appbundle_astillerocotizacion_acservicios_0_precio').keyup(function () {
    var grua_precio = $(this).val();
    $('#grua_precio').html('$ ' + grua_precio);
    $('#grua_precio').data('valor', grua_precio);
    var fila = $('#fila_grua');
    calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var grua_precio_mxn = grua_precio / dolar;
    $('#grua_precio_mxn').html('$ ' + parseFloat(grua_precio_mxn).toFixed(2));
    $('#grua_precio_mxn').data('valor', grua_precio_mxn);

    fila = $('#fila_grua_mxn');
    calculaSubtotalesAstillero(fila);
});
//-- karcher --
$('#appbundle_astillerocotizacion_acservicios_3_cantidad').keyup(function () {
    var karcher_cantidad = $(this).val();
    $('#karcher_cantidad').html(karcher_cantidad);
    $('#karcher_cantidad').data('valor', karcher_cantidad);
    var fila = $('#cotizakarcher');
    calculaSubtotalesAstillero(fila);
    $('#karcher_cantidad_mxn').html(karcher_cantidad);
    $('#karcher_cantidad_mxn').data('valor', karcher_cantidad);
    fila = $('#cotizakarcher_mxn');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_3_precio').keyup(function () {
    var karcher_precio = $(this).val();
    console.log((karcher_precio).replace(',',''));
    $('#karcher_precio').html('$ ' + parseFloat(karcher_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#karcher_precio').data('valor', (karcher_precio).replace(',',''));
    var fila = $('#cotizakarcher');
    calculaSubtotalesAstillero(fila);

    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var karcher_precio_mxn = (karcher_precio).replace(',','') / dolar;
    $('#karcher_precio_mxn').html('$ ' + parseFloat(karcher_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#karcher_precio_mxn').data('valor', karcher_precio_mxn);

    fila = $('#cotizakarcher_mxn');
    calculaSubtotalesAstillero(fila);
});
//-- conexión a electricidad --
$('#appbundle_astillerocotizacion_acservicios_5_precio').keyup(function () {
  var electricidad_precio = $(this).val();
    $('#electricidad_precio').html('$ ' + parseFloat(electricidad_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#electricidad_precio').data('valor', electricidad_precio);
    var fila = $('#cotizaelectricidad');
    calculaSubtotalesAstillero(fila);
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var electricidad_precio_mxn = electricidad_precio / dolar;
    $('#electricidad_precio_mxn').html('$ ' + parseFloat(electricidad_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#electricidad_precio_mxn').data('valor', electricidad_precio_mxn);
    fila = $('#cotizaelectricidad_mxn');
    calculaSubtotalesAstillero(fila);

});

//-- Estadía --
$('#appbundle_astillerocotizacion_acservicios_1_precio').keyup(function () {
    var estadia_precio = $(this).val();
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var estadia_precio_usd = estadia_precio * dolar;
    $('#estadia_precio').data('valor', estadia_precio_usd);
    $('#estadia_precio').html('$ ' + parseFloat(estadia_precio_usd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    var fila = $('#fila_estadia');
    calculaSubtotalesAstillero(fila);
    var estadia_precio_mxn = estadia_precio;
    $('#estadia_precio_mxn').html('$ ' + parseFloat(estadia_precio_mxn).toFixed(2));
    $('#estadia_precio_mxn').data('valor', estadia_precio_mxn);
    fila = $('#fila_estadia_mxn');
    calculaSubtotalesAstillero(fila);

});
//-- limpieza de locación --
$('#appbundle_astillerocotizacion_acservicios_6_cantidad').keyup(function () {
    var limpieza_cantidad = $(this).val();
    $('#limpieza_cantidad').html(limpieza_cantidad);
    $('#limpieza_cantidad').data('valor', limpieza_cantidad);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
    $('#limpieza_cantidad_mxn').html(limpieza_cantidad);
    $('#limpieza_cantidad_mxn').data('valor', limpieza_cantidad);
    fila = $('#cotizalimpieza_mxn');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_6_precio').keyup(function () {
    var limpieza_precio = $(this).val();
    $('#limpieza_precio').html('$ ' + parseFloat(limpieza_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#limpieza_precio').data('valor', limpieza_precio);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var limpieza_precio_mxn = limpieza_precio / dolar;
    $('#limpieza_precio_mxn').html('$ ' + parseFloat(limpieza_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#limpieza_precio_mxn').data('valor', limpieza_precio_mxn);
    fila = $('#cotizalimpieza_mxn');
    calculaSubtotalesAstillero(fila);
});
//-- rampa --
$('#appbundle_astillerocotizacion_acservicios_6_cantidad').keyup(function () {
    var limpieza_cantidad = $(this).val();
    $('#limpieza_cantidad').html(limpieza_cantidad);
    $('#limpieza_cantidad').data('valor', limpieza_cantidad);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
    $('#limpieza_cantidad_mxn').html(limpieza_cantidad);
    $('#limpieza_cantidad_mxn').data('valor', limpieza_cantidad);
    fila = $('#cotizalimpieza_mxn');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_6_precio').keyup(function () {
    var limpieza_precio = $(this).val();
    $('#limpieza_precio').html('$ ' + parseFloat(limpieza_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#limpieza_precio').data('valor', limpieza_precio);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var limpieza_precio_mxn = limpieza_precio / dolar;
    $('#limpieza_precio_mxn').html('$ ' + parseFloat(limpieza_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#limpieza_precio_mxn').data('valor', limpieza_precio_mxn);
    fila = $('#cotizalimpieza_mxn');
    calculaSubtotalesAstillero(fila);
});



$('#appbundle_astillerocotizacion_acservicios_4_precio').keyup(function () {
  var explanada_precio = $(this).val();
  $('#explanada_precio').html('$ ' + parseFloat(explanada_precio).toFixed(2));
  $('#explanada_precio').data('valor', explanada_precio);
  var fila = $('#cotizaexplanada');
  calculaSubtotalesAstillero(fila);

  var dolar = $('#appbundle_astillerocotizacion_dolar').val();
  var explanada_precio_mxn = explanada_precio / dolar;
  $('#explanada_precio_mxn').html('$ ' + parseFloat(explanada_precio_mxn).toFixed(2));
  $('#explanada_precio_mxn').data('valor', explanada_precio_mxn);
  fila = $('#cotizaexplanada_mxn');
  calculaSubtotalesAstillero(fila);
});

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

  var idfila = $(this).parent().parent().parent().data('id');
  var dolar = $('#appbundle_astillerocotizacion_dolar').val();
  var valormxn = $(this).val() / dolar;
  $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').data('valor', valormxn);
  $('#a_nuevacotizacion_mxn>tbody>#' + idfila + '>.valorprecio').html('$ ' + parseFloat(valormxn).toFixed(2));

  fila = $('#a_nuevacotizacion_mxn>tbody>#' + idfila);
  calculaSubtotalesAstillero(fila);
});

function astilleroOcultaMuestraFila (estatus,fila,filamx) {
    if ($(estatus).is(':checked')) {
        fila.removeClass('hidden');
        filamx.removeClass('hidden');
    } else {
        fila.addClass('hidden');
        filamx.addClass('hidden');
    }
    calculaTotalesAstillero();
    calculaTotalesAstilleroMXN();
}

function calculaSubtotalesAstillero(fila) {
  var iva = $('#valorsistemaiva').data('valor');
  var cantidadAd = fila.children('.valorcantidad').data('valor');
  var precioAd = fila.children('.valorprecio').data('valor');
  var subtotalAd = cantidadAd * precioAd;
  var ivaAd = (subtotalAd * iva) / 100;
  var totalAd = subtotalAd + ivaAd;
  fila.children('.valorsubtotal').html('$ ' + (subtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valorsubtotal').data('valor', subtotalAd);

  fila.children('.valoriva').html('$ ' + (ivaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  fila.children('.valoriva').data('valor', ivaAd);

  fila.children('.valortotal').html('$ ' + (totalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
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

  $('#gransubtot').html((granSubtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva').html((granIvaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot').html((granTotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

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

  $('#gransubtot_mxn').html((granSubtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#graniva_mxn').html((granIvaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
  $('#grantot_mxn').html((granTotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

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
    info: 'Mostrando la pagina _PAGE_ de _PAGES_',
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
  columnDefs: [
      {targets: 'no-sort', orderable: false},
      {targets: 'no-show', visible:false, searchable:false}
      ],
  initComplete: function () {
    this.api().columns('.with-choices').every(function () {
      const column = this;
      const columnHeader = column.header();
      const select = document.createElement('select');
      let columnName = columnHeader.innerHTML.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");

      select.add(new Option(columnHeader.innerHTML, ''));
      columnHeader.innerHTML = '';
      columnHeader.appendChild(select);

      select.addEventListener('click', e => e.stopPropagation()
      )
      ;
      select.addEventListener('change', function () {
        let val = $.fn.dataTable.util.escapeRegex(this.value);
        column.search(val, true, true).draw();
      });

      $.ajax({
        url: `${location.href}${columnName}.json`,
        success: function (options) {
          options
              .map(opt => opt.nombre || opt.name
              )
              .filter((item, index, array) => array.indexOf(item || '') === index
              )
              .sort()
              .forEach(optionValue => select.add(new Option(optionValue, optionValue))
              )
          ;
        }
      });

      // LIMITADO A LOS DATOS QUE RECIBE EN EL PRIMER QUERY
      // this.data().unique().sort().map((optionValue) => { select.add(new Option(optionValue, optionValue)) });
    });
  },
};

/*
    MOSTRAR TAB ACTIVA DONDE HAY ERRORES
*/
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