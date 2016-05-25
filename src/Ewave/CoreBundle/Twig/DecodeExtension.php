<?php

namespace Ewave\CoreBundle\Twig;

use Ewave\CoreBundle\Service\Coder;

class DecodeExtension extends \Twig_Extension {

    use Coder;

    public function getFunctions()
    {
        $function = function($value) {
            return $this->decodeValue($value);
        };

        return array(
            new \Twig_SimpleFunction('decode', $function),
        );
    }

    public function getName()
    {
        return 'decode';
    }
}
