# ISI BURGER - Projet d'examen Laravel MVC

Application web de gestion des commandes pour ISI BURGER, realisee avec Laravel 12 selon le modele MVC.

## Fonctionnalites couvertes

- Catalogue client sans authentification
- Filtrage des burgers par nom/libelle et prix
- Detail d'un burger (image, description, prix, disponibilite)
- Passage de commande multi-produits (nom + telephone obligatoires)
- Gestion du stock en temps reel
- Blocage des commandes si stock insuffisant
- Espace gestionnaire unique (`/admin`) sans systeme de roles
- CRUD burgers (ajout, modification, archivage/suppression)
- Gestion des commandes (liste, detail, annulation, changement de statut)
- Paiement en especes, unique par commande
- Historique des paiements (date + montant)
- Generation automatique facture PDF au statut `Prete`
- Dashboard statistiques en temps reel
- Graphiques Chart.js:
  - Nombre de commandes par mois
  - Nombre de produits vendus par categorie et par mois

## Statuts commande

- `pending` -> En attente
- `preparing` -> En preparation
- `ready` -> Prete (genere la facture PDF)
- `paid` -> Payee
- `canceled` -> Annulee

## Architecture (MVC)

### Modeles

- `Category`
- `Burger`
- `Order`
- `OrderItem`
- `Payment`

### Controleurs

- Client:
  - `Client\\CatalogController`
  - `Client\\OrderController`
- Admin:
  - `Admin\\DashboardController`
  - `Admin\\BurgerController`
  - `Admin\\OrderManagementController`

### Services metier

- `OrderPlacementService`: creation commande transactionnelle + decrement stock
- `InvoiceService`: generation facture PDF
- `ClientNotificationService`: simulation notification client (logs)

## Prerequis

- PHP 8.2+
- Composer
- SQLite (par defaut) ou autre base compatible Laravel

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

## Base de donnees

L'application est configuree sur SQLite par defaut.

```bash
# creer le fichier sqlite si besoin
# (Windows PowerShell)
New-Item -ItemType File -Force -Path database\database.sqlite

php artisan migrate --seed
```

## Lancer le projet

```bash
php artisan serve
```

Acces:

- Catalogue client: `http://127.0.0.1:8000/catalogue`
- Espace gestionnaire: `http://127.0.0.1:8000/admin`

## Flux principal

1. Le client ouvre `/catalogue`
2. Il choisit des quantites, saisit nom + telephone, puis valide
3. La commande est creee en `En attente`
4. Le gestionnaire suit la commande dans `/admin/orders`
5. Au passage en `Prete`, la facture PDF est generee automatiquement
6. Le paiement en especes est enregistre une seule fois

## Donnees de demo

Le seeder `BurgerCatalogSeeder` cree:

- categories: Classiques, Gourmets, Vegetariens
- plusieurs burgers avec stock initial

## Tests

```bash
php artisan test
```

## Notes metier appliquees

- Pas de login/register client
- Un burger en rupture ne peut pas etre commande
- Un paiement unique par commande (contrainte + logique applicative)
- Facture PDF uniquement a partir du statut `Prete`
- Statistiques calculees depuis la base en temps reel
