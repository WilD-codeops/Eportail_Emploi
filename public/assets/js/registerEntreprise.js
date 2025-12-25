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
// Live password + confirm
document
  .getElementById("password")
  .addEventListener("input", validatePasswordLive);
document
  .getElementById("password_confirm")
  .addEventListener("input", validatePasswordConfirm);

function validatePasswordLive() {
  const pass = document.getElementById("password").value;
  const checks = document.querySelectorAll(".check-item");
  const fill = document.querySelector(".strength-fill");

  let score = 0;

  // 8+ chars
  if (pass.length >= 8) {
    checks[0].classList.add("valid");
    score++;
  } else checks[0].classList.remove("valid");

  // Majuscule
  if (/[A-Z]/.test(pass)) {
    checks[1].classList.add("valid");
    score++;
  } else checks[1].classList.remove("valid");

  // Chiffre
  if (/\d/.test(pass)) {
    checks[2].classList.add("valid");
    score++;
  } else checks[2].classList.remove("valid");

  // Spécial
  if (/[@$!%*?&]/.test(pass)) {
    checks[3].classList.add("valid");
    score++;
  } else checks[3].classList.remove("valid");

  fill.className = `strength-fill strength-${score}`;
}

function validatePasswordConfirm() {
  const pass = document.getElementById("password").value;
  const confirm = document.getElementById("password_confirm");
  const matchIcon =
    confirm.parentElement.querySelector(".match-icon") ||
    confirm.parentElement.insertAdjacentHTML(
      "beforeend",
      '<i class="bi match-icon ms-1"></i>'
    );

  if (confirm.value === pass && confirm.value) {
    confirm.classList.add("field-valid");
    confirm.classList.remove("field-invalid");
    matchIcon.className =
      "bi bi-check-circle-fill text-success match-icon ms-1";
  } else if (confirm.value) {
    confirm.classList.add("field-invalid");
    confirm.classList.remove("field-valid");
    matchIcon.className = "bi bi-x-circle-fill text-danger match-icon ms-1";
  }
}

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
// APRÈS btnBackToManager.onclick = () => { ... }

// NOUVEAU (ajoute ÇA)
document.addEventListener("DOMContentLoaded", function () {
  const passwordField = document.getElementById("password");
  const confirmField = document.getElementById("password_confirm");

  if (passwordField)
    passwordField.addEventListener("input", validatePasswordLive);
  if (confirmField)
    confirmField.addEventListener("input", validatePasswordConfirm);
});

function validatePasswordLive() {
  const pass = document.getElementById("password").value;
  const checks = document.querySelectorAll(".check-item");
  const fill = document.querySelector(".strength-fill");

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

function validatePasswordConfirm() {
  const pass = document.getElementById("password").value;
  const confirmField = document.getElementById("password_confirm");
  let matchIcon = confirmField.parentElement.querySelector(".match-icon");

  if (!matchIcon) {
    confirmField.parentElement.insertAdjacentHTML(
      "beforeend",
      '<i class="bi match-icon ms-1"></i>'
    );
    matchIcon = confirmField.parentElement.querySelector(".match-icon");
  }

  if (confirmField.value === pass && confirmField.value) {
    confirmField.classList.add("is-valid");
    matchIcon.className =
      "bi bi-check-circle-fill text-success match-icon ms-1";
  } else if (confirmField.value) {
    confirmField.classList.add("is-invalid");
    matchIcon.className = "bi bi-x-circle-fill text-danger match-icon ms-1";
  }
}

// Live password + confirm
document
  .getElementById("password")
  .addEventListener("input", validatePasswordLive);
document
  .getElementById("password_confirm")
  .addEventListener("input", validatePasswordConfirm);

function validatePasswordLive() {
  const pass = document.getElementById("password").value;
  const checks = document.querySelectorAll(".check-item");
  const fill = document.querySelector(".strength-fill");

  let score = 0;

  // 8+ chars
  if (pass.length >= 8) {
    checks[0].classList.add("valid");
    score++;
  } else checks[0].classList.remove("valid");

  // Majuscule
  if (/[A-Z]/.test(pass)) {
    checks[1].classList.add("valid");
    score++;
  } else checks[1].classList.remove("valid");

  // Chiffre
  if (/\d/.test(pass)) {
    checks[2].classList.add("valid");
    score++;
  } else checks[2].classList.remove("valid");

  // Spécial
  if (/[@$!%*?&]/.test(pass)) {
    checks[3].classList.add("valid");
    score++;
  } else checks[3].classList.remove("valid");

  fill.className = `strength-fill strength-${score}`;
}

// Live SIRET (ajoute à ton JS)
document.querySelector(".siret-input").addEventListener("input", function () {
  const val = this.value.replace(/\D/g, "");
  this.value = val;

  const valid = val.length === 14;
  this.classList.toggle("is-valid", valid);
  this.classList.toggle("is-invalid", !valid);

  const validFeedback = this.parentElement.querySelector(".validity-feedback");
  const invalidFeedback = this.parentElement.querySelector(".invalid-feedback");

  validFeedback.classList.toggle("d-none", !valid);
  invalidFeedback.classList.toggle("d-none", valid);
});

function validatePasswordConfirm() {
  const pass = document.getElementById("password").value;
  const confirm = document.getElementById("password_confirm");
  const matchIcon =
    confirm.parentElement.querySelector(".match-icon") ||
    confirm.parentElement.insertAdjacentHTML(
      "beforeend",
      '<i class="bi match-icon ms-1"></i>'
    );

  if (confirm.value === pass && confirm.value) {
    confirm.classList.add("field-valid");
    confirm.classList.remove("field-invalid");
    matchIcon.className =
      "bi bi-check-circle-fill text-success match-icon ms-1";
  } else if (confirm.value) {
    confirm.classList.add("field-invalid");
    confirm.classList.remove("field-valid");
    matchIcon.className = "bi bi-x-circle-fill text-danger match-icon ms-1";
  }
}
