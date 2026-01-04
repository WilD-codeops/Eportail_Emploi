# 📘 README — EPortail Emploi

Plateforme de recherche d’emploi — PHP natif (Architecture MVC Modulaire)

---

# 🧭 1. Présentation du projet

**EPortail Emploi** est une plateforme web permettant la mise en relation entre :

- des **candidats** en recherche d’emploi
- des **entreprises** souhaitant recruter
- des **recruteurs** et **gestionnaires** internes
- un **administrateur** chargé de superviser la plateforme

Le projet a été développé dans le cadre du **titre RNCP 37273 – Développeur Web & Web Mobile**, avec l’objectif de produire une application :

- modulaire
- sécurisée
- évolutive
- structurée comme un pré‑framework PHP

L’application repose sur une **architecture MVC modulaire**, où chaque module représente un domaine métier (Auth, Entreprise, Offres, etc.).

---

# ⚙️ 2. Fonctionnalités principales (par modules et par rôles)

La plateforme est organisée en **modules métiers**, chacun correspondant à un domaine fonctionnel.  
L’accès aux fonctionnalités dépend du **rôle de l’utilisateur**, géré via le module Auth et les routes déclarées dans `public/index.php`.

---

## 🔐 Module Auth (Authentification, Rôles & Gestion des utilisateurs)

Fonctionnalités :

- Inscription candidat
- Inscription entreprise
- Connexion / déconnexion
- Gestion des sessions sécurisées
- Vérification des permissions
- Redirection automatique selon le rôle

**Gestion des utilisateurs (Admin) :**

- Liste des utilisateurs
- Création / modification / suppression
- Attribution des rôles
- Association des utilisateurs à une entreprise (recruteurs / gestionnaires)

---

## 🏠 Module Home (Pages publiques)

- Page d’accueil
- Présentation du service
- Redirection selon le rôle si connecté
- Pages publiques statiques

---

## 🏢 Module Entreprise

### Public :

- Liste des entreprises
- Consultation des fiches entreprises

### Privé :

**Gestionnaire :**

- Modifier les informations de l’entreprise
- Créer / gérer les recruteurs
- Superviser les offres et candidatures

**Administrateur :**

- CRUD complet sur les entreprises
- Gestion des utilisateurs associés

---

## 💼 Module Offres

### Public :

- Liste des offres
- Filtres simples
- Consultation d’une offre

### Privé :

**Recruteur :**

- Création / modification / suppression d’offres
- Gestion des candidatures reçues

**Candidat :**

- Postuler
- Ajouter aux favoris

**Gestionnaire :**

- Supervision des offres de l’entreprise

**Admin :**

- Accès total

---

## 👤 Module Candidat _(prévu)_

- Gestion du profil
- CV / documents
- Suivi des candidatures
- Favoris

---

## 📄 Module Candidatures _(prévu)_

- Statuts : reçue → en cours → acceptée / refusée
- Historique
- Commentaires internes (future évolution)

---

# 🧱 3. Technologies utilisées

- PHP 8+
- MySQL / MariaDB
- Composer (autoload PSR‑4)
- Bootstrap 5
- JavaScript
- Apache (WAMP/XAMPP/MAMP)

---

# 🏗️ 4. Architecture du projet

Le projet suit une architecture **MVC modulaire**, inspirée des frameworks modernes.

```

/app
    /Core
        Router.php
        Database.php
        Security.php
        SessionManager.php
    /Modules
        /Auth
            AuthController.php
            AuthRepository.php
            AuthService.php
            AuthValidator.php
        /Entreprise
            EntrepriseController.php
            EntrepriseRepository.php
            EntrepriseService.php
            EntrepriseValidator.php
        /Offres
            OffresController.php
            OffresRepository.php
            OffresService.php
            OffresValidator.php
        /Home
            HomeController.php
        (Candidat, Candidatures — prévus)
/config
    database.php
    menu.php
/public
    /assets
        /css
        /js
    index.php
/views
    /layouts
        main.php
        auth.php
        dashboard.php
    /partials
    /auth
    /entreprise
    /errors
    /offres
    /home
/database
    eportailemploi.sql
/vendor
    /composer
composer.json
README.md


```

---

# 🚦 5. Routing

Les routes sont déclarées dans :

```

/public/index.php

```

Exemples :

```php
$router->get('/', 'App\\Modules\\Home\\HomeController@index');
$router->get('/login', 'App\\Modules\\Auth\\AuthController@showLogin');
$router->post('/login', 'App\\Modules\\Auth\\AuthController@login');
$router->get('/offres', 'App\\Modules\\Offres\\OffresController@index');
```

Le router maison gère :

- GET / POST
- Normalisation du path
- Mapping `Controller@method`
- Gestion 404

---

# 🖼️ 6. Système de vues

Chaque module possède ses vues dans `/views/<module>`.

Exemple de méthode de rendu :

```php
private function renderAuth(string $view, array $params = []): void
{
    extract($params);

    ob_start();
    require __DIR__ . "/../../../views/auth/{$view}.php";
    $content = ob_get_clean();

    require __DIR__ . "/../../../views/layouts/auth.php";
}
```

Layouts disponibles :

- `layouts/main.php` → pages publiques
- `layouts/auth.php` → pages d’authentification
- `layouts/dashboard.php` → back-office

---

# 🔐 7. Sécurité

Le projet intègre plusieurs mesures de sécurité :

### ✔️ Sessions sécurisées

- `session.use_strict_mode`
- `httponly`
- `samesite=strict`
- expiration d’inactivité
- expiration absolue
- regeneration d’ID

### ✔️ CSRF Protection

- Token unique par formulaire
- Vérification + invalidation automatique
- Protection des actions sensibles (delete)

### ✔️ Authentification

- Hashage des mots de passe
- Vérification des rôles
- Redirections sécurisées

### ✔️ Protection XSS / SQL

- PDO + requêtes préparées
- Échappement dans les vues

---

# 🗄️ 8. Base de données

Le fichier SQL se trouve dans :

```
/database/eportailemploi.sql
```

---

# ⚡ 9. Installation rapide

```bash
git clone https://github.com/WilD-codeops/Eportail_Emploi.git
cd eportail-emploi
composer install
```

1. Configurer `config/database.php`
2. Importer `/database/eportailemploi.sql`
3. Configurer Apache pour pointer vers `/public`
4. Lancer le serveur

---

# 📘 10. Installation détaillée

1. Cloner le projet
2. Installer les dépendances Composer
3. Configurer la base de données
4. Importer le fichier SQL
5. Configurer un VirtualHost
6. Vérifier les permissions
7. Accéder à l’application via :

```
http://localhost/eportail-emploi
```

---

# 👥 11. Comptes de test

### Administrateur

- Email : `admin@site.fr`
- Mot de passe : `hashpwdadmin`

### Gestionnaire

- Email : `paul.martin@santeplus.fr`
- Mot de passe : `paulmartin`

### Gestionnaire

- Email : `lucas.morel@techcorp.fr`
- Mot de passe : `lucasmorel!2026`

### Recruteur

- Email : `marie.durand@techcorp.fr`
- Mot de passe : `mariedurand`

### Candidat

- Email : `jean.dupont@example.com`
- Mot de passe : `jeandupont`

---

# 🔄 12. Rechargement AJAX (Module Offres)

Le module **Offres** utilise un rechargement AJAX pour mettre à jour la liste (table + pagination) sans recharger toute la page.

Principe :

- Le JavaScript intercepte les clics de pagination et les filtres
- Une requête `fetch()` est envoyée vers :  
  `/admin/offres/partial` ou `/dashboard/offres/partial`
- Le contrôleur renvoie un **fragment HTML** (`_results.php`)
- Le JS remplace le contenu du bloc `#offres-results`

Ce choix permet une navigation fluide tout en conservant les vues PHP du MVC.

---

# 🧾 13. Conventions de commit

Le projet suit les conventions **Conventional Commits** :

- `feat:` → nouvelle fonctionnalité
- `fix:` → correction de bug
- `refactor:` → amélioration interne
- `style:` → formatage
- `docs:` → documentation
- `chore:` → maintenance

Exemples :

```
feat(offres): ajout de la pagination et des filtres
fix(ui): correction de la hauteur de la sidebar
refactor(auth): simplification de la gestion des sessions
```

---

# 🧩 14. Modules existants

- Auth
- Entreprise
- Offres
- Home

## 🧱 Modules prévus

- Candidat
- Candidatures

---

# 🖼️ 15. Aperçu (captures d’écran)

_(à compléter)_

---

# 🚧 16. Limites actuelles

- Certains modules non finalisés (Auth, Entreprise, Offres toujours en cours)
- Pas de messagerie interne
- Pas encore d’API REST

---

# 🚀 17. Améliorations futures

- Entités pour compléter les patterns
- API REST
- PhpUnit
- PhpMailer
- Application mobile
- Notifications internes
- Commentaires sur les candidatures
- Tableau de bord avancé
- Optimisation des performances

---

# 👤 18. Auteur

**Wildane MADI**  
Certification **RNCP 37273 – Développeur Web & Web Mobile**  
Projet réalisé en 2025–2026

---

# 🎓 19. Contexte pédagogique

Ce projet démontre :

- la maîtrise d’une architecture MVC modulaire
- la conception d’une base de données professionnelle
- les bonnes pratiques de sécurité
- l’utilisation de Git et Composer
- la capacité à structurer un projet complet

---

# 20. Déploiement (à venir)

Déploiement prévu sur serveur LAMP.

```

---

```
