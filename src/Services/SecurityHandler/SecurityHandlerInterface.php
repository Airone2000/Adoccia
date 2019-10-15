<?php

namespace App\Services\SecurityHandler;

use App\Entity\User;

interface SecurityHandlerInterface
{
    function doAllTheNecessaryForThisUserWhoHaveLostHisPassword(User $user): void;
}