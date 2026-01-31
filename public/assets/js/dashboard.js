document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".js-details-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const html = `
                <h4 class="mb-3">${btn.dataset.nom}</h4>

                <div class="row g-3">

                    <div class="col-md-6">
                        <p><strong>Secteur :</strong><br>${
                          btn.dataset.secteur || "—"
                        }</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>SIRET :</strong><br>${
                          btn.dataset.siret || "—"
                        }</p>
                    </div>

                    <div class="col-md-12">
                        <p><strong>Adresse :</strong><br>
                        ${btn.dataset.adresse || "—"}, ${
        btn.dataset.cp || ""
      } ${btn.dataset.ville || ""} (${btn.dataset.pays || ""})
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>Email :</strong><br>${
                          btn.dataset.email || "—"
                        }</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>Téléphone :</strong><br>${
                          btn.dataset.tel || "—"
                        }</p>
                    </div>

                    <div class="col-md-12">
                        <p><strong>Site web :</strong><br>
                        ${
                          btn.dataset.site
                            ? `<a href="${btn.dataset.site}" target="_blank">${btn.dataset.site}</a>`
                            : "—"
                        }
                        </p>
                    </div>

                    <div class="col-md-12">
                        <p><strong>Description :</strong><br>${
                          btn.dataset.description || "—"
                        }</p>
                    </div>

                    <div class="col-md-12">
                        <p><strong>Gestionnaire :</strong><br>${
                          btn.dataset.gestionnaire || "—"
                        }</p>
                    </div>

                    <div class="col-md-12">
                        <p><strong>Date d'inscription :</strong><br>${
                          btn.dataset.date_inscription || "—"
                        }</p>

                </div>
            `;

      document.getElementById("entrepriseModalContent").innerHTML = html;
    });
  });
});
