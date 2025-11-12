# MiniBlog - Projet Symfony 7

Application de blog complète développée avec Symfony 7 et Twig, mettant en œuvre les bonnes pratiques de développement web.

## Objectif

MiniBlog est une application full-stack de gestion de blog permettant aux utilisateurs de :
- Créer, modifier et supprimer des articles
- Commenter les articles
- Gérer les autorisations avec un système de rôles (USER/ADMIN)
- Naviguer dans une interface responsive et intuitive

## Stack technique

- **Framework** : Symfony 7.3
- **Langage** : PHP 8.2+
- **Template Engine** : Twig
- **Base de données** : SQLite
- **Authentification** : Form Login avec hashage des mots de passe
- **Sécurité** : Voters pour la gestion fine des permissions, protection CSRF
- **Design** : CSS pur, responsive, mobile-first

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- Extension PHP `pdo_sqlite` et `sqlite3` activées

## Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd miniblog
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Le fichier `.env.local` est déjà configuré pour utiliser SQLite :

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

### 4. Créer la base de données et exécuter les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test (fixtures)

```bash
php bin/console doctrine:fixtures:load
```

Cette commande créera :
- 2 utilisateurs de test
- 12 articles
- Des commentaires sur les premiers articles

### 6. Lancer le serveur de développement

```bash
symfony serve -d
# ou
php -S localhost:8000 -t public
```

L'application est accessible à : `http://localhost:8000`

## Comptes de test

### Administrateur
- **Email** : `admin@example.com`
- **Mot de passe** : `Admin123!`
- **Rôles** : ROLE_ADMIN, ROLE_USER

### Utilisateur standard
- **Email** : `user@example.com`
- **Mot de passe** : `User123!`
- **Rôles** : ROLE_USER

## Fonctionnalités

### Authentification et Autorisation
- Inscription et connexion sécurisées
- Hashage automatique des mots de passe
- Système de rôles (ROLE_USER, ROLE_ADMIN)
- Protection CSRF sur tous les formulaires

### Gestion des Articles
- **CRUD complet** : Créer, Lire, Mettre à jour, Supprimer
- **Slug automatique** : Génération à partir du titre
- **Pagination** : 10 articles par page
- **Tri** : Par date ou titre (ascendant/descendant)
- **Voters** : Les utilisateurs ne peuvent modifier/supprimer que leurs propres articles (sauf ADMIN)
- **Validation** : Titre (5-255 caractères), Contenu (min 10 caractères)

### Commentaires
- Ajout de commentaires (utilisateurs connectés uniquement)
- Affichage triés par date (plus récents en premier)
- Suppression par l'auteur ou un administrateur
- Validation : Contenu (3-1000 caractères)

### Interface utilisateur
- Design sobre et responsive
- Navigation intuitive
- Messages flash pour les retours utilisateur
- Pages d'erreur personnalisées (404, 500)
- Favicon personnalisé

## Routes principales

| Route | Méthode | Description | Accès |
|-------|---------|-------------|-------|
| `/` | GET | Page d'accueil avec liste paginée | Public |
| `/articles` | GET | Liste complète des articles | Public |
| `/articles/{slug}` | GET | Détail d'un article | Public |
| `/articles/new` | GET/POST | Créer un article | ROLE_USER |
| `/articles/{id}/edit` | GET/POST | Modifier un article | Auteur ou ROLE_ADMIN |
| `/articles/{id}/delete` | POST | Supprimer un article | Auteur ou ROLE_ADMIN |
| `/comments/{slug}/create` | POST | Ajouter un commentaire | ROLE_USER |
| `/comments/{id}/delete` | POST | Supprimer un commentaire | Auteur ou ROLE_ADMIN |
| `/login` | GET/POST | Connexion | Public |
| `/register` | GET/POST | Inscription | Public |
| `/logout` | POST | Déconnexion | Connecté |

## Architecture du projet

```
miniblog/
├── config/             # Configuration Symfony
├── migrations/         # Migrations Doctrine
├── public/
│   ├── css/
│   │   └── style.css  # Styles CSS
│   └── index.php
├── src/
│   ├── Controller/    # Contrôleurs
│   ├── Entity/        # Entités Doctrine
│   ├── Form/          # Types de formulaires
│   ├── Repository/    # Repositories Doctrine
│   ├── Security/
│   │   └── Voter/     # Voters pour les permissions
│   ├── Service/       # Services métier
│   └── DataFixtures/  # Fixtures de données
├── templates/         # Templates Twig
│   ├── article/
│   ├── security/
│   └── bundles/TwigBundle/Exception/  # Pages d'erreur
├── var/
│   └── data.db        # Base de données SQLite
└── README.md
```

## Sécurité

- ✅ Tous les mots de passe sont hashés avec l'algorithme recommandé par Symfony
- ✅ Protection CSRF sur tous les formulaires
- ✅ Validation côté serveur sur toutes les entités
- ✅ Voters pour contrôler l'accès aux ressources
- ✅ Échappement automatique des variables dans Twig
- ✅ Configuration de sécurité stricte

## Design

Le design de l'application est **sobre et responsive**, sans framework CSS externe :
- Palette de couleurs cohérente et professionnelle
- Typographie système pour des performances optimales
- Espacement basé sur un système de grille de 8 points
- Mobile-first avec breakpoints adaptés
- Transitions et animations subtiles
- Aucune dépendance externe (Dribbble/Behance)

## Tests

Pour vérifier le bon fonctionnement :

1. **Création d'article** : Connectez-vous et créez un article
2. **Modification** : Modifiez votre propre article
3. **Suppression** : Supprimez votre article
4. **Commentaires** : Ajoutez un commentaire sur un article
5. **Permissions** : Essayez de modifier un article d'un autre utilisateur (doit être refusé)
6. **Pagination** : Naviguez entre les pages
7. **Tri** : Testez le tri par date et par titre
8. **Responsive** : Testez sur mobile/tablette

## Limites connues et améliorations futures

### Limites
- Pas de gestion d'images pour les articles
- Pas d'édition des commentaires
- Pas de système de modération
- Pas de catégories/tags

### Améliorations possibles
- Upload et gestion d'images
- Système de catégories et tags
- Recherche full-text
- API REST pour une application mobile
- Système de notifications
- Brouillons d'articles
- Markdown pour la rédaction
- Tests automatisés (PHPUnit, Functional tests)

## Commandes utiles

```bash
# Nettoyer le cache
php bin/console cache:clear

# Recharger les fixtures
php bin/console doctrine:fixtures:load --no-interaction

# Voir les routes
php bin/console debug:router

# Voir les services
php bin/console debug:container

# Voir la configuration de sécurité
php bin/console debug:firewall
```

## Auteur

Projet développé avec Symfony 7 dans le cadre d'un exercice pédagogique.

## Licence

Projet éducatif - Libre d'utilisation

---

Fait avec ❤️ et Symfony 7
