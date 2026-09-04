function mostrarModal(mensaje) {
  document.getElementById("modal-text").textContent = mensaje;
  document.getElementById("modal-error").classList.remove("hidden");
}

function cerrarModal() {
  document.getElementById("modal-error").classList.add("hidden");
}

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
