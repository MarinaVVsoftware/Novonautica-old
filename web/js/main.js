
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
$('.remove-motor').click(function(e) {
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