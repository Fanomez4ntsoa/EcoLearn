<?php

return [
    'greeting' => 'Bonjour :name',
    'greeting_no_name'  => 'Bonjour',
    'signed_up' => [
        'subject'   => 'EcoLearn ~ Confirmation de création de compte',
        'content'   => [
            'confirmation' => 'Afin de confirmer la création de votre, cliquez sur le lien suivant: ',
        ],
        'action'    => 'Confirmer et configurer le mot de passe',
    ],
    'password'  => [
        'reset' => [
            'subject'   => 'Réinitialisation de mot de passe pour le compte [:name].',
            'line1'     => 'Vous avez demandé une réinitialisation de mot de passe sur le plateforme EcoLearn.',
            'line2'     => 'Cliquer sur le lien suivant pour réinitialiser votre mot de passe.',
            'action'    => 'Réinitialiser le mot de passe'
        ],
        'updated'   => [
            'subject'   => [
                'initialized'   => 'Mot de passe initialisé pour le compte [:name].',
                'changed'       => 'Mot de passe modifié pour le compte [:name].'    
            ],
            'content'   => [
                'line1' => [
                    'initialized'   => 'Le mot de passe de votre compte a été configuré le :date à :hour.',
                    'change'        => 'Le mot de passe de votre compte a été modifié le :data à :hour.',
                ],
                'line2' => 'Vous pouvez acceder à votre compte en cliquant le lien suivant: ',
            ],
            'action'    => 'Mon compte',
        ],
    ],
    'link_expiration' => 'Ce lien expirera le :date à :hour. ',
];