
# Gestionnaire de Stock de Produits avec QR Code

Ce projet Symfony est une application de gestion de stock pour des produits, avec gestion des types de produits, catégories, marques, et mouvements de stock. Il inclut également la génération de QR codes pour chaque produit en fonction de leur numéro de série.

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :
- PHP 8.0 ou supérieur
- Composer
- Symfony CLI
- MySQL (ou toute autre base de données compatible avec Doctrine)
- Node.js (facultatif pour le support front-end)

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-utilisateur/nom-du-repo.git
cd nom-du-repo
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configuration de l'environnement

Créez un fichier `.env.local` pour surcharger les variables d'environnement par défaut. Cela inclura la configuration de la base de données :

```bash
cp .env .env.local
```

Ensuite, ouvrez le fichier `.env.local` et configurez les paramètres suivants en fonction de votre environnement de développement, notamment la connexion à la base de données :

```
### Exemple pour une base de données MySQL
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

### 4. Création de la base de données

Créez la base de données, exécutez les migrations, et ajoutez des jeux de données si nécessaire :

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations pour créer les tables
php bin/console doctrine:migrations:migrate
```

### 5. Charger les fixtures (données de test)

Si vous avez des fixtures pour pré-remplir la base de données avec des données de test, exécutez cette commande :

```bash
php bin/console doctrine:fixtures:load
```

### 6. Compilation des assets (facultatif)

Si vous utilisez Webpack Encore pour la gestion des assets front-end (CSS, JavaScript), installez les dépendances Node.js et compilez les assets :

```bash
# Installer les dépendances front-end
npm install

# Compiler les assets
npm run dev
```

### 7. Lancer le serveur de développement

Pour exécuter l'application en mode développement, utilisez la commande Symfony CLI :

```bash
symfony serve
```

Votre application sera accessible sur [http://localhost:8000](http://localhost:8000).

## Fonctionnalités

- **Gestion des produits** : Créez, modifiez et supprimez des produits. Chaque produit est lié à un type de produit, une marque et une catégorie.
- **Mouvements de stock** : Suivez les mouvements d'entrée et de sortie des produits.
- **Génération de QR codes** : Générez des QR codes uniques pour chaque produit basé sur leur numéro de série.
- **Gestion des utilisateurs** : Authentification et autorisation via Symfony Security.
- **Recherche de produits** : Recherchez des produits via leur numéro de série.

## Génération des QR codes

Le projet utilise la bibliothèque `endroid/qr-code-bundle` pour générer des QR codes. Le service `QrCodeService` est utilisé pour générer un QR code pour chaque produit à partir de son numéro de série.

### Exemple d'utilisation du QR code

Lorsque vous accédez à la page de détails d'un produit, un QR code correspondant à son numéro de série sera affiché.

## Commandes utiles

- **Créer la base de données** : `php bin/console doctrine:database:create`
- **Exécuter les migrations** : `php bin/console doctrine:migrations:migrate`
- **Charger des données de test** : `php bin/console doctrine:fixtures:load`
- **Lancer le serveur de développement** : `symfony serve`

## Configuration de sécurité

Le projet utilise Symfony Security pour l'authentification et la gestion des utilisateurs. Les utilisateurs doivent être authentifiés pour accéder aux fonctionnalités de gestion de stock. Si vous souhaitez modifier les rôles ou les routes sécurisées, consultez le fichier `security.yaml` dans `config/packages`.

## Test

Si vous avez mis en place des tests, exécutez-les avec la commande suivante :

```bash
php bin/phpunit
```

## Déploiement

Pour déployer cette application sur un serveur de production, suivez ces étapes :

1. Configurez correctement les variables d'environnement dans `.env` ou `.env.local`.
2. Exécutez `composer install --no-dev --optimize-autoloader` pour installer les dépendances sans les outils de développement.
3. Exécutez les migrations avec `php bin/console doctrine:migrations:migrate`.
4. Compilez les assets front-end avec `npm run build` (si applicable).

## Auteurs

- **DubbelF34140** - Développeur principal
