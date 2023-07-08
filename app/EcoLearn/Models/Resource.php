<?php

namespace App\EcoLearn\Models;

use DateTimeInterface;

class Resource
{
    /**
     * Resource id
     *
     * @var integer
     */
    public int $id;

    /**
     * Category id
     *
     * @var integer
     */
    public int $category_id;

    /**
     * Resource name
     *
     * @var string
     */
    public string $title;

    /**
     * Resource description
     *
     * @var string
     */
    public string $description;

    /**
     * Resource description
     *
     * @var string|null
     */
    public ?string $url;
    
    /**
     * Resource creationDate
     *
     * @var DateTimeInterface
     */
    public DateTimeInterface $creationDate;

    /**
     * Resource updateDate
     *
     * @var DateTimeInterface
     */
    public DateTimeInterface $updatedDate;
}