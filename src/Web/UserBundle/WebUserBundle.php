<?php

namespace Web\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WebUserBundle extends Bundle
{
	// Hérite de FOSUserBundle
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
