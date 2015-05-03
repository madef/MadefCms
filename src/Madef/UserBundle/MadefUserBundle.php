<?php

namespace Madef\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MadefUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
