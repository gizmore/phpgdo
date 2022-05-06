<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Message;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertStringStartsWith;
use function PHPUnit\Framework\assertEquals;

final class MessageTest extends TestCase
{
    public function testRendering()
    {
    	$string = '<p><a>Test</a></p>';
        $message = GDT_Message::make()->var($string);
        $html = $message->renderCell();
        assertEquals($string, $html, 'Test if default renderer does not mess with user input.');
    }
    
}
