<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByMobile(string $mobile): ?User
    {
        return $this->model->where('phone', $mobile)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function createProfile(array $data): Profile
    {
       return Profile::create($data);
       
    }

    // Agar future mein aur methods chahiye (update, delete, etc.) to yahan add kar dena
}