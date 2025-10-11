# 🧾 Système de Gestion de Factures

Une application web complète de gestion de factures développée avec Symfony 7, permettant de gérer les clients, les factures et les lignes de factures avec une interface moderne et responsive.

## ✨ Fonctionnalités

### 📊 Dashboard
- Chiffre d'affaires du mois courant
- Nombre de factures en attente
- Liste des 5 dernières factures
- Actions rapides

### 👥 Gestion des Clients
- CRUD complet (Créer, Lire, Modifier, Supprimer)
- Validation des champs (nom obligatoire, email unique)
- Recherche en temps réel
- Informations complètes (adresse, téléphone, numéro TVA)

### 🧾 Gestion des Factures
- CRUD complet avec calcul automatique des totaux
- Référence auto-générée (FAC-AAAA-0001)
- Gestion des statuts (Brouillon, Validée, Payée)
- Filtrage avancé (client, état, date)
- Export PDF professionnel
- Duplication de factures

### 📋 Lignes de Facture
- Ajout/suppression dynamique
- Calcul automatique des totaux HT/TTC
- Gestion de la TVA par ligne
- Interface intuitive avec JavaScript

### 🔐 Sécurité
- Authentification basique avec Symfony Security
- Protection des routes
- Gestion des sessions

## 🛠️ Stack Technique

- **Backend**: Symfony 7 (PHP 8.2+)
- **Base de données**: MySQL avec Doctrine ORM
- **Frontend**: Twig + Bootstrap 5
- **JavaScript**: Axios pour AJAX
- **PDF**: DomPDF pour l'export
- **Sécurité**: Symfony Security Bundle

## 📦 Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL 8.0 ou supérieur
- Symfony CLI (optionnel)

### 1. Cloner le projet
```bash
git clone <url-du-repo>
cd faichraq
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configuration de l'environnement
Créez un fichier `.env.local` à la racine du projet :
```env
# Configuration de la base de données
DATABASE_URL="mysql://root:@127.0.0.1:3306/factures_db?serverVersion=8.0.32&charset=utf8mb4"

# Configuration de l'application
APP_ENV=dev
APP_SECRET=your-secret-key-here-change-in-production

# Configuration du serveur mail (optionnel)
MAILER_DSN=smtp://localhost:1025

# Configuration DomPDF
DOMPDF_ENABLE_REMOTE=true
DOMPDF_ENABLE_AUTOLOAD=true
```

### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
```

### 5. Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 6. Charger les données de test
```bash
php bin/console doctrine:fixtures:load
```

### 7. Démarrer le serveur de développement
```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

## 👤 Connexion

Une fois l'application démarrée, vous pouvez vous connecter avec :
- **Email**: `admin@example.com`
- **Mot de passe**: `admin123`

## 📁 Structure du Projet

```
src/
├── Controller/
│   ├── DashboardController.php      # Contrôleur du tableau de bord
│   ├── FactureController.php        # Gestion des factures
│   ├── LigneFactureController.php   # Gestion des lignes
│   ├── SecurityController.php       # Authentification
│   └── TiersController.php          # Gestion des clients
├── Entity/
│   ├── Facture.php                  # Entité Facture
│   ├── LigneFacture.php             # Entité LigneFacture
│   ├── Tiers.php                    # Entité Client
│   └── User.php                     # Entité Utilisateur
├── Form/
│   ├── FactureType.php              # Formulaire Facture
│   ├── LigneFactureType.php         # Formulaire LigneFacture
│   └── TiersType.php                # Formulaire Client
├── Repository/
│   ├── FactureRepository.php        # Repository Facture
│   ├── LigneFactureRepository.php   # Repository LigneFacture
│   └── TiersRepository.php          # Repository Client
├── Service/
│   └── ReferenceGenerator.php       # Générateur de références
└── DataFixtures/
    └── AppFixtures.php              # Données de test

templates/
├── base.html.twig                   # Template de base
├── dashboard/
│   └── index.html.twig              # Page d'accueil
├── facture/
│   ├── index.html.twig              # Liste des factures
│   ├── new.html.twig                # Nouvelle facture
│   ├── edit.html.twig               # Modifier facture
│   ├── show.html.twig               # Détails facture
│   ├── pdf.html.twig                # Template PDF
│   └── _list.html.twig              # Liste partielle
├── security/
│   └── login.html.twig              # Page de connexion
└── tiers/
    ├── index.html.twig              # Liste des clients
    ├── new.html.twig                # Nouveau client
    ├── edit.html.twig               # Modifier client
    ├── show.html.twig               # Détails client
    └── _list.html.twig              # Liste partielle
```

## 🚀 Utilisation

### Navigation
- **Dashboard**: Vue d'ensemble avec statistiques
- **Clients**: Gestion des clients (tiers)
- **Factures**: Gestion des factures

### Créer une facture
1. Aller dans "Factures" > "Nouvelle facture"
2. Sélectionner un client
3. Ajouter des lignes de facture
4. Les totaux se calculent automatiquement
5. Sauvegarder

### Export PDF
- Depuis la vue d'une facture, cliquer sur "Télécharger PDF"
- Le PDF est généré avec un design professionnel

### Recherche et filtres
- Utiliser les filtres sur les pages de liste
- Recherche en temps réel avec AJAX
- Filtrage par client, état, date

## 🔧 Commandes Utiles

```bash
# Vider le cache
php bin/console cache:clear

# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load

# Créer un utilisateur
php bin/console make:user

# Voir les routes
php bin/console debug:router
```

## 🎨 Personnalisation

### Modifier le design
- Les templates utilisent Bootstrap 5
- Personnalisez les styles dans `templates/base.html.twig`
- Modifiez les couleurs et la charte graphique

### Ajouter des fonctionnalités
- Créez de nouveaux contrôleurs avec `php bin/console make:controller`
- Ajoutez de nouvelles entités avec `php bin/console make:entity`
- Créez des formulaires avec `php bin/console make:form`

## 🐛 Dépannage

### Problèmes courants

**Erreur de base de données**
```bash
# Vérifier la configuration dans .env.local
# Recréer la base de données
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**Problème de permissions**
```bash
# Donner les permissions d'écriture
chmod -R 755 var/
chmod -R 755 public/
```

**Cache corrompu**
```bash
# Vider le cache
php bin/console cache:clear --env=prod
```

## 📝 Données de Test

Le système inclut des données de test avec :
- 1 utilisateur admin
- 5 clients variés
- 5 factures avec différents statuts
- Lignes de facture complètes

## 🔒 Sécurité

- Authentification obligatoire
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur
- Échappement des sorties HTML

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📞 Support

Pour toute question ou problème :
- Ouvrir une issue sur GitHub
- Consulter la documentation Symfony
- Vérifier les logs dans `var/log/`

---

**Développé avec ❤️ en Symfony 7**

