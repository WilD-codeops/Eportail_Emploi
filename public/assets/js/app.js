document.addEventListener("click", function (e) {
  const link = e.target.closest(".offcanvas a.nav-link");
  if (!link) return;

  const offcanvasEl = document.querySelector("#sidebarOffcanvas");
  if (!offcanvasEl) return;

  const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
  if (instance) instance.hide();
});
