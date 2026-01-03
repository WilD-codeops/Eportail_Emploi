# 📘 **README — EPortail Emploi**

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

## 🔐 Module Auth (Authentification & Rôles)

Fonctionnalités :

- Inscription candidat
- Inscription entreprise
- Connexion / déconnexion
- Gestion des sessions sécurisées
- Redirection automatique selon le rôle
- Vérification des permissions avant chaque action

Rôles gérés :

- Visiteur
- Candidat
- Recruteur
- Gestionnaire
- Administrateur

---

## 🏠 Module Home (Pages publiques)

- Page d’accueil
- Présentation du service
- Accès aux offres publiques
- Redirection selon le rôle si connecté

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
        /Entreprise
        /Offres
        /Home
        (Candidat, Candidatures, prévus)
/config
    database.php
    menu.php
/public
    index.php
/views
    /layouts
    /auth
    /entreprise
    /offres
    /home
/database
    eportailemploi.sql
/vendor
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

Le rendu se fait via des méthodes personnalisées dans les controllers :

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

Il contient la base de données.

---

# ⚡ 9. Installation rapide

```bash
git clone https://github.com/<ton-repo>/eportail-emploi.git
cd eportail-emploi
composer install
```

1. Configurer `config/database.php`
2. Importer `/database/eportailemploi.sql`
3. Configurer Apache pour pointer vers `/public`
4. Lancer le serveur

---

# 📘 10. Installation détaillée

### 1. Cloner le projet

### 2. Installer les dépendances Composer

### 3. Configurer la base de données

### 4. Importer le fichier SQL

### 5. Configurer un VirtualHost (recommandé)

### 6. Vérifier les permissions

### 7. Accéder à l’application via :

```
http://localhost/eportail-emploi
```

---

# 🧩 11. Modules existants

- Auth
- Entreprise
- Offres
- Home

# 🧱 Modules prévus

- Candidat
- Candidatures

---

# 🖼️ 12. Aperçu (captures d’écran)

---

# 🚧 13. Limites actuelles

- Certains modules non finalisés
- Pas encore d’API REST
- Pas de système de messagerie interne
- Pas de gestion avancée des permissions fines

---

# 🚀 14. Améliorations futures

- Entités
- API REST
- PhpUnit
- PhpMailer
- Application mobile
- Système de notifications internes
- Commentaires sur les candidatures
- Tableau de bord avancé
- Optimisation des performances

---

# 👤 15. Auteur

**Wildane MADI**  
Certification **RNCP 37273 – Développeur Web & Web Mobile**  
Projet réalisé en 2025–2026

---

# 🎓 16. Contexte pédagogique

Ce projet a été réalisé dans le cadre de la certification RNCP 37273.  
Il démontre :

- la maîtrise d’une architecture MVC modulaire
- la capacité à concevoir une base de données professionnelle
- la mise en œuvre de bonnes pratiques de sécurité
- l’utilisation de Git et Composer
- la capacité à structurer un projet complet

---
