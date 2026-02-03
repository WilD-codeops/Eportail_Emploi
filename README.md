# 📘 EPortail Emploi

Plateforme de recherche d'emploi en **PHP natif** avec **architecture MVC modulaire**.  
Projet réalisé dans le cadre du **titre RNCP 37273 – Développeur Web & Web Mobile**.

---

## 🎯 Objectif

Mettre en relation **candidats**, **entreprises** et **recruteurs** via un back-office sécurisé et des pages publiques simples.

---

## ✅ Points clés

- **MVC modulaire** avec séparation Controller / Service / Repository / Validator
- **Rôles & permissions** (admin, gestionnaire, recruteur, candidat)
- **Sécurité** : CSRF, sessions sécurisées, requêtes préparées (PDO)
- **Pagination + filtres** (y compris AJAX sur la liste des offres)
- **UX améliorée** : badges, KPI, icônes, tables claires
- **Feedback utilisateur** : SweetAlert2 (connexion, permissions, suppressions)
- **Gestion des erreurs** : flash messages, pages 403/500, redirections
- **Règles métier** : entreprise encadrée par gestionnaire
- **Cohérence** : opérations multi-étapes prêtes pour transactions
- **Roadmap** : préparation à une migration Laravel / Symfony

---

## 🧩 Modules principaux

- **Auth** : inscription, login, sessions, rôles
- **Entreprise** : gestion des entreprises + utilisateurs associés
- **Offres** : CRUD offres + filtres + pagination
- **Home** : pages publiques
- **Candidat / Candidatures** : prévus

---

## 🚀 Installation rapide

```bash
git clone https://github.com/WilD-codeops/Eportail_Emploi.git
cd eportail-emploi
composer install
```

1. Configurer **config/database.php** (vos identifiants MySQL)
2. Importer la base : **/database/eportailemploi.sql**
3. Configurer Apache pour pointer vers **/public**
4. Redémarrer Apache

---

## 🗄️ Base de données

Le fichier SQL est ici : `/database/eportailemploi.sql`

> Vous pouvez utiliser votre propre base : adaptez uniquement **config/database.php**.

---

## 📋 Configuration VirtualHost (WAMP)

### Fichier Apache à modifier :

`C:\wamp64\conf\extra\httpd-vhosts.conf`

### Ajouter cette configuration :

```apache
<VirtualHost *:80>
    ServerName eportail-emploi.local
    ServerAlias www.eportail-emploi.local
    DocumentRoot "C:/wamp64/www/Eportail_Emploi/public"

    <Directory "C:/wamp64/www/Eportail_Emploi/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Fichier hosts (Windows) :

Ajouter dans `C:\Windows\System32\drivers\etc\hosts` :

```
127.0.0.1    eportail-emploi.local
127.0.0.1    www.eportail-emploi.local
```

### Redémarrer Apache :

WAMP → Apache → Restart Service

### Accéder à l'application :

```
http://eportail-emploi.local
```

---

## 👥 Comptes de test

### Administrateur

- Email : `admin@site.fr`
- Mot de passe : `hashpwdadmin`

-Email : `admin@test.com`

- Mot de passe : `Admin!123`

### Gestionnaires

- Email : `paul.martin@santeplus.fr` | Mot de passe : `paulmartin`
- Email : `lucas.morel@techcorp.fr` | Mot de passe : `Lucasmorel!2026`

### Recruteur

- Email : `marie.durand@techcorp.fr`
- Mot de passe : `mariedurand`

### Candidat

- Email : `jean.dupont@exemple.com`
- Mot de passe : `jeandupont`

---

## 🔄 Flux utilisateurs importants

- **Créer une offre** : Dashboard → formulaire → validation → enregistrement → retour liste
- **Filtrer les offres** : filtres → AJAX → pagination dynamique
- **Gérer une entreprise** : Admin → liste → modifier / supprimer
- **Gestion des rôles** : Admin → users → affectation rôle / entreprise

---

## 📚 Documentation détaillée

Consulter **ARCHITECTURE.md** pour :

- Fonctionnement MVC détaillé et flux complets
- Rôles & permissions avec règles métier
- Sécurité (CSRF / sessions / authentification)
- Système de vues et routing
- Schéma Base de Données avec explications
- Gestion des erreurs et flash messages
- Transactions et opérations multi-étapes
- Roadmap (préparation Laravel/Symfony)

---

## 👤 Auteur

**Wildane MADI**  
Certification **RNCP 37273 – Développeur Web & Web Mobile**  
Projet réalisé en 2025–2026
