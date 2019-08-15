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
        
        let surplusPercentage = grupoProducto.porcentajeExcedente;
        if(surplusPercentage) {
          // Realizar la formula de cantidades aqui.
          let quantity = (surplusPercentage * productosCantidad)/100;
          console.log(quantity);
        }
        //fila.data('servicio-pertenece',idservicio);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').val(productosCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parent().data('valor', productosCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_producto').val(grupoProducto.producto.id);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_tipoCantidad').val(grupoProducto.tipoCantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_promedio').val(grupoProducto.cantidad);
        $('#appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_grupo').val(idservicio);
        
        /** Fix sobre división entre 100 de los precios.
         *  01/08/2019 Eduardo Hidalgo
         */
        fila.children('.valorprecio').html('$ ' + parseFloat(grupoProducto.producto.precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' <small>MXN</small>');
        fila.children('.valorprecio').data('valor', grupoProducto.producto.precio);
        fila.children('.valorpromedio').append(grupoProducto.cantidad);
        // document.getElementById('appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parentNode.dataset.tipo = grupoProducto.tipoCantidad;
        // document.getElementById('appbundle_astillerocotizacion_acservicios_' + (totServicios - 1) + '_cantidad').parentNode.dataset.promedio = grupoProducto.cantidad;
        calculaSubtotalesAstillero(fila);
    }
}
function calculaProductosPorServicio(){
  console.log("calculaProductosPorServicio");
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

/** función del módulo: ASTILLERO
 * Sucede cuando se busca un producto de forma individual en una cotización de astillero 
 */
function astilleroBuscaProducto(idproducto, fila) {
  /* Crea la URL del endpoint */
  var baseurl = location.href.endsWith('/') ? location.href : location.href + '/';
  var url = baseurl + '../producto/buscarproducto/' + idproducto;

  /* Ejecuta una petición ajax para buscar los datos del producto */
  $.ajax({
    url: url,
    method: "GET",
    dataType: 'json',
    success: (datos) => {
      /* Inserta el producto en la tabla y setea su valor */
      fila.children('.valorprecio').html(
        '$ ' + 
        parseFloat(datos.precio).toFixed(2) + 
        ' <small>MXN</small>'
      );
      fila.children('.valorprecio').data('valor', datos.precio);
      calculaSubtotalesAstillero(fila);
    }
  });
}

/* ALERT! esta función consume otra función de marina húmeda. Fix pendiente */
/* ALERTA NO SE PARA QUE FUNCIONA ESTO AYUDAA 😭 */
/** función del módulo: ASTILLERO
 */
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

/** evento del módulo: ASTILLERO
 * Añade el servicio seleccionado a la tabla de servicios y añade los productos que
 * pertenecen al kit o servicio a la pestaña de productos.
 */
document.querySelectorAll(".add-servicio").forEach(service => {
  /* Añade el evento click a cada servicio/kit */
  service.addEventListener("click", e => {

    e.preventDefault();

    let service = e.target || e.srcElement;
    let tableServices = document.getElementById("serviciosextra");
    let servicesLength = parseInt(tableServices.getAttribute("data-cantidad"));
    let newWidget = tableServices.getAttribute("data-prototype");

    /* aun no descubro para que es esta línea */
    // Set the attribute "data-cantidad" with the value "servicesLenght+1".
    tableServices.setAttribute("data-cantidad", servicesLength + 1);

    /* Setea varias variables dentro del html del servicio/kit */
    newWidget = newWidget.replace(/__name__/g, servicesLength);
    newWidget = newWidget.replace("td-otroservicio", "hide");
    newWidget = newWidget.replace("td-producto", "hide");
    newWidget = newWidget.replace("td-servicio", "hide");
    newWidget = newWidget.replace("input-group", "hide");
    newWidget = newWidget.replace("valorpromedio hide", "valorpromedio");

    /* Crea el servicio, le añade atributos, y lo añade a la tabla de servicios/kits */
    var serviceAdding = document.createElement("tr");
    serviceAdding.classList.add("servicio-agregado");
    serviceAdding.setAttribute("data-id", servicesLength);
    serviceAdding.innerHTML = newWidget;
    tableServices.appendChild(serviceAdding);
    
    /* Obtiene la row creada */
    var row = document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_servicio`).parentNode.parentNode;

    /* Variables para setear dentro de la row */
    let daysDiscount = service.getAttribute("data-dias_descuento");
    let id = service.getAttribute("data-id");
    let dollar = document.getElementById("appbundle_astillerocotizacion_dolar").value;
    let price = parseFloat(service.getAttribute("data-precio")).toFixed(4);
    let priceToShow = parseFloat(price);
    let currency = service.getAttribute("data-divisa");

    /* Obtiene la length */
    let length = 1;
    let lengthHtml = document.getElementById("eslora");
    if (service.getAttribute("data-tipo_cantidad") == 1) 
      if (typeof lengthHtml != "undefined" && lengthHtml != null) 
        length = parseInt(
          document.getElementById("eslora").getAttribute("data-valor")
        );
    
    /* setea los valores de la nueva row de servicio/kit creado */
    document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_cantidad`).value = length;
    document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_cantidad`).parentNode.setAttribute("data-valor", length);
    document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_servicio`).value = id;
    document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_tipoCantidad`).value = length;
    document.getElementById(`appbundle_astillerocotizacion_acservicios_${servicesLength}_promedio`).setAttribute("data-dias_descuento", daysDiscount);
    row.querySelector(".valorpromedio").setAttribute("data-dias_descuento", daysDiscount);
    row.querySelector(".valorpromedio").innerHTML = daysDiscount;

    /* Se comentó esta linea porque causa un bug: al añadir el servicio básico de estadía y un kit/servicio al mismo tiempo, la estadía
    se vuelve con valores NaN. Comentado esta línea se fixea y no afecta en lo absoluto. Ojo. */
    // calculaDiasEstadiaAstillero();

    /* Recalcula el precio, por alguna razón había bugs con los kits */
    price = parseFloat(price);
    /* Calcula el precio en dólares si es necesario */
    if (currency === "USD")
      priceToShow = parseFloat(price / dollar).toFixed(2);
    else priceToShow = parseFloat(price).toFixed(2);

    row.querySelector('.valorgrupo').querySelector('input').value = id;
    row.querySelector('.td-libre').innerHTML = service.getAttribute("data-nombre");
    // row.querySelector('.valorprecio').innerHTML = parseFloat(service.getAttribute("data-precio")).toFixed(2);
    row.querySelector('.valorprecio').innerHTML = `$ ${priceToShow.replace(/(\d)(?=(\d{3})+\.)/g, '$1,')} ${currency}`;
    row.querySelector('.valorprecio').setAttribute('data-valor', priceToShow);
    row.querySelector('.valorprecio').setAttribute('data-valorreal', price);
    row.querySelector('.valorprecio').setAttribute('data-divisa', currency);

    /* Calcula los subtotales. Cuando se haga refactorización de la función "calculaSubtotalesAstillero" 
    se dará como parámetro row. */
    var fila = $(`#appbundle_astillerocotizacion_acservicios_${servicesLength}_servicio`).parent().parent();
    calculaSubtotalesAstillero(fila);
    // calculaSubtotalesAstillero(row);

    /* Construye la url del endpoint para obtener los productos del servicio/kit */
    var baseurl = location.href.endsWith('/') ? location.href : location.href + '/';
    var url = baseurl + '../servicio/buscarservicio/' + id;

    $.ajax({
      url,
      method: "GET",
      dataType: 'json',
      success: ({gruposProductos}) => {
          gruposProductos.forEach(grupo => astilleroAgregaProducto(grupo, id))
      }
    });
  });
});

/* ASTILLERO */
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


/***************************************
 * 
 * FUNCIONES
 ***************************************/

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

/** función del módulo: ASTILLERO
 * Recalcula los días del astillero (por alguna razón).
 */
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

/** función del módulo: ASTILLERO
 * Función ejecutada dentro de la pestaña "Servicios Básicos",
 * hace el trigger de los checkboxs de servicios. Todos los servicios ya existen como
 * filas dentro del datatable en totales, pero estan "hidden".
 * 
 * @param {*} estatus 
 * @param {*} fila 
 * @param {*} filamx 
 */
function astilleroOcultaMuestraFila (elementId, rowId) {
  /* Si el checkbox es true, muestra la row en el dt de servicios básicos */
  if (document.getElementById(elementId).checked)
    document.getElementById(rowId).classList.remove("hidden");
  else
    document.getElementById(rowId).classList.add("hidden");

  /* Recalcula los totales */
  calculaTotalesAstillero();
}

/** función del módulo: ASTILLERO
 * Calcula los subtotales de la cotización siempre que se hace
 * alguna modificación a algún concepto de la misma.
 * 
 * @param {*} fila Row de algun servicio
 */
function calculaSubtotalesAstillero(fila) {
  let dolar = $('#appbundle_astillerocotizacion_dolar').val();
  let iva = $('#valorsistemaiva').data('valor');
  let precio = fila.children('.valorprecio').data('valor');

  /* Convierte el precio a pesos si viene en dólares, para calcular los totales */
  if(fila.children('.valorprecio').data("divisa") === "USD")
    precio = precio * dolar;

  let nuevaCantidad = fila.children('.valorcantidad').data('valor');
  let nuevoSubtotal = nuevaCantidad * precio;
  /* El IVA se divide entre 100 por el bug de guardar decimales como integers.
  se mantiene así porque no hace falta pegarle al IVA. */
  let nuevoIva = (nuevoSubtotal * iva) / 100;
  let nuevoTotal = nuevoSubtotal + nuevoIva;
  
  /* Renderea los nuevos valores y los setea */
  fila.children('.valorsubtotal').html('$ ' + (nuevoSubtotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' <small>MXN</small>');
  fila.children('.valorsubtotal').data('valor', nuevoSubtotal);
  fila.children('.valoriva').html('$ ' + (nuevoIva).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' <small>MXN</small>');
  fila.children('.valoriva').data('valor', nuevoIva);
  fila.children('.valortotal').html('$ ' + (nuevoTotal).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,') + ' <small>MXN</small>');
  fila.children('.valortotal').data('valor', nuevoTotal);

  /* Recalcula los totales de la cotización */
  calculaTotalesAstillero();
}

/** función del módulo: ASTILLERO
 * Recalcula todos los valores totales de la cotización obteniendo
 * cada row de algún servicio o producto y haciendo sumatorias de sus
 * valores.
 */
function calculaTotalesAstillero() {
    /* Regex para encontrar los precios en el innerHtml de los elementos */
    const pricePattern = /\d{1,9}(?:[.,]\d{3})*(?:[.,]\d{1,2})/g;
    const iva = Number($('#valorsistemaiva').data('valor'));
    const descuentoPorcentaje = Number($('#appbundle_astillerocotizacion_descuento').val());

    let valorSubtotal = 0;
    let valorIva = 0;
    let valorTotal = 0;
    let granSubtotalAd = 0;
    let granIvaAd = 0;
    let granTotalAd = 0;
    let granDescuentoAd = 0;

    /* Obtiene todos los table rows existentes */
    let list = document.querySelectorAll(".servicio-agregado");
    
    /* Obtiene los valores de cada servicio o producto y los acumula */
    list.forEach( tr => {
      valorSubtotal, valorIva, valorTotal = 0;

      if(!tr.classList.contains("hidden")) {
        /* busca el elemento, obtiene su texto html, usa regex para obtener el número,
        y por último hace replace por el formato que tiene y convierte a tipo Number. */
        valorSubtotal = Number(tr.querySelector(".valorsubtotal").innerHTML.match(pricePattern)[0].replace(",",""));
        valorIva = Number(tr.querySelector(".valoriva").innerHTML.match(pricePattern)[0].replace(",",""));
        valorTotal = Number(tr.querySelector(".valortotal").innerHTML.match(pricePattern)[0].replace(",",""));

        granSubtotalAd += valorSubtotal;
        granIvaAd += valorIva;
        granTotalAd += valorTotal;
      }
    });

    /* Calcula valores totales */
    if (descuentoPorcentaje != 0)
      granDescuentoAd = (granSubtotalAd * descuentoPorcentaje/100);
    granIvaAd = ((granSubtotalAd - granDescuentoAd) * iva/100);
    granTotalAd = granSubtotalAd - granDescuentoAd + granIvaAd;

    /* Los setea en la vista y añade al número formato */
    $('#gransubtot').html((granSubtotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#grandescuento').html((granDescuentoAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#graniva').html((granIvaAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    $('#grantot').html((granTotalAd).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
}

/* Astillero -> Servicios Básicos -> Días Adicionales */
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

/***************************************
 * BINDINGS Y EVENTOS
 ***************************************/

/** Función helper que retorna el string id para consumirse en los bindings
 * de servicios de astillero.
 * 
 * @param {*} id 
 */
function getIdService(id) { return `appbundle_astillerocotizacion_acservicios_${id}_estatus`};

/* Eventos "click" de los checkboxs en la pestaña "Servicios Básicos" */
document.getElementById(getIdService(0)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(0), "fila_grua"));
document.getElementById(getIdService(1)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(1), "fila_estadia"));
document.getElementById(getIdService(2)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(2), "cotizarampa"));
document.getElementById(getIdService(3)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(3), "cotizakarcher"));
document.getElementById(getIdService(4)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(4), "cotizaexplanada"));
document.getElementById(getIdService(5)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(5), "cotizaelectricidad"));
document.getElementById(getIdService(6)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(6), "cotizalimpieza"));
document.getElementById(getIdService(7)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(7), "cotizainspeccionar"));
if(document.getElementById(getIdService(8)) != null)
  ocument.getElementById(getIdService(8)).addEventListener("click", () => astilleroOcultaMuestraFila(getIdService(8), "fila_dia_adicional"));

/* Bindings de forms de nueva cotización en pestaña "servicios básicos" */

/** evento del módulo: ASTILLERO
 * Añade un evento de tipo "onChange" al datepicker de fechaLlegada
 */
$('#appbundle_astillerocotizacion_fechaLlegada').on("change", () => calculaDiasEstadiaAstillero());

/** evento del módulo: ASTILLERO
 * Añade un evento de tipo "onChange" al datepicker de fechaSalida
 */
$('#appbundle_astillerocotizacion_fechaSalida').on("change", () => calculaDiasEstadiaAstillero());

/* Astillero -> Servicios Básicos -> Días de estadía */
$('#appbundle_astillerocotizacion_diasEstadia').keyup(function () {
  var dias = $(this).val();
  var nueva_estadia_cantidad = dias * $("#estadia_cantidad").data('eslora');
  $("#estadia_cantidad").data('dias', dias);
  $("#estadia_cantidad").data('valor', nueva_estadia_cantidad);
  $("#estadia_cantidad").html(dias + ' (pie por día)');
  calculaSubtotalesAstillero($('#fila_estadia'));
  //calculaDiasEstadiaAstillero();
});

/* Astillero -> Servicios Básicos -> Dólar */
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

/* Astillero -> Servicios Básicos -> Descuento */
$('#appbundle_astillerocotizacion_descuento').keyup(function (){
  calculaTotalesAstillero();
});

/* Bindings de inputs de "servicios básicos" */

$('#appbundle_astillerocotizacion_acservicios_0_precio').keyup(function () {
  var grua_precio = $(this).val().replace(',','');
  $('#grua_precio').html('$ ' +  parseFloat(grua_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  $('#grua_precio').data('valor', grua_precio);
  var fila = $('#fila_grua');
  calculaSubtotalesAstillero(fila);
});

$('#appbundle_astillerocotizacion_acservicios_1_precio').keyup(function () {
  var estadia_precio = $(this).val().replace(',','');
  var dolar = $('#appbundle_astillerocotizacion_dolar').val();
  var estadia_precio_usd = estadia_precio * dolar;
  $('#estadia_precio').data('valor', estadia_precio_usd.toFixed(2));
  $('#estadia_precio').html('$ ' + parseFloat(estadia_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>USD</small>');
  var fila = $('#fila_estadia');
  calculaSubtotalesAstillero(fila);
});

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

$('#appbundle_astillerocotizacion_acservicios_7_precio').keyup(function () {
  var inspeccionar_precio = $(this).val().replace(',','');
  $('#inspeccionar_precio').html('$ ' + parseFloat(inspeccionar_precio).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')+' <small>MXN</small>');
  $('#inspeccionar_precio').data('valor', inspeccionar_precio);
  var fila = $('#cotizainspeccionar');
  calculaSubtotalesAstillero(fila);
});

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

/***************************************
 * SIN ORDENAR
 ***************************************/

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