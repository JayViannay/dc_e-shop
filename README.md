#### 📌 Installation du projet

- Cloner le répértoire en local
- Dans le terminal, à la racine du projet puis éxécuter les commandes suivantes :
  - `composer install`
  - `symfony server:start`
- Dans le navigateur se rendre à l'url suivante pour accéder au projet : https://localhost:8000

🎈 tips : Le dossier `_ressources` peut contenir de la documentation et des ressources complémentaires pour chacune des étapes du projet

# 🧮 Étape 2 : Afficher une première liste d'article

### 🕹 I : Afficher la liste des références sur '/' home
- Créer un nouveau controller nommé `ArticleController`à l'aide de la commande `bin/console make:controller ArticleController`
- Dans `ArticleController`, observer le code généré par symfony puis le modifier en fonction du besoin : 
  - remplacer `#[Route('/articles', name: 'app_articles')]` par `#[Route('/', name: 'app_articles')]` afin de faire pointer la liste des article vers l'url '/' 
  - Faire une injection de dépendance pointant ReferenceRepository sur la méthode `index` : `public function index(ReferenceRepository $referenceRepository): Response` 
  - Puis remplacer `'controller_name' => 'ArticlesController',` par `'articles' => $referenceRepository->findAll(),` pour passer la liste des articles à la vue grâce à la méthode de referenceRepository::findAll() qui permet de récupérer la liste de tous les articles



### 🕹 II : Ajouter bootstrap

- Intégrer le cdn bootstrap (templates/base.html.twig)

- Installer le bundle fixtures pour peupler une base de données rapidement : [fixtures bundle documentation]('https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html')

- Installer Faker : [documentation]('https://fakerphp.github.io/')

- Créer des fixtures pour peupler la base de données.