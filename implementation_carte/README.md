# Carte Professionnelle — Template HTML/CSS

Reproduction éditable et prête à l'impression de la carte professionnelle
(Ministère de la Fonction Publique, du Travail et de la Reforme du Service Public).

## Structure du dossier

```
carte-professionnelle/
├── index.html          → structure et contenu de la carte
├── style.css           → toutes les couleurs, dimensions, mise en page
├── script.js           → gère le bouton "Imprimer la carte"
├── assets/
│   └── placeholder.svg → silhouette utilisée tant qu'aucune vraie photo n'est fournie
└── README.md
```

## Remplacer la photo de profil

1. Place ton fichier image dans `assets/` (ex : `assets/profile-photo.jpg`).
2. Dans `index.html`, la balise `<img>` pointe déjà vers ce chemin :
   ```html
   <img class="photo" id="profilePhoto" src="assets/profile-photo.jpg" ... />
   ```
3. Si le fichier est absent, la silhouette de remplacement (`assets/placeholder.svg`)
   s'affiche automatiquement — aucune action requise en développement.
4. Photo recommandée : format portrait, ratio proche de 260×290, cadrée sur le visage
   (`object-fit: cover` recadre automatiquement le surplus).

## Modifier les informations de l'agent

Tout le texte éditable se trouve dans `index.html`, clairement commenté par section :

- **Titre du ministère** : bloc `<h1 class="ministry-title">`
- **Intitulé du poste** : `<span class="position-text">`
- **Champs Agent / Matricule / Service / Fonction** : chaque valeur est dans
  `<span class="info-value" data-field="...">...</span>` — remplace uniquement le texte,
  la mise en page (icône, séparateur, libellé, alignement) reste intacte.
- **Numéro du pied de carte** : texte dans `<footer class="card-footer"><p>...</p></footer>`.

## Modifier les couleurs, polices et dimensions

Toutes les valeurs réglables sont centralisées en haut de `style.css`, dans `:root` :

```css
:root {
  --card-width: 760px;
  --card-height: 1070px;
  --flag-green: #00853F;
  --flag-yellow: #FDEF42;
  --flag-red: #E31B23;
  --navy: #142850;
  --dark-band: #0b1a13;
  --icon-green: #1f8a54;
  --font-display: 'Poppins', 'Montserrat', Arial, sans-serif;
  /* ... */
}
```

Changer une variable met à jour automatiquement tous les éléments qui l'utilisent
(drapeau, bandeau du poste, icônes, pied de carte, etc.).

Pour changer les dimensions d'impression physique (par défaut 85 mm × 120 mm),
modifie `--print-width` et `--print-height`.

## Remplacer les icônes ou le drapeau

- Les icônes (Agent, Matricule, Service, Fonction) sont des SVG en ligne directement
  dans `index.html`, à l'intérieur de chaque `<span class="info-icon">`. Tu peux
  remplacer le `<svg>...</svg>` par n'importe quel autre icône au même format
  (viewBox `0 0 24 24`, `stroke="currentColor"`).
- Le drapeau est généré en CSS pur (3 bandes + étoile). Pour utiliser une image de
  drapeau à la place, remplace le bloc `.flag` dans `index.html` par une balise
  `<img>` et adapte `.flag` dans `style.css` en conséquence.

## Imprimer ou exporter en PDF

1. Ouvre `index.html` dans un navigateur (Chrome ou Edge recommandés pour un rendu
   fidèle des couleurs à l'impression).
2. Clique sur le bouton **« Imprimer la carte »** (masqué automatiquement à
   l'impression) ou utilise le raccourci `Ctrl/Cmd + P`.
3. Dans la boîte de dialogue d'impression :
   - Choisis **« Enregistrer au format PDF »** comme destination pour un export PDF.
   - Vérifie que l'option **« Graphiques d'arrière-plan »** (ou *Background graphics*)
     est activée, afin que les couleurs et dégradés soient conservés.
   - Les marges de page sont déjà réglées à 0 et le format de page à 85 mm × 120 mm
     via la règle `@page` dans `style.css` — aucun réglage manuel n'est nécessaire.

## Notes techniques

- Aucune dépendance externe (pas de framework, pas d'image chargée depuis une URL) :
  tous les décors (bande diagonale, motifs de points, filigrane) sont générés en CSS pur.
- Le fichier est entièrement responsive : sur petit écran, la carte se réduit à l'échelle
  tout en gardant ses proportions.
- La bande diagonale et les points décoratifs sont en `z-index: 0`, donc toujours
  **derrière** le contenu (photo, texte, bandeau) qui est en `z-index: 2`.
