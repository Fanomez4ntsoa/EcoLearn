<?php

namespace App\EcoLearn\Models;

use DateTimeInterface;

class Category
{
    /**
     * Category id
     *
     * @var integer
     */
    public int $id;

    /**
     * Category name
     *
     * @var string
     */
    public $name;

    /**
     * Category description
     *
     * @var string
     */
    public $description;
    
    /**
     * Quiz creationDate
     *
     * @var DateTimeInterface
     */
    public DateTimeInterface $creationDate;
}