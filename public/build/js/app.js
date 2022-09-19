let paso=1;const pasoInicial=1,pasoFinal=3,server=window.location.origin,cita={id:"",nombre:"",fecha:"",hora:"",servicios:[]};function iniciarApp(){mostrarSeccion(),tabs(),botonesPaginador(),paginaAnterior(),paginaSiguiente(),consultarAPI(),idCliente(),nombreCliente(),seleccionarFecha(),seleccionarHora(),mostrarResumen()}function mostrarSeccion(){const e=document.querySelector(".mostrar");e&&e.classList.remove("mostrar");const t="#paso-"+paso;document.querySelector(t).classList.add("mostrar");const o=document.querySelector(".actual");o&&o.classList.remove("actual");document.querySelector(`[data-paso="${paso}"]`).classList.add("actual")}function tabs(){document.querySelectorAll(".tabs button").forEach(e=>{e.addEventListener("click",(function(e){paso=parseInt(e.target.dataset.paso),mostrarSeccion(),botonesPaginador()}))})}function botonesPaginador(){const e=document.querySelector("#anterior"),t=document.querySelector("#siguiente");1===paso?(e.classList.add("ocultar"),t.classList.remove("ocultar")):3===paso?(e.classList.remove("ocultar"),t.classList.add("ocultar"),mostrarResumen()):(e.classList.remove("ocultar"),t.classList.remove("ocultar")),mostrarSeccion()}function paginaAnterior(){document.querySelector("#anterior").addEventListener("click",(function(){paso<=1||(paso--,botonesPaginador())}))}function paginaSiguiente(){document.querySelector("#siguiente").addEventListener("click",(function(){paso>=3||(paso++,botonesPaginador())}))}async function consultarAPI(){try{const e=server+"/api/servicios",t=await fetch(e);mostrarServicios(await t.json())}catch(e){console.log(e)}}function mostrarServicios(e){e.forEach(e=>{const{id:t,nombre:o,precio:a}=e,n=document.createElement("P");n.classList.add("nombre-servicio"),n.textContent=o;const r=document.createElement("P");r.classList.add("precio-servicio"),r.textContent="$"+a;const c=document.createElement("DIV");c.classList.add("servicio"),c.dataset.servicioId=t,c.onclick=function(){selecionarServicio(e)},c.appendChild(n),c.appendChild(r),document.querySelector("#servicios").appendChild(c)})}function selecionarServicio(e){const{id:t}=e,{servicios:o}=cita,a=document.querySelector(`[data-servicio-id="${t}"]`);o.some(e=>e.id===t)?(cita.servicios=o.filter(e=>e.id!==t),a.classList.remove("seleccionado")):(cita.servicios=[...o,e],a.classList.add("seleccionado"))}function nombreCliente(){cita.nombre=document.querySelector("#nombre").value}function idCliente(){cita.id=document.querySelector("#id").value}function seleccionarFecha(){const e=document.querySelector("#fecha");e.addEventListener("input",(function(t){const o=new Date(e.value).getUTCDay();[6,0].includes(o)?(t.target.value="",mostrarAlerta("Fines de semana no permitidos","error",".formulario")):cita.fecha=t.target.value}))}function seleccionarHora(){document.querySelector("#hora").addEventListener("input",(function(e){const t=e.target.value.split(":")[0];t<10||t>18?(mostrarAlerta("Hora no válida","error",".formulario"),e.target.value=""):cita.hora=e.target.value}))}function mostrarAlerta(e,t,o,a=!0){const n=document.querySelector(".alerta");n&&n.remove();const r=document.createElement("DIV");r.textContent=e,r.classList.add("alerta"),r.classList.add(t);document.querySelector(o).appendChild(r),a&&setTimeout(()=>{r.remove()},3e3)}function mostrarResumen(){const e=document.querySelector(".contenido-resumen");for(;e.firstChild;)e.removeChild(e.firstChild);if(Object.values(cita).includes("")||0===cita.servicios.length)return void mostrarAlerta("Faltan datos de servicios, fecha u hora","error",".contenido-resumen",!1);const{nombre:t,fecha:o,hora:a,servicios:n}=cita,r=document.createElement("P");r.innerHTML="<span>Nombre:</span> "+t;const c=new Date(o),i=c.getMonth(),s=c.getDate(),d=c.getFullYear(),l=new Date(Date.UTC(d,i,s)).toLocaleDateString("es-ES",{weekday:"long",year:"numeric",month:"long",day:"numeric"}),u=document.createElement("P");u.innerHTML="<span>Fecha:</span> "+l;const m=document.createElement("P");m.innerHTML=`<span>Hora:</span> ${a} Horas`;const p=document.createElement("H3");p.textContent="Resumen Servicios",e.appendChild(p),n.forEach(t=>{const{id:o,nombre:a,precio:n}=t,r=document.createElement("DIV");r.classList.add("contenedor-servicio");const c=document.createElement("P");c.innerHTML="<span>Precio:</span> $"+n;const i=document.createElement("P");i.textContent=a,r.appendChild(i),r.appendChild(c),e.appendChild(r)});const v=document.createElement("H3");v.textContent="Resumen Cita",e.appendChild(v);const f=document.createElement("BUTTON");f.classList.add("boton"),f.textContent="Reservar Cita",f.onclick=reservarCita,e.appendChild(r),e.appendChild(u),e.appendChild(m),e.appendChild(f)}async function reservarCita(){const{nombre:e,fecha:t,hora:o,servicios:a,id:n}=cita,r=a.map(e=>e.id),c=new FormData;c.append("fecha",t),c.append("hora",o),c.append("usuarioId",n),c.append("servicios",r);try{const e=server+".com/api/citas",t=await fetch(e,{method:"POST",body:c}),o=await t.json();console.log(o.resultado),o.resultado&&Swal.fire({icon:"success",title:"Cita Creada",text:"Tu cita fue creada correctamente",button:"OK"}).then(()=>{setTimeout(()=>{window.location.reload()},3e3)})}catch(e){Swal.fire({icon:"error",title:"Error",text:"Hubo un error al guardar la cita"})}}document.addEventListener("DOMContentLoaded",(function(){iniciarApp()}));