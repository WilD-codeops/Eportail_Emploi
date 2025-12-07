<?php

return [
    'admin' => [
        [
            'icon'  => 'bi-speedometer2',
            'label' => 'Dashboard',
            'url'   => '/admin/dashboard'
        ],
        [
            'icon'  => 'bi-people',
            'label' => 'Utilisateurs',
            'url'   => '/admin/users'
        ],
        [
            'icon'  => 'bi-briefcase',
            'label' => 'Offres',
            'url'   => '/admin/offres'
        ],
        [
            'icon'  => 'bi-building',
            'label' => 'Entreprises',
            'url'   => '/admin/entreprises'
        ],
        [
            'icon'  => 'bi-file-earmark-text',
            'label' => 'Candidatures',
            'url'   => '/admin/candidatures'
        ],
    ],

    'gestionnaire' => [
        [
            'icon'  => 'bi-building',
            'label' => 'Mon entreprise',
            'url'   => '/gestionnaire/entreprise'
        ],
        [
            'icon'  => 'bi-briefcase',
            'label' => 'Offres',
            'url'   => '/gestionnaire/offres'
        ],
        [
            'icon'  => 'bi-file-earmark-text',
            'label' => 'Candidatures',
            'url'   => '/gestionnaire/candidatures'
        ],
        [
            'icon'  => 'bi-gear',
            'label' => 'Paramètres',
            'url'   => '/gestionnaire/settings'
        ],
    ],

    'recruteur' => [
        [
            'icon'  => 'bi-briefcase',
            'label' => 'Mes offres',
            'url'   => '/recruteur/offres'
        ],
        [
            'icon'  => 'bi-file-earmark-text',
            'label' => 'Candidatures',
            'url'   => '/recruteur/candidatures'
        ],
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