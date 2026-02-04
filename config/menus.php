<?php

return [
    'admin' => [
        //['section' => 'Général'],
        //['icon'=>'bi-speedometer2','label'=>'Dashboard','url'=>'/admin/dashboard'],

        ['section' => 'Gestion'],
        ['icon'=>'bi-briefcase','label'=>'Offres','url'=>'/admin/offres'],
        ['icon'=>'bi-building','label'=>'Entreprises','url'=>'/admin/entreprises'],
        ['icon'=>'bi-people','label'=>'Utilisateurs','url'=>'/admin/users'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/maintenance'],

        ['section' => 'Référentiels'],
        ['icon'=>'bi-tags','label'=>'Types d’offre','url'=>'/maintenance'],
        ['icon'=>'bi-mortarboard','label'=>'Niveaux de qualification','url'=>'/maintenance'],
        ['icon'=>'bi-diagram-3','label'=>'Domaines d’emploi','url'=>'/maintenance'],
        ['icon'=>'bi-geo-alt','label'=>'Localisations','url'=>'/maintenance'],

        ['section' => 'Système'],
        ['icon'=>'bi-shield-lock','label'=>'Sécurité','url'=>'/maintenance'],
        ['icon'=>'bi-gear','label'=>'Paramètres','url'=>'/maintenance'],
    ],      


    'gestionnaire' => [
        ['icon'=>'bi-building','label'=>'Mon entreprise','url'=>'/dashboard/equipe'],
        ['icon'=>'bi-briefcase','label'=>'Offres','url'=>'/dashboard/offres'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/maintenance'],
        ['icon'=>'bi-gear','label'=>'Paramètres','url'=>'/maintenance'],
    ],      


    'recruteur' => [
        ['icon'=>'bi-person','label'=>'Mes infos','url'=>'/dashboard/profil'],
        ['icon'=>'bi-briefcase','label'=>'Mes offres','url'=>'/dashboard/offres'],
        ['icon'=>'bi-file-earmark-text','label'=>'Candidatures','url'=>'/maintenance'],
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