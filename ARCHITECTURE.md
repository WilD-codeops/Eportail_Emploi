# 🏗️ Architecture EPortail Emploi

Documentation détaillée de l’architecture. Complète le README.

---

## 📁 Structure des dossiers (arborescence)

```
Eportail_Emploi/
├── app/
│   ├── Core/
│   │   ├── Auth.php              # Utilisateur connecté + helpers
│   │   ├── Database.php          # Connexion PDO
│   │   ├── Router.php            # Routage URL → Controller
│   │   ├── Security.php          # CSRF
│   │   ├── SessionManager.php    # Sessions sécurisées
│   │   └── Validator.php         # Outils de validation
│   └── Modules/
│       ├── Auth/
│       │   ├── AuthController.php
│       │   ├── AuthService.php
│       │   ├── AuthRepository.php
│       │   ├── AuthRegistrationService.php
│       │   ├── UserController.php
│       │   ├── UserService.php
│       │   ├── UserRepository.php
│       │   └── UserValidator.php
│       ├── Entreprise/
│       │   ├── EntrepriseController.php
│       │   ├── EntrepriseService.php
│       │   ├── EntrepriseRepository.php
│       │   └── EntrepriseValidator.php
│       ├── Offres/
│       │   ├── OffresController.php
│       │   ├── OffresService.php
│       │   ├── OffresRepository.php
│       │   └── OffresValidator.php
│       ├── Candidat/
│       │   └── ProfilCandidatRepository.php
│       └── Home/
│           └── HomeController.php
├── config/
│   ├── business.php             # Règles métier
│   ├── database.php             # Connexion DB
│   └── menus.php                # Menus par rôle
├── database/
│   └── eportailemploi.sql
├── public/
│   ├── index.php                # Entry point
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
├── views/
│   ├── layouts/
│   ├── partials/
│   ├── auth/
│   ├── users/
│   ├── entreprise/
│   ├── offres/
│   ├── home/
│   └── errors/
├── vendor/
├── composer.json
└── README.md
```

---

## 🔄 Flux d’architecture (MVC + Service + Repository)

```
1. Browser → public/index.php
2. Router → Controller@method
3. Controller → Auth + Service
4. Service → logique métier
5. Repository → requête SQL (PDO)
6. Controller → render view + variables
```

---

## 🎯 Patterns utilisés

### Service / Repository

- **Service** : règles métier, décisions, orchestration
- **Repository** : SQL + mapping des résultats

### Validator

- validation des données (format, champs obligatoires)
- retour d’erreurs métier claires

---

## 👥 Auth, rôles & permissions

| Rôle         | Accès          | Actions                         |
| ------------ | -------------- | ------------------------------- |
| Admin        | Global         | CRUD users, entreprises, offres |
| Gestionnaire | Son entreprise | supervise offres + recruteurs   |
| Recruteur    | Son profil     | CRUD offres                     |
| Candidat     | Son profil     | consulter / postuler            |

Contrôles :

- `Auth::requireLogin()`
- `Auth::requireRole([...])`
- redirections si accès refusé

---

## 🔐 Sécurité

### CSRF

- token unique par formulaire
- validation côté serveur

### Sessions

- strict mode
- httponly
- samesite
- expiration d’inactivité

### SQL / XSS

- PDO préparé
- échappement dans les vues

---

## ⚠️ Erreurs, flash, pages d’erreur

- **Erreurs métier** : validation, règles, formulaire incomplet
- **Erreurs système** : DB / exception
- **Messages flash** : succès / erreur (affichés dans les vues)
- **SweetAlert2** : messages succès/erreur (connexion, permissions), confirmations de suppression, puis redirection
- **Pages d’erreur** : 403 / 500 / maintenance
- **Redirections** après actions importantes

---

## 🧩 Règles métier importantes

- Une entreprise est encadrée par un **gestionnaire**
- Limites de comptes par entreprise (configurable)
- Offres toujours liées à une entreprise

Ces règles sont appliquées dans les **Services**.

---

## 🔁 Transactions & cohérence

Actions multi‑étapes :

- création entreprise + utilisateurs associés
- suppression avec dépendances

Le code est structuré pour permettre des **transactions PDO**.

---

## 🗄️ Base de données (résumé)

Tables principales :

- users
- entreprises
- offres
- types_offres, localisations

Relations :

- entreprise → users
- entreprise → offres

---

## ⚡ AJAX (offres publiques)

- formulaire intercepté en JS
- `fetch()` vers `/offres/partial`
- HTML partiel renvoyé
- pagination dynamique

---

## 🚀 Roadmap (préparation framework)

Objectif : préparer une migration **Laravel / Symfony**

- architecture déjà découpée
- validation centralisée
- routes propres
- templates modulaires

Améliorations futures :

- API REST
- tests unitaires
- transactions systématiques
- authentification avancée
