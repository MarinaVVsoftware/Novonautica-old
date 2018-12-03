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

//collectio al agregar servicios en cotización astillero
jQuery('.add-another-servicio').click(function (e) {
  e.preventDefault();
  var totServicios = $('#serviciosextra').data('cantidad');
  var servicioListPrimero = jQuery('#otros');
  var newWidget = $('#serviciosextra').data('prototype');
  newWidget = newWidget.replace(/__name__/g, totServicios);
  newWidget = newWidget.replace('td-producto', 'hide');
  newWidget = newWidget.replace('td-servicio', 'hide');
  newWidget = newWidget.replace('td-libre', 'hide');
  totServicios++;
  $('#serviciosextra').data('cantidad', totServicios);
  var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);
  newLi.children('.valorcantidad').children('input').prop('required',true);
  newLi.children('.td-otroservicio').children('input').prop('required',true);
  newLi.children('.valorprecio').children('.input-group').children('input').prop('required',true);
  newLi.appendTo(servicioListPrimero);
  newLi.before(newLi);
});

$('#serviciosextra').on('click', '.remove-servicio', function (e) {
  e.preventDefault();
  // Descomentar si se requiere que se borre el servicio con los productos asociados
  var fila = $(this).parent().parent();
  var idservicio = fila.children('.valorgrupo').children('input').val();
  if(idservicio > 0){ //si se borra un servicio con productos asociados
      $.each($('#productos tr'), function (i,filaproducto) {
          // si pertenecen al mismo kit los productos con los servicios
          if($(filaproducto).children('.valorgrupo').children('input').val() === idservicio){
              $(filaproducto).remove();
          }
      });
      let servicios = document.getElementById('serviciosextra').querySelectorAll('tr');
      servicios.forEach(fila =>{
          if(fila.querySelector('.valorgrupo input').value === idservicio){ fila.remove(); }
      });
  }
  $(this).parent().parent().remove();
  calculaDiasEstadiaAstillero();
  //calculaTotalesAstillero();
  return false;
});
$('#otros').on('click', '.remove-servicio', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    calculaTotalesAstillero();
    return false;
});
$('#productos').on('click', '.remove-servicio', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    calculaTotalesAstillero();
    return false;
});
$('.lista-servicios').on('click', '.elimina-producto', function (e) {
    e.preventDefault();
    // var fila = $(this).parent().parent();
    // var idservicio = fila.children('.valorgrupo').children('input').val();
    // if(idservicio > 0){ //si se borra un servicio con productos asociados
    //     $.each($('#productos tr'), function (i,filaproducto) {
    //         // si pertenecen al mismo kit los productos con los servicios
    //         if($(filaproducto).children('.valorgrupo').children('input').val() === idservicio){
    //             $(filaproducto).remove();
    //         }
    //     });
    //     let servicios = document.getElementById('serviciosextra').querySelectorAll('tr');
    //     servicios.forEach(fila =>{
    //         if(fila.querySelector('.valorgrupo input').value === idservicio){ fila.remove(); }
    // });
    // }
    $(this).parent().parent().remove();
    calculaTotalesAstillero();
    return false;
});
//---- aparecer form collection con select de productos ----
$('.add-producto').click(function (e){
    e.preventDefault();
    astilleroAgregaProducto(0,0);
});

function astilleroAgregaProducto(grupoProducto,idservicio){
    var totServicios = $('#serviciosextra').data('cantidad');
    var servicioListPrimero = jQuery('#productos');
    var newWidget = $('#serviciosextra').data('prototype');

    newWidget = newWidget.replace(/__name__/g, totServicios);
    newWidget = newWidget.replace('td-otroservicio', 'hide');
    newWidget = newWidget.replace('td-servicio', 'hide');
    newWidget = newWidget.replace('td-libre', 'hide');
    newWidget = newWidget.replace('input-group', 'hide');

    totServicios++;

    $('#serviciosextra').data('cantidad', totServicios);

    var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);

    newLi.appendTo(servicioListPrimero);
    newLi.before(newLi);

    var fila = $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_producto').parent().parent();

    fila.children('.valorpromedio').removeClass('hide'); //hacer visible columna de valor promedio solo para productos en caso de que pertenezcan a un servicio

    if (grupoProducto === 0) {
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').val(1);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parent().data('valor', 1);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_producto').val('');
    } else {
        var productosCantidad = 0;
        var eslora = typeof($('#eslora').data('valor')) === 'undefined' ? 0 : $('#eslora').data('valor');

        if (grupoProducto.tipoCantidad) {
            productosCantidad = Math.round(eslora * grupoProducto.cantidad);
        } else {
            productosCantidad = grupoProducto.cantidad;
        }
        //fila.data('servicio-pertenece',idservicio);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').val(productosCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parent().data('valor', productosCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_producto').val(grupoProducto.producto.id);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_tipoCantidad').val(grupoProducto.tipoCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_promedio').val(grupoProducto.cantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_grupo').val(idservicio);
        fila.children('.valorprecio').html('$ ' + parseFloat((grupoProducto.producto.precio) / 100).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' <small>MXN</small>');
        fila.children('.valorprecio').data('valor', ((grupoProducto.producto.precio) / 100));
        fila.children('.valorpromedio').append(grupoProducto.cantidad);
        // document.getElementById('appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parentNode.dataset.tipo = grupoProducto.tipoCantidad;
        // document.getElementById('appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parentNode.dataset.promedio = grupoProducto.cantidad;
        calculaSubtotalesAstillero(fila);
    }
}
function calculaProductosPorServicio(){
    var eslora = typeof($('#eslora').data('valor')) === 'undefined' ? 0 : $('#eslora').data('valor');
    var productos = 0;
    $.each($('#productos').children(), function (i, fila) {
        var celdaCantidad = $(fila).children('.valorcantidad');
        var celdaTipo = $(fila).children('.valortipo').children('input').val();
        var celdaPromedio = $(fila).children('.valorpromedio').children('input').val();
        if(Number(celdaTipo) === 1){
            productos = Math.round(eslora * Number(celdaPromedio));
            celdaCantidad.children('input').val(productos);
            // celdaCantidad.dataset.valor = productos;
            $(fila).children('.valorcantidad').data('valor',productos);
            calculaSubtotalesAstillero($(fila));
        }
    });
}
function astilleroBuscaProducto(idproducto,fila){
    // var url = `${location.protocol + '//' + location.host}/astillero/producto/buscarproducto/${idproducto}`;
    var baseurl = location.href.endsWith('/') ? location.href : location.href + '/';
    var url = baseurl + '../producto/buscarproducto/' + idproducto;

    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        success: function(datos) {
            fila.children('.valorprecio').html(
                '$ '+parseFloat((datos.precio)/100).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>'
            );
            fila.children('.valorprecio').data('valor',((datos.precio)/100));
            calculaSubtotalesAstillero(fila);
        }
    });
}
function recalculaPreciosOtros(){
    let otros = document.getElementById('otros').querySelectorAll('tr');
    otros.forEach(fila => { calculaSubtotalesAdicionales($(fila)) });
}
function recalculaPreciosProductos(){
    let productos = document.getElementById('productos').querySelectorAll('tr');
    productos.forEach(fila =>{astilleroBuscaProducto(fila.querySelector('.select-busca-producto').value,$(fila))});
}
function recalculaPreciosServicios(){
    let servicios = document.getElementById('serviciosextra').querySelectorAll('tr');
    let botonera = document.getElementById('botonera-servicios');
    servicios.forEach(fila => {
        agregaPrecioServiciosExtra($(fila),fila.querySelector('.select-busca-servicio').value,$(botonera.querySelector("[data-id='"+fila.querySelector('.select-busca-servicio').value+"']")))
        //console.log(botonera.querySelector("[data-id='"+fila.querySelector('.select-busca-servicio').value+"']"))
    });
    // calculaSubtotalesAstillero($(fila))
    //agregaPrecioServiciosExtra(fila,idservicio,elemento)
}
//---- aparecer form collection con select de servicios ----
$('.add-servicio').click(function (e) {
    e.preventDefault();
    var totServicios = $('#serviciosextra').data('cantidad');
    var servicioListPrimero = jQuery('#serviciosextra');
    var newWidget = $('#serviciosextra').data('prototype');
    newWidget = newWidget.replace(/__name__/g, totServicios);
    newWidget = newWidget.replace('td-otroservicio', 'hide');
    newWidget = newWidget.replace('td-producto', 'hide');
    newWidget = newWidget.replace('td-servicio', 'hide');
    newWidget = newWidget.replace('input-group', 'hide');
    newWidget = newWidget.replace('valorpromedio hide', 'valorpromedio');
    totServicios++;
    $('#serviciosextra').data('cantidad', totServicios);
    var newLi = jQuery('<tr class="servicio-agregado" data-id="' + (totServicios - 1) + '"></tr>').html(newWidget);
    newLi.appendTo(servicioListPrimero);
    newLi.before(newLi);
    let cantidad = 1;
    if($(this).data('tipo_cantidad') === 1){
        cantidad = typeof($('#eslora').data('valor')) === 'undefined' ? 1 : $('#eslora').data('valor');
    }
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').val(cantidad);
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parent().data('valor', cantidad);
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_servicio').val($(this).data('id'));
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_tipoCantidad').val($(this).data('tipo_cantidad'));
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_promedio').val($(this).data('dias_descuento'));
    $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_servicio').parent().parent().children('.valorpromedio').append($(this).data('dias_descuento'));
    //------------- descuenta dias estadia --------------------
    // let diasEstadia = Number($('#appbundle_astillerocotizacion_diasEstadia').val()) - Number($(this).data('dias_descuento'));
    // $('#appbundle_astillerocotizacion_diasEstadia').val(diasEstadia);
    calculaDiasEstadiaAstillero();
    //--------- fin descuenta dias estadia --------------------

    var fila = $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_servicio').parent().parent();
    var idservicio = $(this).data('id');
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var precio = 0;
    $(fila).children('.valorgrupo').children('input').val(idservicio);
    $(fila.children('.td-libre')).html($(this).data('nombre'));
    $(fila.children('.valorprecio')).html('$'+($(this).data('precio')/100).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' '+$(this).data('divisa'));
    if($(this).data('divisa')==='USD'){
        precio = (($(this).data('precio') * dolar)/100).toFixed(2);
    }else{
        precio = ($(this).data('precio')/100).toFixed(2);
    }
    var precioreal = ($(this).data('precio')/100).toFixed(2);
    $(fila.children('.valorprecio')).data('valor',precio);
    $(fila.children('.valorprecio')).data('valorreal',precioreal);
    $(fila.children('.valorprecio')).data('divisa',$(this).data('divisa'));
    calculaSubtotalesAstillero(fila);

    // let url = `${location.protocol + '//' + location.host}/astillero/servicio/buscarservicio/${idservicio}`;
    var baseurl = location.href.endsWith('/') ? location.href : location.href + '/';
    var url = baseurl + '../servicio/buscarservicio/' + idservicio;

    $.ajax({
        method: "GET",
        url,
        dataType: 'json',
        success: function({gruposProductos}) {
            gruposProductos.forEach(grupo => astilleroAgregaProducto(grupo, idservicio))
        }
    });
});
function agregaPrecioServiciosExtra(fila,idservicio,boton){
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var precio = 0;
    $(fila).children('.valorgrupo').children('input').val(idservicio);
    $(fila.children('.td-libre')).html(boton.data('nombre'));
    $(fila.children('.valorprecio')).html('$'+(boton.data('precio')/100).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' '+boton.data('divisa'));
    if(boton.data('divisa')==='USD'){
        precio = ((boton.data('precio') * dolar)/100).toFixed(2);
    }else{
        precio = (boton.data('precio')/100).toFixed(2);
    }
    var precioreal = (boton.data('precio')/100).toFixed(2);
    $(fila.children('.valorprecio')).data('valor',precio);
    $(fila.children('.valorprecio')).data('valorreal',precioreal);
    $(fila.children('.valorprecio')).data('divisa',boton.data('divisa'));
    calculaSubtotalesAstillero(fila);
}

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
$('.lista-pagos').on('click', '.remove-pago', function (e) {
  e.preventDefault();
  $(this).parent().parent().remove();
  return false;
});

//---------- colection al agregar contratista a ODT -----------------
jQuery('.add-another-proveedor').click(function (e) {
    coleccionContratistaODT(e, this,'',0,0,1,0);
});
$('.lista-proveedores').on('click', '.remove-proveedor', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    return false;
});
function coleccionContratistaODT(e,objeto,descripcion,cantidad,total,tipoelemento,idproveedor, element){
    e.preventDefault();
    var totProveedor = $(objeto).data('cantidad');
    var lista = $(objeto).data('idlista');
    var proveedorListPrimero = jQuery('#proveedor-fields-list' + lista);
    var newWidget = $(proveedorListPrimero).data('prototype');
    newWidget = newWidget.replace(/__name__/g, totProveedor);
    totProveedor++;
    $(objeto).data('cantidad', totProveedor);
    var newLi = jQuery('<div class="row"></div>').html(newWidget);
    newLi.appendTo(proveedorListPrimero);
    $('#appbundle_ordendetrabajo_contratistas_'+(totProveedor-1)+'_cotizacionInicial').val(descripcion);
    $('#appbundle_ordendetrabajo_contratistas_'+(totProveedor-1)+'_cantidad').val(cantidad);
    $('#appbundle_ordendetrabajo_contratistas_'+(totProveedor-1)+'_preciovv').val((total/100).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));

    const proveedorSelect = newLi[0].querySelector('select');
    const proveedorLabel = proveedorSelect.parentNode.children[0];

    if (element.producto) {
      proveedorLabel.innerText = 'Proveedor';
      newLi[0].children[1].children[0].children[1].value = element.producto.id;
    }

    for (let childOption of proveedorSelect.children) {
      if (tipoelemento === 1 && childOption.dataset.trabajador === '0') {
        childOption.classList.add('hidden');
      }

      if (childOption.value && element.producto) {
        childOption.style.display = 'none';

        for (let proveedor of element.producto.proveedores) {
          if (childOption.innerText === proveedor.nombre) {
            childOption.style.display = 'block';
          }
        }

      }
    }

    newLi.before(newLi);

    return newLi[0];
}
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

//----- colección agregar elementos (uso general) ------
jQuery('.agrega-elemento').click(function (e) {
    e.preventDefault();
    var totElementos = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var listadoElementos = jQuery('#listado'+lista);
    var nuevoElemento = $(listadoElementos).data('prototype');
    nuevoElemento = nuevoElemento.replace(/__name__/g, totElementos);
    totElementos++;
    $(this).data('cantidad',totElementos);
    var nuevaLinea = jQuery('<div class="row"></div>').html(nuevoElemento);
    nuevaLinea.appendTo(listadoElementos);
    nuevaLinea.before(nuevaLinea);
    $('.select-buscador').select2();
});
$('.lista-elementos').on('click', '.elimina-elemento', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    return false;
});
//------ fin colección elementos (uso general) -------

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
function totalDiasLaborales(start, end) {
    // This makes no effort to account for holidays
    // Counts end day, does not count start day

    // make copies we can normalize without changing passed in objects
    var start = new Date(start);
    var end = new Date(end);
    start.setDate(start.getDate() + 1);
    end.setDate(end.getDate() + 1);
    // initial total
    var totalBusinessDays = 0;

    // normalize both start and end to beginning of the day
    start.setHours(0,0,0,0);
    end.setHours(0,0,0,0);

    var current = new Date(start);
    current.setDate(current.getDate() + 1);
    var day;
    // loop through each day, checking
    while (current <= end) {
        day = current.getDay();
        if (day >= 1 && day <= 6) {
            ++totalBusinessDays;
        }
        current.setDate(current.getDate() + 1);
    }
    return totalBusinessDays;
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
  var fila = $(this).parent().parent();
  calculaSubtotalesAdicionales(fila);
});
$('#appbundle_marinahumedacotizacionadicional_iva').on('keyup', function () {
    document.querySelectorAll('.servicio-agregado').forEach(fila =>{calculaSubtotalesAdicionales($(fila))});
});
function calculaSubtotalesAdicionales(fila) {
  var iva = document.getElementById('appbundle_marinahumedacotizacionadicional_iva').value;
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
//---- fin marina humeda servicio adicional -----------


//--- para astillero nueva cotización ---
//--dolar--
$('#appbundle_astillerocotizacion_dolar').keyup(function () {
  var dolar = $(this).val();
  $('.dolarval').html('$ ' + dolar);
  var precio = 0;
  var auxprecio = 0;

    var estadia_precio = $('#appbundle_astillerocotizacion_acservicios_1_precio').val();
    var estadia_precio_mxn = estadia_precio * dolar;
    $('#estadia_precio').data('valor', estadia_precio_mxn);
    //$('#estadia_precio').html('$ ' + parseFloat(estadia_precio_mxn).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    var fila = $('#fila_estadia');
    calculaSubtotalesAstillero(fila);

    var dias_adicionales = $('#appbundle_astillerocotizacion_acservicios_8_precio').val();
    var dias_adicionales_mxn = dias_adicionales * dolar;
    $('#dia_adicional_precio').data('valor', dias_adicionales_mxn);
    var fila = $('#fila_dia_adicional');
    calculaSubtotalesAstillero(fila);

    $('#serviciosextra .servicio-agregado').each(function () {
        divisa = $(this).children('.valorprecio').data('divisa');
        precio = $(this).children('.valorprecio').data('valorreal');
        if (divisa === 'USD'){
            preciomxn = precio * dolar;
            $(this).children('.valorprecio').data('valor',(preciomxn).toFixed(2));
        }
        calculaSubtotalesAstillero($(this));

    });
});

// -- fecha llegada --
$('#appbundle_astillerocotizacion_fechaLlegada').on("change", function () {
    calculaDiasEstadiaAstillero();
});

// -- fecha salida --
$('#appbundle_astillerocotizacion_fechaSalida').on("change", function () {
    calculaDiasEstadiaAstillero();
});
 function calculaDiasEstadiaAstillero(){
     var llegada = $('#appbundle_astillerocotizacion_fechaLlegada').val();
     var salida = $('#appbundle_astillerocotizacion_fechaSalida').val();
     var diasLaborales = totalDiasLaborales(llegada, salida);
     var diasDescuento = 0;
     document.getElementById('serviciosextra').querySelectorAll('tr').forEach(
         servicio => diasDescuento+=Number($(servicio).children('.valorpromedio').children('input').val()));
     var dias = diasLaborales - diasDescuento;
     $('#appbundle_astillerocotizacion_diasEstadia').val(dias);
     var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
     $("#estadia_cantidad").data('dias', dias);
     $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
     $("#estadia_cantidad").html(dias + ' (pie por día)');
     calculaSubtotalesAstillero($('#fila_estadia'));
 }
$('#appbundle_astillerocotizacion_diasEstadia').keyup(function () {
    var dias = $(this).val();
    var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
    $("#estadia_cantidad").data('dias', dias);
    $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
    $("#estadia_cantidad").html(dias + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_estadia'));
    //calculaDiasEstadiaAstillero();
});
$('#appbundle_astillerocotizacion_descuento').keyup(function (){
    calculaTotalesAstillero();
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
//-- dia adicional
$('#appbundle_astillerocotizacion_acservicios_8_estatus').on('click', function () {
    astilleroOcultaMuestraFila(this,$('#fila_dia_adicional'),$('#fila_dia_adicional_mxn'));
});
//-- Uso de grua (sacar varada y botadura) --
$('#appbundle_astillerocotizacion_acservicios_0_precio').keyup(function () {
    var grua_precio = $(this).val().replace(',','');
    $('#grua_precio').html('$ ' +  parseFloat(grua_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#grua_precio').data('valor', grua_precio);
    var fila = $('#fila_grua');
    calculaSubtotalesAstillero(fila);
});
//-- karcher --
$('#appbundle_astillerocotizacion_acservicios_3_cantidad').keyup(function () {
    var karcher_cantidad = $(this).val();
    $('#karcher_cantidad').html(karcher_cantidad);
    $('#karcher_cantidad').data('valor', karcher_cantidad);
    var fila = $('#cotizakarcher');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_3_precio').keyup(function () {
    var karcher_precio = $(this).val().replace(',','');
    $('#karcher_precio').html('$ ' + parseFloat(karcher_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#karcher_precio').data('valor', karcher_precio);
    var fila = $('#cotizakarcher');
    calculaSubtotalesAstillero(fila);
});
//-- conexión a electricidad --
$('#appbundle_astillerocotizacion_acservicios_5_cantidad').keyup(function () {
    var electricidad_cantidad = $(this).val();
    $('#electricidad_cantidad').html(electricidad_cantidad);
    $('#electricidad_cantidad').data('valor', electricidad_cantidad);
    var fila = $('#cotizaelectricidad');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_5_precio').keyup(function () {
  var electricidad_precio = $(this).val().replace(',','');
    $('#electricidad_precio').html('$ ' + parseFloat(electricidad_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#electricidad_precio').data('valor', electricidad_precio);
    var fila = $('#cotizaelectricidad');
    calculaSubtotalesAstillero(fila);

});

//-- Estadía --
$('#appbundle_astillerocotizacion_acservicios_1_precio').keyup(function () {
    var estadia_precio = $(this).val().replace(',','');
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var estadia_precio_usd = estadia_precio * dolar;
    $('#estadia_precio').data('valor', estadia_precio_usd.toFixed(2));
    $('#estadia_precio').html('$ ' + parseFloat(estadia_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>USD</small>');
    var fila = $('#fila_estadia');
    calculaSubtotalesAstillero(fila);
});
//-- limpieza de locación --
$('#appbundle_astillerocotizacion_acservicios_6_cantidad').keyup(function () {
    var limpieza_cantidad = $(this).val();
    $('#limpieza_cantidad').html(limpieza_cantidad);
    $('#limpieza_cantidad').data('valor', limpieza_cantidad);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_6_precio').keyup(function () {
    var limpieza_precio = $(this).val().replace(',','');
    $('#limpieza_precio').html('$ ' + parseFloat(limpieza_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#limpieza_precio').data('valor', limpieza_precio);
    var fila = $('#cotizalimpieza');
    calculaSubtotalesAstillero(fila);
});
//-- rampa --
$('#appbundle_astillerocotizacion_acservicios_2_cantidad').keyup(function () {
    var rampa_cantidad = $(this).val();
    $('#rampa_cantidad').html(rampa_cantidad);
    $('#rampa_cantidad').data('valor', rampa_cantidad);
    var fila = $('#cotizarampa');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_2_precio').keyup(function () {
    var rampa_precio = $(this).val().replace(',','');
    $('#rampa_precio').html('$ ' + parseFloat(rampa_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#rampa_precio').data('valor', rampa_precio);
    var fila = $('#cotizarampa');
    calculaSubtotalesAstillero(fila);
});
//-- uso de explanada
$('#appbundle_astillerocotizacion_acservicios_4_cantidad').keyup(function () {
    var explanada_cantidad = $(this).val();
    $('#explanada_cantidad').html(explanada_cantidad);
    $('#explanada_cantidad').data('valor', explanada_cantidad);
    var fila = $('#cotizaexplanada');
    calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_4_precio').keyup(function () {
  var explanada_precio = $(this).val().replace(',','');
  $('#explanada_precio').html('$ ' + parseFloat(explanada_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  $('#explanada_precio').data('valor', explanada_precio);
  var fila = $('#cotizaexplanada');
  calculaSubtotalesAstillero(fila);
});
$('#appbundle_astillerocotizacion_acservicios_7_precio').keyup(function () {
    var inspeccionar_precio = $(this).val().replace(',','');
    $('#inspeccionar_precio').html('$ ' + parseFloat(inspeccionar_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
    $('#inspeccionar_precio').data('valor', inspeccionar_precio);
    var fila = $('#cotizainspeccionar');
    calculaSubtotalesAstillero(fila);
});
//-- dias adicionales
function astilleroDiasAdicionalesCantidad(dias_adicionales){
    var nuevo_dias_adicionales_cantidad = dias_adicionales * $("#dia_adicional_cantidad").data('eslora');
    $("#dia_adicional_cantidad").data('dias', dias_adicionales);
    $("#dia_adicional_cantidad").data('valor', nuevo_dias_adicionales_cantidad);
    $("#dia_adicional_cantidad").html(dias_adicionales + ' (pie por día)');
    calculaSubtotalesAstillero($('#fila_dia_adicional'));
    $("#electricidad_cantidad").data('valor', dias_adicionales);
    $("#electricidad_cantidad").html(dias_adicionales);
    calculaSubtotalesAstillero($("#cotizaelectricidad"));
}
$('#appbundle_astillerocotizacion_acservicios_8_cantidad').keyup(function () {
    astilleroDiasAdicionalesCantidad($(this).val());
});
$('#appbundle_astillerocotizacion_acservicios_8_precio').keyup(function () {
    var dias_adicionales_precio = $(this).val();
    var dolar = $('#appbundle_astillerocotizacion_dolar').val();
    var dias_adicionales_precio_mxn = dias_adicionales_precio * dolar;
    $("#dia_adicional_precio").data('valor',dias_adicionales_precio_mxn);
    $("#dia_adicional_precio").html(dias_adicionales_precio+' <small>USD</small>');
    calculaSubtotalesAstillero($('#fila_dia_adicional'));
});


$('.tabla-astillero').on('keyup', 'input', function () {
    var clasecelda = $(this).parent().attr('class');
    if(clasecelda === 'input-group'){
        $(this).parent().parent().data('valor', $(this).val());
        fila = $(this).parent().parent().parent();
    }else{
        $(this).parent().data('valor', $(this).val());
        fila = $(this).parent().parent();
    }
    calculaSubtotalesAstillero(fila);
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
}

function calculaSubtotalesAstillero(fila) {
  var iva = $('#valorsistemaiva').data('valor');
  var cantidadAd = fila.children('.valorcantidad').data('valor');
  var precioAd = fila.children('.valorprecio').data('valor');
  var subtotalAd = cantidadAd * precioAd;
  var ivaAd = (subtotalAd * iva) / 100;
  var totalAd = subtotalAd + ivaAd;
  fila.children('.valorsubtotal').html('$ ' + (subtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  fila.children('.valorsubtotal').data('valor', subtotalAd);

  fila.children('.valoriva').html('$ ' + (ivaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  fila.children('.valoriva').data('valor', ivaAd);

  fila.children('.valortotal').html('$ ' + (totalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  fila.children('.valortotal').data('valor', totalAd);
  calculaTotalesAstillero();
}

function calculaTotalesAstillero() {
    var iva = Number($('#valorsistemaiva').data('valor'));
    var granSubtotalAd = 0;
    var granIvaAd = 0;
    var granTotalAd = 0;
    var valorSubtotal = 0;
    var valorIva = 0;
    var valorTotal = 0;
    var descuentoPorcentaje = Number($('#appbundle_astillerocotizacion_descuento').val());
    var granDescuentoAd = 0;
    $("table tbody tr").each(function () {
        if (!$(this).hasClass('hidden')) {
            valorSubtotal = $(this).children('.valorsubtotal').data('valor');
            valorIva = $(this).children('.valoriva').data('valor');
            valorTotal = $(this).children('.valortotal').data('valor');
            if (typeof valorSubtotal === "undefined") {
                valorSubtotal = 0
            }
            if (typeof valorIva === "undefined") {
                valorIva = 0
            }
            if (typeof valorTotal === "undefined") {
                valorTotal = 0
            }
            granSubtotalAd += valorSubtotal;
            granIvaAd += valorIva;
            granTotalAd += valorTotal;
        }
    });
    granDescuentoAd = (granSubtotalAd * descuentoPorcentaje)/100;
    granIvaAd = ((granSubtotalAd - granDescuentoAd) * iva)/100;
    granTotalAd = granSubtotalAd - granDescuentoAd + granIvaAd;
    $('#gransubtot').html((granSubtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#grandescuento').html((granDescuentoAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#graniva').html((granIvaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#grantot').html((granTotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
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

/*
  ALERTAS PARA INVENTARIO
 */
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

