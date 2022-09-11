#### 📌 Installation du projet

- Cloner le répértoire en local
- Dans le terminal, à la racine du projet puis éxécuter les commandes suivantes :
  - `composer install`
  - `symfony server:start`
- Dans le navigateur se rendre à l'url suivante pour accéder au projet : https://localhost:8000

🎈 tips : Le dossier `_ressources` peut contenir de la documentation et des ressources complémentaires pour chacune des étapes du projet

# 🧮 Étape 1 : Base de données

### 🕹 I : Créer la base de données
- Explorer le fichier .env
- Créer et connecter le projet à une base de données nommée `dc_e_shop` :
  - Créer un fichier `.env.local` puis ajouter la ligne suivante avec vos propres identifiants de bdd
  `DATABASE_URL="mysql://user_name:password@127.0.0.1:3306/dc_e_shop?serverVersion=8&charset=utf8mb4"`
- Dans le terminal, à la racine du projet, éxécuter la commande suivante pour créer la base de données `dc_e_shop`: `bin/console doctrine:database:create`
- Modéliser la base de données suivante à l'aide de Doctrine :
  ![Schéma bdd](./_ressources/schema_bdd_part_1.png)

### 🕹 II : Peupler la base de données

- Installer le bundle fixtures pour peupler une base de données rapidement : [fixtures bundle documentation]('https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html')

- Installer Faker : [documentation]('https://fakerphp.github.io/')

- Créer des fixtures pour peupler la base de données.