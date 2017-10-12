
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
var descuento = 0;

$('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').keyup(function () {
    de_cantidad = $(this).val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    $('#de_cantidad').html(de_cantidad);
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').keyup(function () {
    da_cantidad = $(this).val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    $('#da_cantidad').html(da_cantidad);
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').keyup(function () {
    precio_dia = $(this).val();
    de_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
    da_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').val();
    $('#de_precio').html(precio_dia);
    $('#da_precio').html(precio_dia);
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaTotales();

});

$('#appbundle_marinahumedacotizacion_mhcservicios_3_precio').keyup(function () {
    a_precio = $(this).val();
    $('#a_precio').html(a_precio);
    calculaSubtotales(a_cantidad,a_precio,$('#a_subtotal'),$('#a_iva'),$('#a_descuento'),$('#a_total'));
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_mhcservicios_4_precio').keyup(function () {
    e_precio = $(this).val();
    $('#e_precio').html(e_precio);
    calculaSubtotales(e_cantidad,e_precio,$('#e_subtotal'),$('#e_iva'),$('#e_descuento'),$('#e_total'));
    calculaTotales();
});

$('#appbundle_marinahumedacotizacion_descuento').keyup(function () {
    descuento = $(this).val();
    de_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_0_cantidad').val();
    da_cantidad = $('#appbundle_marinahumedacotizacion_mhcservicios_1_cantidad').val();
    precio_dia = $('#appbundle_marinahumedacotizacion_mhcservicios_0_precio').val();
    a_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_3_precio').val();
    e_precio = $('#appbundle_marinahumedacotizacion_mhcservicios_4_precio').val();
    calculaSubtotales(de_cantidad,precio_dia,$('#de_subtotal'),$('#de_iva'),$('#de_descuento'),$('#de_total'));
    calculaSubtotales(da_cantidad,precio_dia,$('#da_subtotal'),$('#da_iva'),$('#da_descuento'),$('#da_total'));
    calculaSubtotales(a_cantidad,a_precio,$('#a_subtotal'),$('#a_iva'),$('#a_descuento'),$('#a_total'));
    calculaSubtotales(e_cantidad,e_precio,$('#e_subtotal'),$('#e_iva'),$('#e_descuento'),$('#e_total'));
    calculaTotales();
});


function calculaSubtotales(cantidad,precio,tdsubtot,tdiva,tddesc,tdtot){
    var iva = 0.16
    var descuento = $('#appbundle_marinahumedacotizacion_descuento').val();
    var subtotal = cantidad * precio;
    var ivatot = subtotal * iva;
    var desctot = (subtotal*descuento)/100;
    var total = (subtotal + ivatot - desctot).toFixed(2);
    tdsubtot.html((subtotal).toFixed(2));
    tdiva.html((ivatot).toFixed(2));
    tddesc.html((desctot).toFixed(2));
    tdtot.html(total);
    tdtot.data('valor',total);
}

function calculaTotales() {
    //console.log($('#de_total').data('valor'));
    //console.log($('#da_total').data('valor'));
    var grantotal = (parseFloat($('#de_total').data('valor')) +
        parseFloat($('#da_total').data('valor')) +
        parseFloat($('#a_total').data('valor'))
    ).toFixed(2);
    console.log('total '+grantotal);
    $('#grantot').html(grantotal);
}


// $('#appbundle_marinahumedacotizacion_mhcservicios_2_estatus').on('click',function(){
//    console.log('hola');
// });
//
// $('#appbundle_marinahumedacotizacion_mhcservicios_5_estatus').on('click',function () {
//
// });
//
// $('#appbundle_marinahumedacotizacion_mhcservicios_6_estatus').on('click',function () {
//
// });