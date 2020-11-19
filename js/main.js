$(document).ready(function() {
    inicializarSlider();
    playVideoOnScroll();
    $("#tabs").tabs();

    fillProperties()
    fillPropertiesToMe()

    fillCities()
    fillTypes()

    loadEvents()
});

/*
  Creación de una función personalizada para jQuery que detecta cuando se detiene el scroll en la página
*/

$.fn.scrollEnd = function(callback, timeout) {
    $(this).scroll(function() {
        var $this = $(this);
        if ($this.data('scrollTimeout')) {
            clearTimeout($this.data('scrollTimeout'));
        }
        $this.data('scrollTimeout', setTimeout(callback, timeout));
    });
};

/*
  Función que inicializa el elemento Slider
*/

function inicializarSlider() {
    $("#rangoPrecio").ionRangeSlider({
        type: "double",
        grid: false,
        min: 0,
        max: 100000,
        from: 200,
        to: 80000,
        prefix: "$"
    });
}

/*
  Función que reproduce el video de fondo al hacer scroll, y deteiene la reproducción al detener el scroll
*/

function playVideoOnScroll() {
    var ultimoScroll = 0,
        intervalRewind;
    var video = document.getElementById('vidFondo');
    $(window)
        .scroll((event) => {
            var scrollActual = $(window).scrollTop();
            if (scrollActual > ultimoScroll) {

            } else {
                //this.rewind(1.0, video, intervalRewind);
                video.play();
            }
            ultimoScroll = scrollActual;
        })
        .scrollEnd(() => {
            video.pause();
        }, 10)
}

function fillProperties(city = null, type = null) {
    let params = ['a=properties']
    if (city) params.push('city=' + city)
    if (type) params.push('type=' + type)

    fetch('/backend?' + params.join('&'))
        .then(res => res.json())
        .then(properties => {
            const container = document.querySelector('#divResultadosBusqueda ul')

            container.innerHTML = ''
            for (let property of properties) {
                container.innerHTML += `
                    <li class="property-item">
                        <figure class="property-image">
                            <img src="./img/home.jpg" alt="Propiedad">
                        </figure>
                        <div class="property-details">
                            <h5 class="property-price">${property.Precio}</h5>
                            <p><strong>Dirección: </strong>${property.Direccion}</p>
                            <p><strong>Ciudad: </strong>${property.Ciudad}</p>
                            <p><strong>Teléfono: </strong>${property.Telefono}</p>
                            <p><strong>Cod. Postal: </strong>${property.Codigo_Postal}</p>
                            <p><strong>Tipo: </strong>${property.Tipo}</p>
                            <button type="button" class="btn" onclick="addToMe(event, ${property.Id})">Guardar</button>
                        </div>
                    </li>
                `
            }
        })
}

function fillPropertiesToMe() {
    let params = ['a=properties-to-me']

    fetch('/backend?' + params.join('&'))
        .then(res => res.json())
        .then(properties => {
            const container = document.querySelector('#divResultadosMios ul')

            container.innerHTML = ''
            for (let property of properties) {
                container.innerHTML += `
                    <li class="property-item">
                        <figure class="property-image">
                            <img src="./img/home.jpg" alt="Propiedad">
                        </figure>
                        <div class="property-details">
                            <h5 class="property-price">${property.Precio}</h5>
                            <p><strong>Dirección: </strong>${property.Direccion}</p>
                            <p><strong>Ciudad: </strong>${property.Ciudad}</p>
                            <p><strong>Teléfono: </strong>${property.Telefono}</p>
                            <p><strong>Cod. Postal: </strong>${property.Codigo_Postal}</p>
                            <p><strong>Tipo: </strong>${property.Tipo}</p>
                            <button type="button" class="btn" onclick="removeToMe(event, ${property.Id})">Eliminar</button>
                        </div>
                    </li>
                `
            }
        })
}

function fillCities() {
    fetch('/backend?a=cities')
        .then(res => res.json())
        .then(cities => {
            const select = document.getElementById('selectCiudad')
            
            select.innerHTML = '<option value="" selected>Todas</option>'
            for (let city of cities) {
                select.innerHTML += `<option value="${city.Ciudad}">${city.Ciudad}</option>`
            }
        })
}

function fillTypes() {
    fetch('/backend?a=types')
        .then(res => res.json())
        .then(cities => {
            const select = document.getElementById('selectTipo')
            
            select.innerHTML = '<option value="" selected>Todos</option>'
            for (let city of cities) {
                select.innerHTML += `<option value="${city.Tipo}">${city.Tipo}</option>`
            }
        })
}

function searchProperties(evt) {
    evt.preventDefault()
    
    const cityElement = document.getElementById('selectCiudad')
    const typeElement = document.getElementById('selectTipo')

    fillProperties(
        (cityElement.value.length ? cityElement.value : null),
        (typeElement.value.length ? typeElement.value : null)
    )
}

function addToMe(event, idProperty) {
    event.preventDefault()

    fetch('/backend?a=add-to-me&property=' + idProperty)
        .then(res => {
            fillPropertiesToMe()

            event.target.innerHTML = 'Añadido'
            event.target.disabled = true
        })
}

function removeToMe(event, idProperty) {
    event.preventDefault()

    fetch('/backend?a=remove-to-me&property=' + idProperty)
        .then(res => {
            fillPropertiesToMe()
        })
}

function loadEvents() {
    $('#formulario').on('submit', searchProperties)
}