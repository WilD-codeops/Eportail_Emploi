document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("candidatForm");
  if (!form) return;

  const errorBox = document.getElementById("form-errors");

  const stepAccount = document.getElementById("step-account");
  const stepProfile = document.getElementById("step-profile");
  const stepRecap = document.getElementById("step-recap");

  const btnNextToProfile = document.getElementById("btnNextToProfile");
  const btnBackToAccount = document.getElementById("btnBackToAccount");

  const btnSkipProfile = document.getElementById("btnSkipProfile");
  const btnNextToRecap = document.getElementById("btnNextToRecap");
  const btnBackToProfile = document.getElementById("btnBackToProfile");

  const recapContent = document.getElementById("recap-content");

  const passwordField = document.getElementById("password");
  const confirmField = document.getElementById("password_confirm");

  function isStepVisible(stepEl) {
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

  // Empêcher Enter avant récap (évite submit accidentel)
  form.addEventListener("keydown", (event) => {
    if (event.key !== "Enter") return;

    if (
      (isStepVisible(stepAccount) || isStepVisible(stepProfile)) &&
      !isStepVisible(stepRecap)
    ) {
      event.preventDefault();
    }
  });

  // Password strength UI (identique à entreprise)
  function updatePasswordStrength() {
    if (!passwordField) return;

    const pass = passwordField.value;
    const checks = document.querySelectorAll(".check-item");
    const fill = document.querySelector(".strength-fill");
    if (!checks.length || !fill) return;

    let score = 0;

    if (pass.length >= 8) {
      checks[0].classList.add("valid");
      score++;
    } else checks[0].classList.remove("valid");

    if (/[A-Z]/.test(pass)) {
      checks[1].classList.add("valid");
      score++;
    } else checks[1].classList.remove("valid");

    if (/\d/.test(pass)) {
      checks[2].classList.add("valid");
      score++;
    } else checks[2].classList.remove("valid");

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

    let matchIcon = confirmField.parentElement.querySelector(".match-icon");
    if (!matchIcon) {
      confirmField.parentElement.insertAdjacentHTML(
        "beforeend",
        '<i class="bi match-icon ms-1"></i>'
      );
      matchIcon = confirmField.parentElement.querySelector(".match-icon");
    }

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

  if (passwordField) {
    passwordField.addEventListener("input", () => {
      updatePasswordStrength();
      updatePasswordMatch();
    });
  }
  if (confirmField) confirmField.addEventListener("input", updatePasswordMatch);

  // Toggle password
  document.querySelectorAll(".toggle-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (!input) return;

      const isHidden = input.type === "password";
      input.type = isHidden ? "text" : "password";

      const icon = btn.querySelector("i");
      if (icon) icon.className = isHidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
  });

  // STEP 1 validation
  function validateStepAccount() {
    clearErrors();
    resetInvalid();

    let valid = true;

    const prenom = (form["prenom"]?.value || "").trim();
    if (!prenom) {
      showError("Le prénom est obligatoire.", "prenom");
      valid = false;
    }

    const nom = (form["nom"]?.value || "").trim();
    if (!nom) {
      showError("Le nom est obligatoire.", "nom");
      valid = false;
    }

    const email = (form["email"]?.value || "").trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showError("L’email est invalide.", "email");
      valid = false;
    }

    const tel = (form["telephone"]?.value || "").trim();
    if (tel && !/^(0[1-9]\d{8}|\+33[1-9]\d{8})$/.test(tel)) {
      showError("Numéro de téléphone invalide.", "telephone");
      valid = false;
    }

    const pass = passwordField?.value || "";
    const confirm = confirmField?.value || "";

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
      showError("Vous devez accepter les Conditions Générales d’Utilisation.");
      valid = false;
    }

    return valid;
  }

  function goToStepProfile() {
    stepAccount.style.display = "none";
    stepProfile.style.display = "block";
    stepRecap.style.display = "none";
  }

  function goToStepAccount() {
    stepProfile.style.display = "none";
    stepAccount.style.display = "block";
    stepRecap.style.display = "none";
  }

  function goToStepRecap() {
    stepAccount.style.display = "none";
    stepProfile.style.display = "none";
    stepRecap.style.display = "block";
  }

  // Recap builder
  function buildRecap() {
    if (!recapContent) return;

    const poste = (form["poste_recherche"]?.value || "").trim() || "—";
    const dispo = (form["disponibilite"]?.value || "").trim() || "—";
    const mobilite = (form["mobilite"]?.value || "").trim() || "—";
    const exp = (form["annee_experience"]?.value || "").trim() || "—";
    const etudes = (form["niveau_etudes"]?.value || "").trim() || "—";
    const statut = (form["statut_actuel"]?.value || "").trim() || "—";

    recapContent.innerHTML = `
      <div class="mb-2"><strong>Compte :</strong> ${form["prenom"].value} ${form["nom"].value} (${form["email"].value})</div>
      <hr>
      <div class="mb-2"><strong>Poste recherché :</strong> ${poste}</div>
      <div class="mb-2"><strong>Disponibilité :</strong> ${dispo}</div>
      <div class="mb-2"><strong>Mobilité :</strong> ${mobilite}</div>
      <div class="mb-2"><strong>Expérience :</strong> ${exp}</div>
      <div class="mb-2"><strong>Niveau d’études :</strong> ${etudes}</div>
      <div class="mb-2"><strong>Statut actuel :</strong> ${statut}</div>
    `;
  }

  // Nav buttons
  if (btnNextToProfile) {
    btnNextToProfile.addEventListener("click", () => {
      if (!validateStepAccount()) return;
      goToStepProfile();
    });
  }

  if (btnBackToAccount) {
    btnBackToAccount.addEventListener("click", () => {
      clearErrors();
      goToStepAccount();
    });
  }

  // Skip profile => recap direct
  if (btnSkipProfile) {
    btnSkipProfile.addEventListener("click", () => {
      clearErrors();
      buildRecap();
      goToStepRecap();
    });
  }

  // Continue profile => recap
  if (btnNextToRecap) {
    btnNextToRecap.addEventListener("click", () => {
      clearErrors();
      buildRecap();
      goToStepRecap();
    });
  }

  if (btnBackToProfile) {
    btnBackToProfile.addEventListener("click", () => {
      clearErrors();
      stepRecap.style.display = "none";
      stepProfile.style.display = "block";
    });
  }
});
