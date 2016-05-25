<?php

namespace Ewave\CoreBundle\Service;

trait Coder {

    private static $_SALT = 'Pn5*#2e!9v';
    private static $_PASS = 'ewave1206';

    /**
     * @param $value
     * @return string
     */
    public function encodeValue($value)
    {
        if (empty($value)) {
            return $value;
        }
        return base64_encode($this->_strcode($value));
    }

    /**
     * @param $value
     * @return string
     */
    public function decodeValue($value)
    {
        if (empty($value)) {
            return $value;
        }
        return $this->_strcode(base64_decode($value));
    }

    /**
     * @param array $array
     * @return array
     */
    public function encodeArray(array $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->encodeValue($value);
        }

        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public function decodeArray(array $array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->decodeValue($value);
        }

        return $array;
    }

    /**
     * @param $str
     * @return string
     */
    private function _strcode($str)
    {
        $len = strlen($str);
        $gamma = '';
        $n = $len > 100 ? 8 : 2;
        while(strlen($gamma) < $len)
        {
            $gamma .= substr(pack('H*', sha1(self::$_PASS.$gamma.self::$_SALT)), 0, $n);
        }

        return $str^$gamma;
    }

}
