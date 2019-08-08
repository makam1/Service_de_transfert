<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Form\UtilisateurType;

class Post
{
    // ...

    /**
     * Is the given User the author of this Post?
     *
     * @return bool
     */
    public function isAuthor(Utilisateur $user = null)
    {
        return $user && $user->getStatut() === 'actif';
    }
}

