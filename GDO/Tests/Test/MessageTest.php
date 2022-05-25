<?php
namespace GDO\Tests\Test;

use GDO\Tests\TestCase;
use GDO\UI\GDT_Message;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertStringStartsWith;
use function PHPUnit\Framework\assertEquals;

final class MessageTest extends TestCase
{
    public function testMessageRendering()
    {
    	$string = '<p><a>Test</a></p>';
        $message = GDT_Message::make('msg')->var($string);
        $html = $message->renderHTML();
        assertEquals($string, $html, 'Test if default renderer does not mess with user input.');
    }
    
}
