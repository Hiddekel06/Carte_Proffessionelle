<?php

/*
|--------------------------------------------------------------------------
| Carte professionnelle
|--------------------------------------------------------------------------
|
| Les fonds de carte (public/images/carte/*.png) sont exportés depuis le
| design Canva « Cartes professionnelles MFPTRSP ». L'application ne fait
| que superposer les données de l'agent (photo, nom, matricule, service,
| fonction, QR code) aux positions définies dans le design.
|
*/

return [

    /*
     | Dimensions d'impression de la carte (en millimètres).
     | Le design Canva a un ratio 1 : 1,414 (format « A »). La hauteur est
     | calculée automatiquement à partir de la largeur pour ne pas déformer
     | le design : 85 mm de large donnent 120 mm de haut.
     */
    'largeur_mm' => (float) env('CARTE_LARGEUR_MM', 85),

    /*
     | Modèle « inspecteur » : recto sans ligne Service + verso avec les
     | articles 352 et 356 du Code du Travail.
     |
     | Un agent reçoit automatiquement ce modèle si sa fonction ou son poste
     | contient l'un des mots-clés ci-dessous (sans tenir compte des
     | majuscules ni des accents). Le choix peut être forcé agent par agent
     | depuis sa fiche (champ « Modèle de carte »).
     */
    'mots_cles_inspecteur' => [
        'inspecteur du travail',
        'controleur du travail',
    ],

    /*
     | Police utilisée pour les valeurs (nom, matricule, service, fonction).
     | Doit correspondre à la police des libellés du design Canva.
     | Calibri est installée sur Windows ; Carlito (Google Fonts) est son
     | équivalent libre, utilisé si Calibri est absente.
     */
    'police' => [
        'famille' => env('CARTE_POLICE', "Calibri, 'Carlito'"),
        'google_fonts' => env('CARTE_POLICE_GOOGLE', 'Carlito:wght@400;700'),
        'graisse' => (int) env('CARTE_POLICE_GRAISSE', 700),
    ],
];
