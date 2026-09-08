// Seleccionamos el elemento del modal
const modal = document.getElementById('modal-bienvenida');

// 1. Comprobamos si el usuario ya visitó la página antes
if (localStorage.getItem('modalMostrado') === 'true') {
  // Si ya la visitó, ocultamos el modal de inmediato
  if (modal) modal.style.display = 'flex'; //Usa none ,si quieres que solo aparezca la primera vez
} else {
  // Si es su primera vez, nos aseguramos de que el modal sea visible
  if (modal) modal.style.display = 'flex';
}

// 2. Función para cerrar el modal y guardar el registro
function cerrarModal() {
  if (modal) {
    modal.style.display = 'none';
  }
  // Guardamos en el navegador que el usuario ya vio el modal
  localStorage.setItem('modalMostrado', 'true');
}

function moverCarrusel(direccion) {
    const track = document.getElementById('carruselTrack');
    if (!track) return;
  
    // Calculamos el ancho de una sola tarjeta
    const anchoTarjeta = track.clientWidth;
    
    // Desplazamos sumando o restando el ancho de la tarjeta
    track.scrollBy({
      left: anchoTarjeta * direccion,
      behavior: 'smooth'
    });
  }


// Esperamos a que todo el HTML de la página esté cargado
document.addEventListener("DOMContentLoaded", () => {
  
    // Tiempo de auto-reproducción (5 segundos)
    const TIEMPO_AUTO = 3000; 
  
    // Buscamos todos los carruseles de la página
    const carruseles = document.querySelectorAll('.carrusel-contenedor');
  
    carruseles.forEach((carrusel) => {
      const track = carrusel.querySelector('.carrusel-track');
      const bloques = carrusel.querySelectorAll('.carrusel-bloque');
      const dotsContainer = carrusel.querySelector('.carrusel-dots');
      
      // Si falta algún elemento esencial en este carrusel, nos saltamos al siguiente
      if (!track || bloques.length === 0 || !dotsContainer) return;
  
      // Guardamos el estado interno dentro del propio elemento HTML
      carrusel.dataset.indiceActual = 0;
      carrusel.dataset.totalBloques = bloques.length;
  
      // 1. Generar los puntos (dots) dinámicamente
      bloques.forEach((_, i) => {
        const dot = document.createElement('span');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        
        // Al hacer clic en el punto, movemos ese carrusel específico
        dot.addEventListener('click', () => {
          carrusel.dataset.indiceActual = i;
          actualizarCarrusel(carrusel);
          reiniciarTemporizador(carrusel);
        });
        
        dotsContainer.appendChild(dot);
      });
  
      // 2. Iniciar su propio temporizador automático
      iniciarAutoplay(carrusel);
  
      // 3. Soporte para deslizamiento táctil/manual en celulares
      track.addEventListener('scroll', () => {
        const anchoBloque = track.clientWidth;
        if (anchoBloque === 0) return; // Evita errores si el contenedor está oculto temporalmente
        const nuevoIndice = Math.round(track.scrollLeft / anchoBloque);
        if (nuevoIndice !== parseInt(carrusel.dataset.indiceActual) && nuevoIndice < bloques.length) {
          carrusel.dataset.indiceActual = nuevoIndice;
          actualizarDots(carrusel);
        }
      });
    });
  
    // Funciones internas del sistema de carruseles
    function iniciarAutoplay(carrusel) {
      carrusel.dataset.intervaloId = setInterval(() => {
        let indice = parseInt(carrusel.dataset.indiceActual);
        const total = parseInt(carrusel.dataset.totalBloques);
        
        indice = (indice + 1) % total;
        carrusel.dataset.indiceActual = indice;
        actualizarCarrusel(carrusel);
      }, TIEMPO_AUTO);
    }
  });
  
  // FUNCIÓN GLOBAL: Debe quedar fuera del DOMContentLoaded para que los botones HTML (onclick) puedan verla
  function cambiarBloque(boton, direccion) {
    const carrusel = boton.closest('.carrusel-contenedor');
    if (!carrusel) return;
  
    let indice = parseInt(carrusel.dataset.indiceActual);
    const total = parseInt(carrusel.dataset.totalBloques);
  
    indice += direccion;
    if (indice >= total) indice = 0;
    if (indice < 0) indice = total - 1;
  
    carrusel.dataset.indiceActual = indice;
    actualizarCarrusel(carrusel);
    
    // Detiene y arranca el reloj del carrusel específico
    clearInterval(carrusel.dataset.intervaloId);
    const TIEMPO_AUTO = 5000;
    carrusel.dataset.intervaloId = setInterval(() => {
      let ind = parseInt(carrusel.dataset.indiceActual);
      const tot = parseInt(carrusel.dataset.totalBloques);
      ind = (ind + 1) % tot;
      carrusel.dataset.indiceActual = ind;
      actualizarCarrusel(carrusel);
    }, TIEMPO_AUTO);
  }
  
  function actualizarCarrusel(carrusel) {
    const track = carrusel.querySelector('.carrusel-track');
    const indice = parseInt(carrusel.dataset.indiceActual);
    if (!track) return;
    
    const anchoBloque = track.clientWidth;
  
    track.scrollTo({
      left: anchoBloque * indice,
      behavior: 'smooth'
    });
  
    actualizarDots(carrusel);
  }
  
  function actualizarDots(carrusel) {
    const dots = carrusel.querySelectorAll('.dot');
    const indice = parseInt(carrusel.dataset.indiceActual);
    
    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === indice);
    });
  }
  