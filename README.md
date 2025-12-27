# 📋 Guide Complet : Formulaires Symfony avec DTO

## 📚 Table des matières
- [Introduction](#introduction)
- [1. Le DTO (Data Transfer Object)](#1-le-dto-data-transfer-object)
- [2. Le Form Type](#2-le-form-type)
- [3. Le Controller](#3-le-controller)
- [4. Le Template Twig](#4-le-template-twig)
- [Flux complet des données](#flux-complet-des-données)

---

## Introduction

Ce guide explique comment créer un formulaire Symfony professionnel en utilisant un **DTO** (Data Transfer Object) pour la gestion des données. Nous avons créé un formulaire "Add to Cart" qui permet de sélectionner une quantité et une couleur de produit.

---

## 1. Le DTO (Data Transfer Object)

### 🎯 Qu'est-ce qu'un DTO ?

Un **DTO** est un objet simple qui transporte des données entre différentes couches de l'application. Il n'a pas de logique métier, juste des propriétés avec leurs getters/setters.

### ✅ Pourquoi utiliser un DTO ?

- **Séparation des responsabilités** : Le DTO ne contient que les données du formulaire
- **Validation centralisée** : Les contraintes sont définies directement dans le DTO
- **Type-safe** : Typage strict PHP pour éviter les erreurs
- **Indépendant de la base de données** : Pas besoin d'entité Doctrine
- **Réutilisable** : Peut être utilisé dans les APIs, tests, etc.

### 📝 Structure du DTO

```php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AddToCartDTO
{
    #[Assert\NotBlank(message: 'Please select a quantity')]
    #[Assert\Range(min: 1, max: 10)]
    private ?int $quantity = 1;

    #[Assert\NotBlank(message: 'Please select a color')]
    #[Assert\Choice(choices: ['black', 'white', 'silver'])]
    private ?string $color = null;

    // Getters et Setters...
}
```

### 🔍 Les Contraintes de Validation

| Contrainte | Rôle | Exemple |
|------------|------|---------|
| `@Assert\NotBlank` | Le champ ne peut pas être vide | `message: 'Ce champ est requis'` |
| `@Assert\Range` | Valeur entre min et max | `min: 1, max: 10` |
| `@Assert\Choice` | Valeur parmi une liste | `choices: ['black', 'white']` |
| `@Assert\Email` | Format email valide | `message: 'Email invalide'` |
| `@Assert\Length` | Longueur de chaîne | `min: 3, max: 100` |

### 💡 Valeurs par défaut

```php
private ?int $quantity = 1;  // Valeur par défaut : 1
```

Cette valeur sera affichée dans le formulaire lors du premier chargement.

---

## 2. Le Form Type

### 🎯 Qu'est-ce qu'un Form Type ?

Le **Form Type** est une classe qui définit la structure et le comportement du formulaire. C'est le "blueprint" du formulaire.

### 📋 Structure de base

```php
namespace App\Form;

use App\DTO\AddToCartDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Définition des champs
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configuration globale
    }
}
```

---

### 🔨 Le FormBuilder

Le **FormBuilder** est l'outil qui construit le formulaire champ par champ.

#### Syntaxe de base :

```php
$builder->add('nomDuChamp', TypeDuChamp::class, [
    'options' => 'valeurs'
]);
```

#### Exemple complet :

```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('quantity', IntegerType::class, [
            'label' => 'Quantity',
            'attr' => [
                'class' => 'form-control',
                'min' => 1,
                'max' => 10,
            ]
        ])
        ->add('color', ChoiceType::class, [
            'label' => 'Select Color',
            'choices' => [
                'Matte Black' => 'black',
                'Pearl White' => 'white',
                'Silver' => 'silver'
            ],
            'placeholder' => 'Choose a color'
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Add to Cart'
        ]);
}
```

---

### 👶 Les "Children" (Enfants)

Chaque appel à `->add()` crée un **child** (enfant) du formulaire. Dans notre exemple :

```php
$builder
    ->add('quantity', ...)    // Child 1
    ->add('color', ...)        // Child 2
    ->add('submit', ...)       // Child 3
```

**Pourquoi "children" ?** 
- Le formulaire est le parent
- Chaque champ est un enfant
- On peut accéder à un enfant : `$form->get('quantity')`

---

### 📦 Les Types de champs courants

| Type | Usage | Exemple |
|------|-------|---------|
| `TextType` | Texte simple | Nom, prénom |
| `EmailType` | Email avec validation | Email |
| `IntegerType` | Nombre entier | Quantité, âge |
| `ChoiceType` | Liste déroulante | Couleurs, pays |
| `CheckboxType` | Case à cocher | CGV, newsletter |
| `TextareaType` | Texte multiligne | Description |
| `DateType` | Date | Date de naissance |
| `SubmitType` | Bouton submit | Soumettre |

---

### ⚙️ Les Options des champs

#### Options communes à tous les champs :

```php
->add('quantity', IntegerType::class, [
    'label' => 'Quantité',              // Libellé affiché
    'required' => true,                  // Champ obligatoire (HTML5)
    'attr' => [                          // Attributs HTML
        'class' => 'form-control',
        'placeholder' => 'Ex: 5'
    ],
    'help' => 'Entre 1 et 10',          // Texte d'aide
    'mapped' => true,                    // Lié au DTO (défaut: true)
    'data' => 1                          // Valeur par défaut
])
```

#### Options spécifiques au ChoiceType :

```php
->add('color', ChoiceType::class, [
    'choices' => [                       // Liste des choix
        'Label' => 'valeur',
        'Noir' => 'black',
        'Blanc' => 'white'
    ],
    'placeholder' => 'Choisir...',      // Option vide
    'expanded' => false,                 // false = <select>, true = radio/checkbox
    'multiple' => false                  // false = un choix, true = plusieurs
])
```

---

### 🎛️ Le configureOptions()

Cette méthode configure le comportement global du formulaire.

```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => AddToCartDTO::class,  // ⭐ CRUCIAL !
    ]);
}
```

#### 🔑 `data_class` : Le lien avec le DTO

**C'est ici que la magie opère !**

En définissant `'data_class' => AddToCartDTO::class`, vous dites à Symfony :

1. **Lors de l'affichage** : Utilise le DTO pour remplir les valeurs par défaut
2. **Lors de la soumission** : Crée/remplit automatiquement un objet `AddToCartDTO`
3. **Mapping automatique** : Les champs du formulaire correspondent aux propriétés du DTO

#### Comment Symfony fait le mapping ?

```php
// Formulaire
->add('quantity', ...)
->add('color', ...)

// DTO
private ?int $quantity;   // ✅ Correspond à 'quantity'
private ?string $color;   // ✅ Correspond à 'color'
```

**Symfony utilise les setters pour remplir le DTO :**

```php
// Quand le formulaire est soumis, Symfony fait :
$dto = new AddToCartDTO();
$dto->setQuantity($formData['quantity']);
$dto->setColor($formData['color']);
```

---

## 3. Le Controller

### 🎮 Rôle du Controller

Le controller orchestre tout le processus :
1. Crée le formulaire
2. Gère la soumission
3. Valide les données
4. Traite les données
5. Affiche la vue

### 📝 Code complet expliqué

```php
namespace App\Controller;

use App\DTO\AddToCartDTO;
use App\Form\AddToCartType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product_show', methods: ['GET', 'POST'])]
    public function show(Request $request): Response
    {
        // 1️⃣ Créer une instance vide du DTO
        $addToCartDTO = new AddToCartDTO();
        
        // 2️⃣ Créer le formulaire lié au DTO
        $form = $this->createForm(AddToCartType::class, $addToCartDTO);
        
        // 3️⃣ Traiter la requête HTTP (GET ou POST)
        $form->handleRequest($request);

        // 4️⃣ Vérifier si formulaire soumis ET valide
        if ($form->isSubmitted() && $form->isValid()) {
            // 5️⃣ Le DTO contient les données validées !
            $quantity = $addToCartDTO->getQuantity();
            $color = $addToCartDTO->getColor();

            // 6️⃣ Afficher les données (debug)
            dd([
                'Quantity' => $quantity,
                'Color' => $color,
                'DTO complet' => $addToCartDTO
            ]);
        }

        // 7️⃣ Afficher le formulaire
        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

### 🔍 Explications détaillées

#### Étape 1 : Créer le DTO

```php
$addToCartDTO = new AddToCartDTO();
```

On crée une instance vide (ou avec valeurs par défaut) du DTO.

---

#### Étape 2 : Créer le formulaire

```php
$form = $this->createForm(AddToCartType::class, $addToCartDTO);
```

**Que fait `createForm()` ?**
- Instancie la classe `AddToCartType`
- Lie le formulaire au DTO
- Configure tous les champs définis dans `buildForm()`
- Prépare la validation

---

#### Étape 3 : Traiter la requête

```php
$form->handleRequest($request);
```

**Que fait `handleRequest()` ?**

**Si GET (première visite) :**
- Lit les valeurs du DTO
- Remplit le formulaire avec ces valeurs
- Affiche le formulaire vide (ou avec valeurs par défaut)

**Si POST (soumission) :**
- Récupère les données POST
- Vérifie le token CSRF
- Remplit le DTO avec les données soumises
- Exécute la validation (contraintes du DTO)

---

#### Étape 4 : Vérifier la soumission

```php
if ($form->isSubmitted() && $form->isValid()) {
    // ...
}
```

**`isSubmitted()`** : Le formulaire a-t-il été soumis ? (méthode POST)

**`isValid()`** : Les données respectent-elles toutes les contraintes ?
- Vérifie `@Assert\NotBlank`
- Vérifie `@Assert\Range`
- Vérifie `@Assert\Choice`
- etc.

**Si invalide**, Symfony garde les données et affiche les erreurs dans le formulaire.

---

#### Étape 5 : Utiliser les données

```php
$quantity = $addToCartDTO->getQuantity();
$color = $addToCartDTO->getColor();
```

**Le DTO est automatiquement rempli !** ✨

Symfony a déjà appelé :
```php
$addToCartDTO->setQuantity($_POST['add_to_cart']['quantity']);
$addToCartDTO->setColor($_POST['add_to_cart']['color']);
```

---

## 4. Le Template Twig

### 🎨 Les fonctions helper de formulaire

Twig fournit des fonctions spéciales pour afficher les formulaires facilement.

### 📋 Les fonctions principales

#### 1. `form_start(form)`

```twig
{{ form_start(form) }}
```

**Génère :**
```html
<form method="post" action="/product">
    <input type="hidden" name="_token" value="abc123...">
```

**Rôle :**
- Ouvre la balise `<form>`
- Ajoute automatiquement le token CSRF
- Configure l'action et la méthode

---

#### 2. `form_end(form)`

```twig
{{ form_end(form) }}
```

**Génère :**
```html
    </form>
```

**Rôle :**
- Ferme la balise `</form>`
- Affiche les champs non encore rendus (évite les oublis)

---

#### 3. `form_row(form.champ)`

```twig
{{ form_row(form.quantity) }}
```

**Génère :**
```html
<div>
    <label for="quantity">Quantity</label>
    <input type="number" id="quantity" name="add_to_cart[quantity]" value="1">
    <span class="error">Message d'erreur si invalide</span>
</div>
```

**Rôle :**
- Affiche le label + widget + erreurs en une seule ligne
- ⭐ **La plus utilisée !**

---

#### 4. `form_label(form.champ)`

```twig
{{ form_label(form.quantity) }}
```

**Génère :**
```html
<label for="quantity">Quantity</label>
```

**Options :**
```twig
{{ form_label(form.quantity, 'Quantité personnalisée') }}
```

---

#### 5. `form_widget(form.champ)`

```twig
{{ form_widget(form.quantity) }}
```

**Génère :**
```html
<input type="number" id="quantity" name="add_to_cart[quantity]" value="1" class="form-control">
```

**Rôle :**
- Affiche le champ de saisie (input, select, textarea, etc.)
- Applique les attributs définis dans `'attr'`

---

#### 6. `form_errors(form.champ)`

```twig
{{ form_errors(form.quantity) }}
```

**Génère (si erreur) :**
```html
<span class="error">La quantité doit être entre 1 et 10</span>
```

**Rôle :**
- Affiche les messages d'erreur de validation

---

#### 7. `form(form)`

```twig
{{ form(form) }}
```

**Génère tout le formulaire automatiquement !**

**Rôle :**
- Équivalent à : `form_start()` + tous les `form_row()` + `form_end()`
- ⚠️ Peu utilisé car moins personnalisable

---

### 🎯 Exemple complet du template

```twig
{% extends 'base.html.twig' %}

{% block body %}
    <div class="container">
        {# Afficher les messages flash #}
        {% for message in app.flashes('success') %}
            <div class="alert alert-success">{{ message }}</div>
        {% endfor %}

        <h1>Premium Wireless Headphones</h1>
        
        {# Démarrer le formulaire #}
        {{ form_start(form) }}
        
            {# Méthode 1 : Tout en un avec form_row #}
            {{ form_row(form.quantity) }}
            {{ form_row(form.color) }}
            
            {# Méthode 2 : Contrôle granulaire #}
            <div class="mb-3">
                {{ form_label(form.quantity) }}
                {{ form_widget(form.quantity) }}
                {{ form_errors(form.quantity) }}
            </div>
            
            {# Bouton submit #}
            {{ form_widget(form.submit) }}
            
        {# Fermer le formulaire #}
        {{ form_end(form) }}
    </div>
{% endblock %}
```

---

## Flux complet des données

### 📊 Diagramme du flux

```
1. AFFICHAGE (GET)
   ┌─────────────────┐
   │ User visite     │
   │ /product        │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ Controller      │
   │ - Crée DTO      │
   │ - Crée Form     │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ Form Type       │
   │ - BuildForm     │
   │ - Lie au DTO    │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ Twig Template   │
   │ - form_start()  │
   │ - form_widget() │
   │ - form_end()    │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ HTML généré     │
   │ + CSRF token    │
   └─────────────────┘

2. SOUMISSION (POST)
   ┌─────────────────┐
   │ User soumet     │
   │ formulaire      │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ handleRequest() │
   │ - Lit POST      │
   │ - Vérifie CSRF  │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ Remplit DTO     │
   │ - setQuantity() │
   │ - setColor()    │
   └────────┬────────┘
            │
            ▼
   ┌─────────────────┐
   │ Validation      │
   │ - @Assert       │
   │ - Contraintes   │
   └────────┬────────┘
            │
      ┌─────┴─────┐
      │           │
   VALIDE    INVALIDE
      │           │
      ▼           ▼
   Traiter    Réafficher
   données    + erreurs
```

---

## 🎓 Résumé des concepts clés

| Concept | Rôle | Analogie |
|---------|------|----------|
| **DTO** | Objet qui porte les données | 📦 Un carton de déménagement |
| **Form Type** | Définit la structure du formulaire | 📋 Le plan de construction |
| **FormBuilder** | Construit le formulaire | 🔨 L'ouvrier qui assemble |
| **Children** | Les champs du formulaire | 👶 Les enfants du parent |
| **Options** | Configuration des champs | ⚙️ Les réglages |
| **Contraintes** | Règles de validation | ✅ Le contrôleur qualité |
| **handleRequest()** | Traite la soumission | 📨 Le facteur qui livre |
| **isValid()** | Vérifie la validité | 🛡️ Le garde qui vérifie |
| **form_widget()** | Affiche le champ | 🎨 Le pinceau qui dessine |

---

## 📌 Bonnes pratiques

✅ **Toujours utiliser un DTO** pour les formulaires non liés à une entité

✅ **Définir les contraintes dans le DTO**, pas dans le Form Type

✅ **Utiliser `form_row()`** pour un rendu rapide et cohérent

✅ **Toujours vérifier `isSubmitted() && isValid()`** avant de traiter

✅ **Rediriger après succès** (pattern PRG : Post-Redirect-Get)

✅ **Typer strictement** les propriétés du DTO (`?int`, `?string`)

---

## 🚀 Pour aller plus loin

- **Form Events** : Modifier dynamiquement le formulaire
- **Data Transformers** : Transformer les données avant/après soumission
- **Form Themes** : Personnaliser le rendu HTML
- **Embedded Forms** : Formulaires imbriqués
- **Collections** : Gérer des listes de sous-formulaires

---

**Créé avec ❤️ pour comprendre les formulaires Symfony**