<?php

namespace App\Services\EcoLearn;

use App\Contracts\EcoLearn\CategoryServiceInterface;
use App\Contracts\EcoLearn\ResourceServiceInterface;
use App\Models\Ressources;
use Illuminate\Support\Facades\DB;

class ResourceService implements ResourceServiceInterface
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {
    }

    public function findByID(int $id): ?Ressources
    {
        $ressource = DB::table('ressources')
            ->where('ressource_id', $id)
            ->first();

        if ($ressource) {
            $creationDate   = to_datetime($ressource->created_at);

            $newRessource = new Ressources();
            $newRessource->id                  = $ressource->ressource_id;
            $newRessource->title               = $ressource->title;
            $newRessource->description         = $ressource->description;
            $newRessource->url                 = $ressource->url;
            $newRessource->created_at          = $creationDate;

            return $newRessource;
        }
        return null;
    }

    public function create(int $category_id, string $title, string $description, string $url): int
    {
        $ressource = new Ressources();
        $ressource->category_id     = $category_id;
        $ressource->title           = $title;
        $ressource->description     = $description;
        $ressource->url             = $url;


        $category = $this->categoryService->findByID($category_id);

        if ($category) {
            $ressource->save();
            if ($ressource->save() == true) {
                return 1;
            }
        } else {
            return ERROR_CATEGORY_NOTFOUND;
        }


        return 0;
    }   
}