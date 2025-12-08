// Empêcher Enter de soumettre avant l'étape 3
document
  .getElementById("entrepriseForm")
  .addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      const stepCompany = stepIsVisible("step-company");
      const stepManager = stepIsVisible("step-manager");
      const stepRecap = stepIsVisible("step-recap");

      // Autoriser Enter seulement à l'étape 3
      if ((stepCompany || stepManager) && !stepRecap) {
        event.preventDefault();
        return false;
      }
    }
  });

function stepIsVisible(id) {
  return document.getElementById(id).style.display !== "none";
}

// RESET invalid styles
function resetInvalid(form) {
  form
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));
}

// Helper display error
function showError(message, inputName = null) {
  const errorBox = document.getElementById("form-errors");
  errorBox.innerHTML += message + "<br>";
  errorBox.classList.remove("d-none");

  if (inputName) {
    const field = document.querySelector(`[name="${inputName}"]`);
    if (field) field.classList.add("is-invalid");
  }
}

// =======================================================================
// STEP 1 → STEP 2
// =======================================================================
document.getElementById("btnNextToManager").onclick = () => {
  const form = document.getElementById("entrepriseForm");
  const errorBox = document.getElementById("form-errors");
  errorBox.classList.add("d-none");
  errorBox.innerHTML = "";

  resetInvalid(form);

  const rules = [
    [
      "nom_entreprise",
      (v) => v !== "",
      "Le nom de l’entreprise est obligatoire.",
    ],
    ["secteur_id", (v) => v !== "", "Le secteur d'activité est obligatoire."],
    ["adresse", (v) => v !== "", "L’adresse est obligatoire."],
    [
      "code_postal",
      (v) => /^\d{5}$/.test(v),
      "Le code postal doit contenir 5 chiffres.",
    ],
    [
      "ville",
      (v) => /^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/.test(v),
      "La ville contient des caractères invalides.",
    ],
    ["pays", (v) => v !== "", "Le pays est obligatoire."],
    ["siret", (v) => /^\d{14}$/.test(v), "Le SIRET doit contenir 14 chiffres."],
    [
      "telephone",
      (v) => v === "" || /^(0\d{9}|\+33\d{9})$/.test(v),
      "Numéro de téléphone invalide.",
    ],
    [
      "email_entreprise",
      (v) => v === "" || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
      "L’email de l’entreprise est invalide.",
    ],
  ];

  let valid = true;

  rules.forEach(([name, validator, message]) => {
    const value = form[name].value.trim();
    if (!validator(value)) {
      showError(message, name);
      valid = false;
    }
  });

  if (!valid) return;

  document.getElementById("step-company").style.display = "none";
  document.getElementById("step-manager").style.display = "block";
};

// =======================================================================
// STEP 2 → STEP 3
// =======================================================================
document.getElementById("btnNextToRecap").onclick = () => {
  const form = document.getElementById("entrepriseForm");
  const errorBox = document.getElementById("form-errors");
  errorBox.classList.add("d-none");
  errorBox.innerHTML = "";

  resetInvalid(form);

  const rules = [
    ["prenom", (v) => /^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/.test(v), "Prénom invalide."],
    ["nom", (v) => /^[A-Za-zÀ-ÖØ-öø-ÿ\-\s]+$/.test(v), "Nom invalide."],
    ["email", (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), "Email invalide."],
    //  fr numéro mobile (06/07)
    [
      "telephone_gestionnaire",
      (v) => v === "" || /^0[6-7]\d{8}$/.test(v.replace(/[\s-]/g, "")),
      "Numéro mobile invalide (06/07).",
    ],
    [
      "password",
      (v) => v.length >= 6,
      "Mot de passe trop court. (min 6 caractères)",
    ],
  ];

  let valid = true;

  rules.forEach(([name, validator, message]) => {
    const value = form[name].value.trim();
    if (!validator(value)) {
      showError(message, name);
      valid = false;
    }
  });

  if (form.password.value !== form.password_confirm.value) {
    showError("Les mots de passe ne correspondent pas.", "password_confirm");
    valid = false;
  }

  if (!valid) return;

  // RÉCAP
  const recap = document.getElementById("recap-content");
  recap.innerHTML = "";

  const data = new FormData(form);
  data.forEach((val, key) => {
    if (key !== "password" && key !== "password_confirm") {
      recap.innerHTML += `<div><strong>${key}</strong> : ${
        val || "<em>Non renseigné</em>"
      }</div>`;
    }
  });

  document.getElementById("step-manager").style.display = "none";
  document.getElementById("step-recap").style.display = "block";
};

// =======================================================================
// STEP 3 : Validation finale
// =======================================================================
function validateFinalStep() {
  return stepIsVisible("step-recap");
}

// RETOUR
document.getElementById("btnBackToCompany").onclick = () => {
  document.getElementById("step-manager").style.display = "none";
  document.getElementById("step-company").style.display = "block";
};

document.getElementById("btnBackToManager").onclick = () => {
  document.getElementById("step-recap").style.display = "none";
  document.getElementById("step-manager").style.display = "block";
};
