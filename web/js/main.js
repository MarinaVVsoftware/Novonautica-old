var cantidadMotor = document.getElementById('add-another-motor');
var motorCount = cantidadMotor.dataset.cantidad;  //'{{ form.barcos.children[0].motores|length }}';


jQuery('#add-another-motor').click(function (e) {
    e.preventDefault();

    var motorList = jQuery('#motor-fields-list');

    // grab the prototype template
    var newWidget = motorList.attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, motorCount);
    motorCount++;

    // create a new list element and add it to the list
    var newLi = jQuery('<div></div>').html(newWidget);
    newLi.appendTo(motorList);
});
