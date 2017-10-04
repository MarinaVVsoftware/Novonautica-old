$(document).ready(function() {
    $('#loading').hide();
    $('.treeview ul a').on('click',function () {
        $('#loading').show();
    });
    $('ul .only a').on('click',function (){
        $('#loading').show();
    });
    // $('.btn').on('click',function () {
    //     $('#loading').show(); $('#loading').show();
    // });
    $('.barcoespacio').on('click',function(){
        $('#infobarco').html('Barco: '+$(this).attr('id'));
        $('#modalinfobarco').modal('toggle');
    }); 
});

jQuery('.add-another-motor').click(function (e) {
    e.preventDefault();
    // var elementoMotor = document.getElementsByClassName(this);
    var totMotores = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var motorListPrimero = jQuery('#motor-fields-list'+lista);
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
$('.lista-motores').on('click','.remove-motor',function(e) {
    e.preventDefault();
    //console.log('quitar motor');
    $(this).parent().remove();

    return false;
});

jQuery('.add-another-servicio').click(function (e) {
    e.preventDefault();
    // var elementoMotor = document.getElementsByClassName(this);
    var totServicios = $(this).data('cantidad');
    var lista = $(this).data('idlista');
    var servicioListPrimero = jQuery('#servicio-fields-list'+lista);
    //var motorListOtros = jQuery('.lista-motores'+lista);
    // grab the prototype template
    var newWidget = $(servicioListPrimero).data('prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, totServicios);
    totServicios++;
    $(this).data('cantidad', totServicios);
    // create a new list element and add it to the list
    var newLi = jQuery('<tr class="servicio-agregado"></tr>').html(newWidget);
    newLi.appendTo(servicioListPrimero);

    // also add a remove button, just for this example
    //newLi.append('<a href="#" class="remove-motor btn btn-borrar">Quitar Motor</a>');

    newLi.before(newLi);
});
$('.lista-servicios').on('click','.remove-servicio',function(e) {
    e.preventDefault();
    //console.log('quitar motor');
    $(this).parent().parent().remove();

    return false;
});
//--- select dependiente para marina humeda cotización ---
var elcliente = $('#appbundle_marinahumedacotizacion_cliente');
elcliente.change(function() {
    // ... retrieve the corresponding form.
    var form = $(this).closest('form');
    // Simulate form data, but only include the selected elcliente value.
    var data = {};
    data[elcliente.attr('name')] = elcliente.val();
    // Submit data via AJAX to the form's action path.
    $.ajax({
        url : form.attr('action'),
        type: form.attr('method'),
        data : data,
        success: function(html) {
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

//--- select dependiente para astillero cotización ---
var elclienteastillero = $('#appbundle_astillerocotizacion_cliente');
elclienteastillero.change(function() {

    // ... retrieve the corresponding form.
    var form = $(this).closest('form');
    // Simulate form data, but only include the selected elcliente value.
    var data = {};
    data[elclienteastillero.attr('name')] = elclienteastillero.val();
    // Submit data via AJAX to the form's action path.
    console.log(elclienteastillero.val());
    $.ajax({
        url : form.attr('action'),
        type: form.attr('method'),
        data : data,
        success: function(html) {
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

$(document).ready(function() {
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
        format: 'dd-mm-yyyy',
        language: "es",
        orientation: "bottom auto",
    });
});

//--- para marina humeda nueva cotización ---

var de_cantidad = 0;
var da_cantidad = 0;
var precio_dia = 0;
var a_precio = 0;
var a_cantidad = 1;
var e_precio = 0;
var e_cantidad = 1;
var g_cantidad = 0;
var g_precio = 0;
var d_cantidad = 1;
var d_precio = 0;
var l_cantidad = 1;
var l_precio = 0;
var descuento = 0;

//-- Días estadía --
$('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').keyup(function () {
    de_cantidad = $(this).val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    $('#de_cantidad').html(de_cantidad);
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaTotales();
});

//-- Días adicionales --
$('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').keyup(function () {
    da_cantidad = $(this).val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    $('#da_cantidad').html(da_cantidad);
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaTotales();
});

//-- Precio por día --
$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').keyup(function () {
    precio_dia = $(this).val();
    de_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
    da_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').val();
    $('#de_precio').html('$ '+precio_dia);
    $('#da_precio').html('$ '+precio_dia);
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaTotales();

});

//-- Agua --
$('#appbundle_marinahumedacotizacion_mhcservicios_2_precio').keyup(function () {
    a_precio = $(this).val();
    $('#a_precio').html('$ '+a_precio);
    calculaSubtotales(a_cantidad,a_precio,$('#a_subtotal'),$('#a_iva'),$('#a_descuento'),$('#a_total'));
    calculaTotales();
});

//-- Electricidad --
$('#appbundle_marinahumedacotizacion_mhcservicios_3_precio').keyup(function () {
    e_precio = $(this).val();
    $('#e_precio').html('$ '+e_precio);
    calculaSubtotales(e_cantidad,e_precio,$('#e_subtotal'),$('#e_iva'),$('#e_descuento'),$('#e_total'));
    calculaTotales();
});

//-- Gasolina litros --
$('#appbundle_marinahumedacotizacion_mhcservicios_4_cantidad').keyup(function () {
    g_cantidad = $(this).val();
    g_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_4_precio').val();
    $('#g_cantidad').html(g_cantidad);
    calculaSubtotales(g_cantidad,g_precio,$('#g_subtotal'),$('#g_iva'),$('#g_descuento'),$('#g_total'));
    calculaTotales();
});

//-- Gasolina precio --
$('#appbundle_marinahumedacotizacion_mhcservicios_4_precio').keyup(function () {
    g_precio = $(this).val();
    g_cantidad= $('#appbundle_marinahumedacotizacion_mhcservicios_4_cantidad').val();
    $('#g_precio').html('$ '+g_precio);
    calculaSubtotales(g_cantidad,g_precio,$('#g_subtotal'),$('#g_iva'),$('#g_descuento'),$('#g_total'));
    calculaTotales();
});

//-- Dezasolve --
$('#appbundle_marinahumedacotizacion_mhcservicios_5_precio').keyup(function () {
    d_precio = $(this).val();
    $('#d_precio').html('$ '+d_precio);
    calculaSubtotales(d_cantidad,d_precio,$('#d_subtotal'),$('#d_iva'),$('#d_descuento'),$('#d_total'));
    calculaTotales();
});

//-- Limpieza de locación --
$('#appbundle_marinahumedacotizacion_mhcservicios_6_precio').keyup(function () {
    l_precio = $(this).val();
    $('#l_precio').html('$ '+l_precio);
    calculaSubtotales(l_cantidad,l_precio,$('#l_subtotal'),$('#l_iva'),$('#l_descuento'),$('#l_total'));
    calculaTotales();
})

//-- Descuento --
$('#appbundle_marinahumedacotizacion_descuento').keyup(function () {
    descuento = $(this).val();
    de_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
    da_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    a_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_2_precio').val();
    e_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_3_precio').val();
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaSubtotales(a_cantidad,a_precio,$('#a_subtotal'),$('#a_iva'),$('#a_descuento'),$('#a_total'));
    calculaSubtotales(e_cantidad,e_precio,$('#e_subtotal'),$('#e_iva'),$('#e_descuento'),$('#e_total'));

    g_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_4_cantidad').val();
    g_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_4_precio').val();
    calculaSubtotales(g_cantidad,g_precio,$('#g_subtotal'),$('#g_iva'),$('#g_descuento'),$('#g_total'));

    d_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_5_precio').val();
    calculaSubtotales(d_cantidad,d_precio,$('#d_subtotal'),$('#d_iva'),$('#d_descuento'),$('#d_total'));

    l_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_6_precio').val();
    calculaSubtotales(l_cantidad,l_precio,$('#l_subtotal'),$('#l_iva'),$('#l_descuento'),$('#l_total'));

    calculaTotales();
});



function calculaSubtotales(cantidad,precio,tdsubtot,tdiva,tddesc,tdtot){
    var iva = ($('#valiva').data('valor'))/100;
    var descuento = $('#appbundle_marinahumedacotizacion_descuento').val();
    var subtotal = cantidad * precio;
    var ivatot = subtotal * iva;
    var desctot = (subtotal*descuento)/100;
    var total = (subtotal + ivatot - desctot).toFixed(2);

    tdsubtot.html('$ '+(subtotal).toFixed(2));
    tdiva.html('$ '+(ivatot).toFixed(2));
    tddesc.html('$ '+(desctot).toFixed(2));
    tdtot.html('$ '+total);

    tdsubtot.data('valor',subtotal);
    tdiva.data('valor',ivatot);
    tddesc.data('valor',desctot)
    tdtot.data('valor',total);
}

function calculaTotales() {
    var g_subtotal = 0;
    var g_iva = 0;
    var g_descuento = 0;
    var g_total = 0;

    var d_subtotal = 0;
    var d_iva = 0;
    var d_descuento = 0;
    var d_total = 0;

    var l_subtotal = 0;
    var l_iva = 0;
    var l_descuento = 0;
    var l_total = 0;

    if($('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').is(':checked')) {
        g_subtotal = $('#g_subtotal').data('valor');
        g_iva = $('#g_iva').data('valor');
        g_descuento = $('#g_descuento').data('valor');
        g_total = $('#g_total').data('valor');
    }else{
        g_subtotal = 0;
        g_iva = 0;
        g_descuento = 0;
        g_total = 0;
    }

    if($('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').is(':checked')){
        d_subtotal = $('#d_subtotal').data('valor');
        d_iva = $('#d_iva').data('valor');
        d_descuento = $('#d_descuento').data('valor');
        d_total = $('#d_total').data('valor');
    }else{
        d_subtotal = 0;
        d_iva = 0;
        d_descuento = 0;
        d_total = 0;
    }

    if($('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').is(':checked')){
        l_subtotal = $('#l_subtotal').data('valor');
        l_iva = $('#l_iva').data('valor');
        l_descuento = $('#l_descuento').data('valor');
        l_total = $('#l_total').data('valor');
    }else{
        l_subtotal = 0;
        l_iva = 0;
        l_descuento = 0;
        l_total = 0;
    }

    var gransubtotal = (
        parseFloat($('#de_subtotal').data('valor')) +
        parseFloat($('#da_subtotal').data('valor')) +
        parseFloat($('#a_subtotal').data('valor')) +
        parseFloat($('#e_subtotal').data('valor')) +
        parseFloat(g_subtotal) +
        parseFloat(d_subtotal) +
        parseFloat(l_subtotal)
    ).toFixed(2);

    var graniva = (
        parseFloat($('#de_iva').data('valor')) +
        parseFloat($('#da_iva').data('valor')) +
        parseFloat($('#a_iva').data('valor')) +
        parseFloat($('#e_iva').data('valor')) +
        parseFloat(g_iva) +
        parseFloat(d_iva) +
        parseFloat(l_iva)
    ).toFixed(2);

    var grandescuento = (
        parseFloat($('#de_descuento').data('valor')) +
        parseFloat($('#da_descuento').data('valor')) +
        parseFloat($('#a_descuento').data('valor')) +
        parseFloat($('#e_descuento').data('valor')) +
        parseFloat(g_descuento) +
        parseFloat(d_descuento) +
        parseFloat(l_descuento)
    ).toFixed(2);

    var grantotal = (
        parseFloat($('#de_total').data('valor')) +
        parseFloat($('#da_total').data('valor')) +
        parseFloat($('#a_total').data('valor')) +
        parseFloat($('#e_total').data('valor')) +
        parseFloat(g_total) +
        parseFloat(d_total) +
        parseFloat(l_total)
    ).toFixed(2);

    $('#gransubtot').html(gransubtotal);
    $('#graniva').html(graniva);
    $('#grandecuento').html(grandescuento);
    $('#grantot').html(grantotal);




}

$('#appbundle_marinahumedacotizacion_mhcservicios_4_estatus').on('click',function () {
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
});

//-------- fin metodos marina humeda --------

$('.selectclientebuscar').change(function(e) {
    $('#loading').show();
    $("#info-barco").empty();
    $("#info-cliente").empty();
    console.log('buscando cliente');
    $.ajax({
        method: "GET",
        url: "../ajax/buscacliente",
        dataType: 'json',
        data: {'id':$(this).val()},
        success: function(data) {
            if(data.hasOwnProperty("response") && data.response === "success") {
                if(data.hasOwnProperty("posts")) {
                    //http://stackoverflow.com/questions/3710204/how-to-check-if-a-string-is-a-valid-json-string-in-javascript-without-using-try/3710226
                    if (/^[\],:{}\s]*$/.test(data.posts.replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                        var posts = JSON.parse(data.posts);
                        console.log(posts);
                        if(posts.id > 0) {
                            var html = "";
                            html= "<label>Correo electrónico</label>"+
                                "<div class='info-input'>"+posts.correo+"</div>"+
                                "<label>Número de teléfono</label>"+
                                "<div class='info-input'>"+posts.telefono+"</div>"+
                                "<label>Dirección</label>"+
                                "<div class='info-input'>"+posts.direccion+"</div>"+
                                "<label>R.F.C.</label>"+
                                "<div class='info-input'>"+posts.rfc+"</div>"+
                                "<label>Razón social</label>"+
                                "<div class='info-input'>"+posts.razonsocial+"</div>"+
                                "<label>Dirección fiscal</label>"+
                                "<div class='info-input'>"+posts.direccionfiscal+"</div>";
                            $("#info-cliente").append(html);
                        }
                    }
                    else {
                        console.log("INVALID JSON STRING");
                    }
                }
                else {
                    console.log("POSTS NOT FOUND");
                }
            }
            $('#loading').hide();
        },
        error: function(jqXHR, exception) {
            if(jqXHR.status === 405) {
                console.error("METHOD NOT ALLOWED!");
            }
            $('#loading').hide();
        }
    }).fail(function () {
        $('#loading').hide();
        console.log('fallo ajax');
    });
});

var x = 1;
$('.buscabarcomh').click(function () {
    $('#loading').show();
        //console.log('click '+x);
        if(x==2){
            $("#info-barco").empty();
            var idbarco = $( "input[type=radio]:checked" ).val();
            buscaDatosBarco(idbarco);
            x=1;
        }else{
            x++;
        }
});

function buscaDatosBarco(idbarco){
    $.ajax({
        method: "GET",
        url: "../ajax/buscabarco",
        dataType: 'json',
        data: {'id': idbarco},
        success: function(data) {
            if(data.hasOwnProperty("response") && data.response === "success") {
                if(data.hasOwnProperty("posts")) {
                    //http://stackoverflow.com/questions/3710204/how-to-check-if-a-string-is-a-valid-json-string-in-javascript-without-using-try/3710226
                    if (/^[\],:{}\s]*$/.test(data.posts.replace(/\\["\\\/bfnrtu]/g, '@').
                        replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                        replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                        var barcos = JSON.parse(data.posts);
                        console.log(barcos);
                        if(barcos.id > 0) {
                            var html = "";
                            html= "<label>Marca</label>"+
                                "<div class='info-input'>"+barcos.marca+"</div>"+
                                "<label>Modelo</label>"+
                                "<div class='info-input'>"+barcos.modelo+"</div>"+
                                "<label>Eslora</label>"+
                                "<div class='info-input'>"+barcos.eslora+"</div>"+
                                "<label>Manga</label>"+
                                "<div class='info-input'>"+barcos.manga+"</div>"+
                                "<label>Nombre del capitán</label>"+
                                "<div class='info-input'>"+barcos.nombreCapitan+"</div>"+
                                "<label>Teléfono del capitán</label>"+
                                "<div class='info-input'>"+barcos.telefonoCapitan+"</div>"+
                                "<label>Correo del capitán</label>"+
                                "<div class='info-input'>"+barcos.correoCapitan+"</div>";
                            $("#info-barco").append(html);
                        }
                    }
                    else {
                        console.log("INVALID JSON STRING");
                    }
                }
                else {
                    console.log("POSTS NOT FOUND");
                }
            }
            $('#loading').hide();
        },
        error: function(jqXHR, exception) {
            if(jqXHR.status === 405) {
                console.error("METHOD NOT ALLOWED!");
            }
            $('#loading').hide();
        }
    }).fail(function () {
        $('#loading').hide();
        console.log('fallo ajax');
    });
}

