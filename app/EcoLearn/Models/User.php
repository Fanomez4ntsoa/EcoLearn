<?php

namespace App\EcoLearn\Models;

use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Notifiable, Authorizable;
    
    /**
     * User id
     *
     * @var integer
     */
    public int $id;

    /**
     * User username
     *
     * @var string
     */
    public $name;

    /**
     * User username
     *
     * @var string
     */
    public $username;

    /**
     * User email
     *
     * @var string
     */
    public string $email;

    /**
     * User creationDate
     *
     * @var DateTimeInterface
     */
    public DateTimeInterface $created_at;

    /**
     * Password token expiration date minimal limit
     *
     * @var DateTimeInterface|null
     */
    public DateTimeInterface|null $tokenValidFrom;

    /**
     * Password token expiration date maximal limit
     *
     * @var DateTimeInterface|null
     */
    public DateTimeInterface|null $tokenValidTill;

    /**
     * Hashed password
     *
     * @var string
     */
    private string $hashedPassword;

    /**
     * Password Token
     *
     * @var string|null
     */
    private ?string $passwordToken;

    /**
     * Get user full name
     *
     * @return string
     */
    public function getFullname(): string 
    {
        return trim($this->name . ' ' . $this->username);
    }

    /**
     * Set hashed password value
     */
    public function setHashedPassword(string $value): self
    {
        $this->hashedPassword = $value;
        return $this;
    }

    /**
     * Set Password Token
     *
     * @param string|null $token
     * @return self
     */
    public function setPasswordToken(?string $token = null): self
    {
        $this->passwordToken = $token;
        return $this;
    }

    /**
     * Get Password Token
     *
     * @return string|null
     */
    public function getPasswordToken(): ?string
    {
        return $this->passwordToken;
    }

    /**
     * Get hashed password
     *
     * @return string|null
     */
    public function getHashedPassword(): ?string
    {
        return $this->hashedPassword ?? null;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get email for notification
     *
     * @return string
     */
    public function routeNotificationForMail(): string|null
    {
        return $this->email;
    }
}