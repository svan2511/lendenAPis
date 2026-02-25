<?php

namespace App\Repositories;

use App\Models\Onboarding;


class OnboardingRepository
{
    protected $model;

    public function __construct(Onboarding $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Onboarding
    {
        return $this->model->updateOrCreate(
        ['user_id' => $data['user_id']],
        [
            'business_type'     => $data['business_type'],
            'has_stock'         => $data['has_stock'],
            'has_appointments'  => $data['has_appointments'],
            'has_staff'         => $data['has_staff'] ?? null,
        ]
    );
         
    }

}