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
     * Quiz title
     *
     * @var string
     */
    public $title;

    /**
     * Quiz description
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