<?php

return [
    'default'       => 'Une erreur s\'est produite',
    'validations'    => 'Requête invalide.',
    'user'  => [
        'collection' => 'Aucun utilisateur',
        'not_found' => 'Cette utilisateur n\'existe pas ou à été supprimer.',
        'delete' => "La suppression a échouer",
    ],
    'security'  => [
        'password'  => [
            'token_invalid' => 'Jeton de mot de pass invalide ou expiré.',
            'old_invalid'   => 'L\'ancien mot de passe n\'est plus valid'
        ],
    ],
    'quizz'  => [
        'category'    => [
            'not_found'    => 'Le categorie séléctionner n\'existe pas ou à été supprimer. Veuillez le créer svp.',
            'exists'    => 'Un quiz existe déjà pour la catégorie donnée.',
        ],
        'resource'  => [
            'category'  => 'Le ressource séléctionner n\'existe pas dans la catégorie'
        ],
        'not_found'    => 'Le quiz séléctionner n\'existe pas ou à été supprimer. Veuillez le créer svp.',
        'create'    => 'Erreur s\'est produite lors de la création d\'un quizz',
        'question'  => 'Erreur lors de la création d\'un Question',
        'question_already' => 'La question soumis existe déjà, veuillez inserer une nouvelle question svp. ',
        'already_deleted'    => 'La quiz séléctionner n\'existe pas ou à déjà été supprimer. Veuillez vérifier svp.',
        'answer' => [
            'user' => 'La réponse soumis par l\'utilisateur existe déjà.',
            'option'    => 'L\'option sélectionnée n\'est pas valide pour cette question'    
        ]
    ],
    'category'  => [
        'creation'   => 'Erreur s\'est produite lors de la création d\'un nouveau categorie.',
        'update'    => 'Erreur s\'est produite lors de la mise à jour du categorie.',
        'delete'    => 'La suppression de la catégorie a échouer',
    ],
    'resource' => [
        'category'    => [
            'not_found'    => 'Le categorie séléctionner n\'existe pas ou à été supprimer. Veuillez le créer svp.',
        ],
        'create' => 'Erreur s\'est produite lors de l\'ajout de cette ressource.',
        'not_found'    => 'La ressource séléctionner n\'existe pas ou à été supprimer. Veuillez vérifier svp.',
        'already_deleted'    => 'La ressource séléctionner n\'existe pas ou à déjà été supprimer. Veuillez vérifier svp.',
        'delete'    => 'La suppression de la catégorie a échouer',
        'collection' => 'Aucun resource trouver',
    ],
    'access'    => [
        'denied'    => 'Vous n\'avez pas l\'autorisation à faire cette demande. ',
        // 'forbidden' => 'Vous n\'avez pas le droit à faire cette demande. ',
    ],
];