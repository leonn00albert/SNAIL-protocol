<?php

namespace Snail\User;

use Exception;

class User
{
    private string $id;
    private string $user_name;
    private string $user_file = __DIR__ . '/../../user.json';
    public function __construct()
    {
    }
    public function createUser(string $user_name)
    {
        $this->id = uniqid();
        $this->user_name = $user_name;
        file_put_contents($this->user_file, json_encode($this->toArray()));
    }

    public function readUser(): User
    {
        $userData = json_decode(file_get_contents($this->user_file), true);
        if ($userData === null) {
            throw new Exception("Error reading user data from JSON file.");
        }
        $this->id = $userData['id'];
        $this->user_name = $userData['user_name'];

        return $this;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name,
        ];
    }

    public function getId() :string
    {
        return $this->id;
    }
}
