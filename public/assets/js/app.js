document.addEventListener("click", function (e) {
  const link = e.target.closest(".offcanvas a.nav-link");
  if (!link) return;

  const offcanvasEl = document.querySelector("#sidebarOffcanvas");
  if (!offcanvasEl) return;

  const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
  if (instance) instance.hide();
});

// ===========================
// Offres publiques (AJAX simple)
// ===========================
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#offres-public-filters-form");
  const results = document.querySelector("#offres-public-results");

  if (!form || !results) return;

  const fetchResults = (qs) => {
    results.setAttribute("aria-busy", "true");

    fetch("/offres/partial?" + qs, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    })
      .then((res) => res.text())
      .then((html) => {
        results.innerHTML = html;
        results.removeAttribute("aria-busy");
      })
      .catch(() => {
        results.removeAttribute("aria-busy");
      });
  };

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const fd = new FormData(form);
    fd.set("page", "1");

    const qs = new URLSearchParams(fd).toString();

    history.replaceState(null, "", "/offres?" + qs);
    fetchResults(qs);
  });

  results.addEventListener("click", function (e) {
    const link = e.target.closest("a[data-ajax-page='1']");
    if (!link) return;

    e.preventDefault();

    const qs = link.getAttribute("data-qs") || "";

    history.replaceState(null, "", "/offres?" + qs);
    fetchResults(qs);
  });
});
