<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('MessagesHelper', 'View/Helper');

class MessagesHelperTest extends CakeTestCase {

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->Messages = new MessagesHelper($View);
    }

    public function tearDown() {
        unset($this->Messages);

        parent::tearDown();
    }

    public function testFormatedContent() {
        $tests = array(
            'a simple http://example.com/ URL'
                => 'a simple <a href="http://example.com/" target="_blank">http://example.com/</a> URL',
            'with entity & inside'
                => 'with entity &amp; inside',
            'with entity http://example.com/foo?bar=1&baz=2'
                => 'with entity <a href="http://example.com/foo?bar=1&amp;baz=2" target="_blank">http://example.com/foo?bar=1&amp;baz=2</a>',
            'long link http://example.com/some-page-there?p=yesAndThisParam23=no'
                => 'long link <a href="http://example.com/some-page-there?p=yesAndThisParam23=no" target="_blank">http://example.com/some-page-th...ThisParam23=no</a>',
            'long link with entities http://example.com/some-page-there?p=yes&&&&&&&Param23=no'
                => 'long link with entities <a href="http://example.com/some-page-there?p=yes&amp;&amp;&amp;&amp;&amp;&amp;&amp;Param23=no" target="_blank">http://example.com/some-page-th...&amp;&amp;&amp;&amp;Param23=no</a>',
            'link http://example.com/ends-with-question-mark?'
                => 'link <a href="http://example.com/ends-with-question-mark?" target="_blank">http://example.com/ends-with-question-mark?</a>',
            'link http://example.com/parenthesis)'
                => 'link <a href="http://example.com/parenthesis)" target="_blank">http://example.com/parenthesis)</a>',
            'link http://example.com/parenthesis http://example.com/parenthesis)'
                => 'link <a href="http://example.com/parenthesis" target="_blank">http://example.com/parenthesis</a> <a href="http://example.com/parenthesis)" target="_blank">http://example.com/parenthesis)</a>',
        );
        foreach ($tests as $text => $formatedHTML) {
            $result = $this->Messages->formatedContent($text);
            $this->assertEquals($formatedHTML, $result);
        }
    }
}
