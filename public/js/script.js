/*
function mostrarModalLogout() {
  document.getElementById("modal-logout").classList.remove("hidden");
}

function cerrarModalLogout() {
  document.getElementById("modal-logout").classList.add("hidden");
}

const logout = document.getElementById("logout");

if (logout) {
  logout.addEventListener("click", function (event) {
    event.preventDefault();
    mostrarModalLogout();
  });
}
*/

function mostrarModal(mensaje) {
  document.getElementById("modal-text").textContent = mensaje;
  document.getElementById("modal-error").classList.remove("hidden");
}

function cerrarModal() {
  document.getElementById("modal-error").classList.add("hidden");
}

const formularioCrearTarea = document.getElementById("formularioCrearTarea");

if (formularioCrearTarea) {
  const semanal = document.getElementById("semanal");
  const mensual = document.getElementById("mensual");

  const diasSemana = document.getElementById("diasSemana");
  const diasMes = document.getElementById("diasMes");

  if (semanal && mensual) {
    semanal.addEventListener("change", function () {
      diasSemana.style.display = "block";
      diasMes.style.display = "none";
    });

    mensual.addEventListener("change", function () {
      diasSemana.style.display = "none";
      diasMes.style.display = "grid";
    });
  }

  formularioCrearTarea.addEventListener("submit", function (event) {
    const diasSemana = document.querySelectorAll(
      "#diasSemana input[type='checkbox']:checked",
    );

    const diasMes = document.querySelectorAll(
      "#diasMes input[type='checkbox']:checked",
    );

    if (diasSemana.length === 0 && diasMes.length === 0) {
      event.preventDefault();
      mostrarModal("Debes seleccionar al menos un día.");
    }
  });
}
