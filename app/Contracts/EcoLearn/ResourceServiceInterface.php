<?php

namespace App\Contracts\EcoLearn;

use App\Models\Ressources;

interface ResourceServiceInterface
{
    /**
     * Fin ressource by id
     * 
     * @param integer $id
     * @return Ressource|null
     */
    public function findByID(int $id): ?Ressources;

    /**
     * Create new Ressource
     * 
     * @param int $category_id
     * @param string $title
     * @param string $description
     * @param string $url
     * @return integer
     */
    public function create(int $category_id, string $title, string $description, string $url): int;   
}