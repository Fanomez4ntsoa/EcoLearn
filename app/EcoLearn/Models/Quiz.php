<?php

namespace App\EcoLearn\Models;

use DateTimeInterface;

class Quiz
{
    /**
     * Quiz id
     *
     * @var integer
     */
    public int $id;

    /**
     * Quiz category
     *
     * @var string
     */
    public $category;

    /**
     * Quiz creationDate
     *
     * @var DateTimeInterface
     */
    public DateTimeInterface $creationDate;
}