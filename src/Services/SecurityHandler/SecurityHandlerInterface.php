<?php

namespace App\Services\SecurityHandler;

use App\Entity\User;

interface SecurityHandlerInterface
{
    public function doAllTheNecessaryForThisUserWhoHaveLostHisPassword(User $user): void;
}
