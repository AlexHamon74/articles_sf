# Projet Symfony : sf_Articles ⌨️

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=Symfony&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## Introduction 🎬

**sf_Articles** est un projet développé avec Symfony pour illustrer la création d'une application simple avec les fonctionnalités de base : création, lecture, mise à jour et suppression (CRUD) pour gérer des entités comme les articles et les catégories. Ce document vous guide dans l'installation, la configuration et l'utilisation de ce projet.

---

## Configuration du projet ⚙️

### Installation 🔧
Pour installer les dépendances du projet, exécutez la commande suivante :
```bash
composer install
```

### Utilisation de l'ORM Doctrine de Symfony 🪄
1. Creer un fichier `.env.local` à la racine du projet
Ce fichier permettra de configurer la connexion à votre base de données. Voici un exemple de configuration :
```bash
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

2. Paramètres à modifier :
    - `app` : Nom d'utilisateur de votre base de données.
    - `!ChangeMe!` : Mot de passe de votre base de données.
    - `3306` : Port utilisé par votre instance MySQL (à modifier si nécessaire).
    - `app` : Nom de la base de données.
    - `8.0.32` : Version de votre serveur MySQL (à adapter).

3. Commandes pour préparer la base de données :
    - Créez la base de données :
      ```bash
      php bin/console doctrine:database:create
      ```

    - Appliquez les migrations (structure de la base de données) :
      ```bash
      php bin/console doctrine:migration:migrate
      ```

    - Chargez les données de test :
      ```bash
      php bin/console doctrine:fixtures:load
      ```

### Lancez le serveur 💻
Pour lancer le serveur local et accéder à votre projet :
```bash
symfony serve --no-tls
```
---

## Fonctionnalitées 🔎

### CRUD Articles 📝
Le projet comprend deux entités principales : Article et Category.
#### 1. Création des entités
Pour générer une entité, utilisez la commande suivante :
```bash 
php bin/console make:entity
```
Lors de la création des entités :
    - Définissez les propriétés (par exemple, title et content pour Article).
    - Ajoutez une relation OneToMany entre Article et Category.

#### 2. Enregistrement des entités en base de données
    - Générez les fichiers de migration (représentation SQL des entités) :
```bash 
php bin/console make:migration
```

    - Exécutez les migrations pour appliquer les changements :
```bash
php bin/console d:m:m
```

#### 3. Création des contrôleurs
Créez un contrôleur pour gérer vos entités :
```bash
php bin/console make:controller nom_controller
```

Notre contrôleur dispose d'un moyen de récupérer la collection d'articles, et peut la transmettre à la vue.
Ceci s'effectue avec la méthode render
```bash
#[Route('/article', name: 'app_article_list')]
    public function article_list(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render('article/article_list.html.twig', [
            'articles' => $articles,
        ]);
    }
```

#### 4. Gestion des formulaires
Pour créer ou modifier des entités, générez des formulaires avec la commande suivante :
```bash
php bin/console make:form
```
Par exemple, pour un formulaire basé sur l'entité Article, Symfony génère un fichier similaire à celui-ci :  
[Exemple de génération de formualire](src/Form/ArticleType.php).  
Il nous reste plus qu'a faire les vérification dans le controller et afficher le formulaire dans la vue twig.

---

### Upload d'image/fichier 🖼️
[Documentation de VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle/blob/master/docs/index.md).
#### 1. Installation
```bash
composer require vich/uploader-bundle
```

#### 2. Configurez le fichier `vich_uploader.yaml` généré après l'installation. Définissez :
    - L'emplacement où les fichiers seront enregistrés.
    - Les entités qui utiliseront l'upload.

#### 3. Modifications des entités
Ajoutez les champs nécessaires dans vos entités. Exemple pour une entité Article :
```bash
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[Vich\Uploadable]
class Article
{
    #[Vich\UploadableField(mapping: 'articles', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    // Générer les getters et setters pour cette propriété
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
```

#### 4. Modification du formualire
Dans votre formulaire, modifiez le champ lié à l'image pour utiliser le FileType :
```bash
use Symfony\Component\Form\Extension\Core\Type\FileType;
->add('imageFile', FileType::class, [
    'required' => false
])
```
#### 5. Affichage dans twig
Pour afficher l'image dans votre template Twig :  
`<img src="{{ vich_uploader_asset(article, 'imageFile') }}" alt="">
`

---

### Mailer 🔗
Exemple d'envoi d'un email de confirmation  
**Pré-requis**  
Utilisez [Mailtrap](https://mailtrap.io/) ou un autre service d'email pour vos tests.
#### 1. Création de l'entité et du contrôleur
    - Créez une entité `Contact` avec les champs nécessaires (ex. : nom, email, message).
    - Créez un contrôleur pour gérer l'envoi d'emails.

#### 2. Configuration du controller
Ajoutez la logique d'envoi d'email :
```bash
$email = (new Email())
    ->from('noreply@exemple.com')
    ->to($contact->getEmail())
    ->subject('Merci pour votre message.')
    ->text("Bonjour, merci de nous avoir contactés. Nous vous répondrons dans les plus brefs délais.");
$mailer->send($email);
```
#### 3. Configuration de `messenger.yaml`  
Si l'envoi des emails est bloqué à cause de la gestion asynchrone, modifiez le fichier [messenger.yaml](config/packages/messenger.yaml)

---

### Securité 🔒
#### Création des fonctionnalités de base
##### 1. Enregistrement d'utilisateur
```bash
php bin/console make:user
php bin/console make:registration-form
```

##### 2. Connexion
```bash
php bin/console make:security:form-login
```

#### Controle d'accès
Modifiez le fichier [security.yaml](config/packages/security.yaml) pour configurer les règles d'accès :
```bash
access_control:
    # - { path: ^/admin, roles: ROLE_ADMIN }
    # - { path: ^/profile, roles: ROLE_USER }
    - { path: ^/login, roles: PUBLIC_ACCESS }
    - { path: ^/register, roles: PUBLIC_ACCESS }
    - { path: ^/reset-password, roles: PUBLIC_ACCESS }
    - { path: ^/, roles: IS_AUTHENTICATED_FULLY }
```

---

### Mot de passe oublié 🤔
Pour ajouter la fonctionnalité de réinitialisation de mot de passe, utilisez le bundle dédié. Consultez la documentation officielle de [reset-password-bundle](https://github.com/SymfonyCasts/reset-password-bundle).

---

## Conclusion 📌
Ce projet est une introduction pratique à Symfony et offre un socle solide pour développer des fonctionnalités 
avancées. N'hésitez pas à explorer les concepts de Symfony pour personnaliser et enrichir cette application. 🎉