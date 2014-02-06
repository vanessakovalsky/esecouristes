<?php

namespace Esecouristes\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EsecouristesUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
