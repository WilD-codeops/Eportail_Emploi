document.addEventListener("DOMContentLoaded", () => {
  // -----------------------------
  // 0) Sélecteurs principaux
  // -----------------------------
  const form = document.getElementById("entrepriseForm");
  if (!form) return;

  const errorBox = document.getElementById("form-errors");

  const stepCompany = document.getElementById("step-company");
  const stepManager = document.getElementById("step-manager");
  const stepRecap = document.getElementById("step-recap");

  const btnNextToManager = document.getElementById("btnNextToManager");
  const btnNextToRecap = document.getElementById("btnNextToRecap");

  const btnBackToCompany = document.getElementById("btnBackToCompany");
  const btnBackToManager = document.getElementById("btnBackToManager");

  const recapContent = document.getElementById("recap-content");

  const passwordField = document.getElementById("password");
  const confirmField = document.getElementById("password_confirm");

  // -----------------------------
  // 1) Helpers UI
  // -----------------------------
  function isStepVisible(stepEl) {
    // plus fiable que element.style.display (car parfois vide)
    return window.getComputedStyle(stepEl).display !== "none";
  }

  function resetInvalid() {
    form
      .querySelectorAll(".is-invalid, .is-valid, .field-valid, .field-invalid")
      .forEach((el) => {
        el.classList.remove(
          "is-invalid",
          "is-valid",
          "field-valid",
          "field-invalid"
        );
      });
  }

  function clearErrors() {
    if (!errorBox) return;
    errorBox.classList.add("d-none");
    errorBox.innerHTML = "";
  }

  function showError(message, inputName = null) {
    if (errorBox) {
      errorBox.innerHTML += `${message}<br>`;
      errorBox.classList.remove("d-none");
    }

    if (inputName) {
      const field = form.querySelector(`[name="${inputName}"]`);
      if (field) field.classList.add("is-invalid");
    }
  }

  // -----------------------------
  // 2) Empêcher Enter avant step 3
  // -----------------------------
  form.addEventListener("keydown", (event) => {
    if (event.key !== "Enter") return;

    // Autoriser Enter seulement à l'étape 3 (récap)
    if (
      (isStepVisible(stepCompany) || isStepVisible(stepManager)) &&
      !isStepVisible(stepRecap)
    ) {
      event.preventDefault();
    }
  });

  // -----------------------------
  // 3) STEP 1 -> STEP 2 (Entreprise)
  // -----------------------------
  function validateStepCompany() {
    clearErrors();
    resetInvalid();

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
      [
        "siret",
        (v) => /^\d{14}$/.test(v),
        "Le SIRET doit contenir 14 chiffres.",
      ],
      [
        "telephone",
        (v) => v === "" || /^(0[1-9]\d{8}|\+33[1-9]\d{8})$/.test(v),
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
      const field = form[name];
      const value = (field?.value || "").trim();
      if (!validator(value)) {
        showError(message, name);
        valid = false;
      }
    });

    return valid;
  }

  function goToStepManager() {
    stepCompany.style.display = "none";
    stepManager.style.display = "block";
    stepRecap.style.display = "none";
  }

  if (btnNextToManager) {
    btnNextToManager.addEventListener("click", () => {
      if (!validateStepCompany()) return;
      goToStepManager();
    });
  }

  // -----------------------------
  // 4) Live password (force + match)
  // -----------------------------
  function updatePasswordStrength() {
    if (!passwordField) return;

    const pass = passwordField.value;
    const checks = document.querySelectorAll(".check-item");
    const fill = document.querySelector(".strength-fill");

    // Si la partie UI n'existe pas, on ne bloque pas le JS
    if (!checks.length || !fill) return;

    let score = 0;

    // 1) 8+ chars
    if (pass.length >= 8) {
      checks[0].classList.add("valid");
      score++;
    } else checks[0].classList.remove("valid");

    // 2) Majuscule
    if (/[A-Z]/.test(pass)) {
      checks[1].classList.add("valid");
      score++;
    } else checks[1].classList.remove("valid");

    // 3) Chiffre
    if (/\d/.test(pass)) {
      checks[2].classList.add("valid");
      score++;
    } else checks[2].classList.remove("valid");

    // 4) Spécial
    if (/[@$!%*?&]/.test(pass)) {
      checks[3].classList.add("valid");
      score++;
    } else checks[3].classList.remove("valid");

    fill.className = `strength-fill strength-${score}`;
  }

  function updatePasswordMatch() {
    if (!passwordField || !confirmField) return;

    const pass = passwordField.value;
    const confirm = confirmField.value;

    // Ajout / récupération de l’icône
    let matchIcon = confirmField.parentElement.querySelector(".match-icon");
    if (!matchIcon) {
      confirmField.parentElement.insertAdjacentHTML(
        "beforeend",
        '<i class="bi match-icon ms-1"></i>'
      );
      matchIcon = confirmField.parentElement.querySelector(".match-icon");
    }

    // Reset classes
    confirmField.classList.remove("is-valid", "is-invalid");

    if (confirm && confirm === pass) {
      confirmField.classList.add("is-valid");
      matchIcon.className =
        "bi bi-check-circle-fill text-success match-icon ms-1";
    } else if (confirm) {
      confirmField.classList.add("is-invalid");
      matchIcon.className = "bi bi-x-circle-fill text-danger match-icon ms-1";
    } else {
      matchIcon.className = "bi match-icon ms-1";
    }
  }

  if (passwordField)
    passwordField.addEventListener("input", () => {
      updatePasswordStrength();
      updatePasswordMatch();
    });

  if (confirmField) confirmField.addEventListener("input", updatePasswordMatch);

  // -----------------------------
  // 5) Toggle afficher/masquer mot de passe
  // -----------------------------
  document.querySelectorAll(".toggle-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;

      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";

      // Switch icon (Bootstrap Icons)
      const icon = btn.querySelector("i");
      if (icon) icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
  });

  // -----------------------------
  // 6) STEP 2 -> STEP 3 (Gestionnaire + Récap)
  // -----------------------------
  function validateStepManager() {
    clearErrors();
    resetInvalid();

    let valid = true;

    const prenom = (form["prenom"]?.value || "").trim();
    if (!prenom) {
      showError("Le prénom du gestionnaire est obligatoire.", "prenom");
      valid = false;
    }

    const nom = (form["nom"]?.value || "").trim();
    if (!nom) {
      showError("Le nom du gestionnaire est obligatoire.", "nom");
      valid = false;
    }

    const email = (form["email"]?.value || "").trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showError("L’email du gestionnaire est invalide.", "email");
      valid = false;
    }

    const pass = passwordField?.value || "";
    const confirm = confirmField?.value || "";

    // Complexité (même logique que ton Validator PHP)
    const passOk =
      pass.length >= 8 &&
      /[A-Z]/.test(pass) &&
      /[a-z]/.test(pass) &&
      /\d/.test(pass) &&
      /[@$!%*?&]/.test(pass);

    if (!passOk) {
      showError(
        "Mot de passe invalide (8+ caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 spécial).",
        "password"
      );
      valid = false;
    }

    if (pass !== confirm) {
      showError(
        "Le mot de passe et sa confirmation ne correspondent pas.",
        "password_confirm"
      );
      valid = false;
    }

    const cgu = document.getElementById("cgu");
    if (cgu && !cgu.checked) {
      showError("Vous devez accepter les conditions générales d’utilisation.");
      valid = false;
    }

    return valid;
  }

  function buildRecap() {
    if (!recapContent) return;

    const secteurSelect = form["secteur_id"];
    const secteurLabel = secteurSelect
      ? secteurSelect.options[secteurSelect.selectedIndex]?.text
      : "";

    recapContent.innerHTML = `
      <div class="mb-2"><strong>Nom de l’entreprise :</strong> ${form["nom_entreprise"].value}</div>
      <div class="mb-2"><strong>Secteur :</strong> ${secteurLabel}</div>
      <div class="mb-2"><strong>Adresse :</strong> ${form["adresse"].value}, ${form["code_postal"].value} ${form["ville"].value}, ${form["pays"].value}</div>
      <div class="mb-2"><strong>SIRET :</strong> ${form["siret"].value}</div>
      <div class="mb-2"><strong>Gestionnaire :</strong> ${form["prenom"].value} ${form["nom"].value} (${form["email"].value})</div>
    `;
  }

  function goToStepRecap() {
    stepCompany.style.display = "none";
    stepManager.style.display = "none";
    stepRecap.style.display = "block";
  }

  if (btnNextToRecap) {
    btnNextToRecap.addEventListener("click", () => {
      if (!validateStepManager()) return;
      buildRecap();
      goToStepRecap();
    });
  }

  // -----------------------------
  // 7) Boutons retour
  // -----------------------------
  if (btnBackToCompany) {
    btnBackToCompany.addEventListener("click", () => {
      clearErrors();
      stepManager.style.display = "none";
      stepCompany.style.display = "block";
      stepRecap.style.display = "none";
    });
  }

  if (btnBackToManager) {
    btnBackToManager.addEventListener("click", () => {
      clearErrors();
      stepRecap.style.display = "none";
      stepManager.style.display = "block";
    });
  }

  // -----------------------------
  // 8) Live SIRET (optionnel)
  // -----------------------------
  const siretInput = form.querySelector(".siret-input");
  if (siretInput) {
    siretInput.addEventListener("input", function () {
      const val = this.value.replace(/\D/g, "");
      this.value = val;

      const valid = val.length === 14;

      this.classList.toggle("is-valid", valid);
      this.classList.toggle("is-invalid", !valid);

      const validFeedback =
        this.parentElement.querySelector(".validity-feedback");
      const invalidFeedback =
        this.parentElement.querySelector(".invalid-feedback");

      if (validFeedback) validFeedback.classList.toggle("d-none", !valid);
      if (invalidFeedback) invalidFeedback.classList.toggle("d-none", valid);
    });
  }
});
