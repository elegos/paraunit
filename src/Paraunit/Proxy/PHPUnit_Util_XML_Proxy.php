<?php

namespace Paraunit\Proxy;

/**
 * Class PHPUnit_Util_XML_Proxy
 *
 * This class exists just as a proxy, 'cause you can't mock a static method with Prophecy
 * @package Paraunit\Process
 */
class PHPUnit_Util_XML_Proxy
{
    /**
     * @param $filename
     * @param bool|false $isHtml
     * @param bool|false $xinclude
     * @param bool|false $strict
     * @return \DOMDocument
     */
    public function loadFile($filename, $isHtml = false, $xinclude = false, $strict = false)
    {
        return \PHPUnit_Util_XML::loadFile($filename, $isHtml, $xinclude, $strict);
    }
}