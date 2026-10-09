
## RESTful Authentication API — Laravel Sanctum

Une API REST robuste, sécurisée et extensible d'authentification et d'autorisation, développée avec Laravel 11, Laravel Sanctum et Spatie Laravel-Permission.

# Fonctionnalités principales

 Authentification complète : Inscription, Connexion, Déconnexion (révocation de jeton), Profil utilisateur.

 Jetons de sécurité (Tokens) : Authentification basée sur les jetons Bearer grâce à Laravel Sanctum.

 Réinitialisation de mot de passe : Envoi de code OTP à 6 chiffres par e-mail avec temps d'expiration.

 Gestion des Rôles & Permissions : Contrôle d'accès basé sur les rôles (RBAC) via spatie/laravel-permission.

 Validation stricte : Isolation de la logique de validation avec des FormRequest dédiées.

 Prêt pour le Multi-plateforme : Conçu pour s'interfacer facilement avec React, Vue, Angular, Flutter, React Native, etc.

#Stack Technique

Framework: Laravel 11.x

Authentification: Laravel Sanctum

Autorisation / RBAC: Spatie Laravel-Permission

Base de données: MySQL / PostgreSQL / SQLite

Envoi de Mails: Mailtrap (Environnement de développement)

 Installation et Configuration

Préréquis

PHP >= 8.2

Composer

MySQL ou PostgreSQL

Node.js & NPM (optionnel)

1. Cloner le dépôt

git clone https://github.com/fari-code/Authentification-laravel-api.git
cd votre-projet-api


2. Installer les dépendances PHP

composer install


3. Configuration de l'environnement

Copiez le fichier d'environnement et générez la clé d'application :

cp .env.example .env
php artisan key:generate


Éditez le fichier .env pour configurer l'accès à votre base de données et votre service de messagerie (ex: Mailtrap) :

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_votre_bdd
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@votreapp.com"
MAIL_FROM_NAME="${APP_NAME}"


4. Exécuter les migrations et le Seed

Exécutez la migration des tables (incluant les tables Sanctum et Spatie Permission) ainsi que les seeders pour initialiser les rôles :

php artisan migrate --seed


5. Démarrer le serveur local

php artisan serve


L'API sera accessible sur http://localhost:8000.

 Documentation des Endpoints API

 En-têtes requis pour toutes les requêtes :

Accept: application/json

Content-Type: application/json

 Routes Publiques

 Inscription Utilisateur

Endpoint: POST /api/register

Corps de la requête (JSON):

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}


Réponse (201 Created):

{
  "message": "Utilisateur créé avec succès",
  "user": { ... },
  "token": "1|Qx8z..."
}


2. Connexion

Endpoint: POST /api/login

Corps de la requête (JSON):

{
  "email": "john@example.com",
  "password": "Password123!"
}


3. Demande de réinitialisation de mot de passe (Envoi de code)

Endpoint: POST /api/forgot-password

Corps de la requête (JSON):

{
  "email": "john@example.com"
}


4. Validation du code et changement de mot de passe

Endpoint: POST /api/reset-password

Corps de la requête (JSON):

{
  "email": "john@example.com",
  "code": "123456",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}


 Routes Protégées (Nécessite Authorization: Bearer <token>)

1. Consulter le profil connecté

Endpoint: GET /api/profile

2. Déconnexion (Révocation du jeton courant)

Endpoint: POST /api/logout

 Gestion des Erreurs de Validation (422)

En cas d'erreur lors des saisies dans les formulaires, l'API retourne un code de statut 422 Unprocessable Entity uniformisé :

{
  "message": "Les données fournies sont invalides.",
  "errors": {
    "email": [
      "Cet adresse email est déjà utilisée."
    ],
    "password": [
      "La confirmation du mot de passe ne correspond pas."
    ]
  }
}


 Licence

Ce projet est sous licence MIT.

