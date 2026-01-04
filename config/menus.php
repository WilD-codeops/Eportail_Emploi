<?php

return [
    'admin' => [
        ['section' => 'Général'],
        ['icon'=>'bi-speedometer2','label'=>'Dashboard','url'=>'/admin/dashboard'],

        ['section' => 'Gestion'],
        ['icon'=>'bi-briefcase','label'=>'Offres','url'=>'/admin/offres'],
        ['icon'=>'bi-building','label'=>'Entreprises','url'=>'/admin/entreprises'],
        ['icon'=>'bi-people','label'=>'Utilisateurs','url'=>'/admin/users'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/admin/candidatures'],

        ['section' => 'Référentiels'],
        ['icon'=>'bi-tags','label'=>'Types d’offre','url'=>'/admin/ref/types-offres'],
        ['icon'=>'bi-mortarboard','label'=>'Niveaux de qualification','url'=>'/admin/ref/niveaux-qualification'],
        ['icon'=>'bi-diagram-3','label'=>'Domaines d’emploi','url'=>'/admin/ref/domaines-emploi'],
        ['icon'=>'bi-geo-alt','label'=>'Localisations','url'=>'/admin/ref/localisations'],

        ['section' => 'Système'],
        ['icon'=>'bi-shield-lock','label'=>'Sécurité','url'=>'/admin/security'],
        ['icon'=>'bi-gear','label'=>'Paramètres','url'=>'/admin/settings'],
    ],      


    'gestionnaire' => [
        ['icon'=>'bi-building','label'=>'Mon entreprise','url'=>'/dashboard/entreprise'],
        ['icon'=>'bi-briefcase','label'=>'Offres','url'=>'/dashboard/offres'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/dashboard/candidatures'],
        ['icon'=>'bi-gear','label'=>'Paramètres','url'=>'/dashboard/settings'],
    ],      


    'recruteur' => [
        ['icon'=>'bi-briefcase','label'=>'Mes offres','url'=>'/dashboard/offres'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/dashboard/candidatures'],
    ],

    'candidat' => [
        [
            'icon'  => 'bi-person',
            'label' => 'Mon profil',
            'url'   => '/candidat/profil'
        ],
        [
            'icon'  => 'bi-heart',
            'label' => 'Favoris',
            'url'   => '/candidat/favoris'
        ],
        [
            'icon'  => 'bi-file-earmark-text',
            'label' => 'Mes candidatures',
            'url'   => '/candidat/mes-candidatures'
        ],
    ],
];   