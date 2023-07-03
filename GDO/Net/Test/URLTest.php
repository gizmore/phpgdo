<?php

namespace GDO\Net\Test;

use GDO\Net\GDT_Url;
use GDO\Net\URL;

class URLTest extends \GDO\Tests\TestCase
{

    public function testIRCUrls(): void
    {

        $url = 'irc://irc.giz.org:6667/foo?bar';
        $U = new URL($url);
        $p = $U->parts;
        $this->assertEquals('irc', $U->getScheme(), 'Check if URL scheme gets parsed.');
        $this->assertEquals('6667', $U->getPort(), 'Check if URL port gets parsed.');
        $this->assertEquals('irc.giz.org', $U->getHost(), 'Check if URL host gets parsed.');



    }

}