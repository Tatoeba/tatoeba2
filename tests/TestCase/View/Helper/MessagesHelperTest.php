<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\MessagesHelper;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\Helper;
use Cake\View\View;

class MessagesHelperTest extends TestCase {

    public $fixtures = array(
        'app.sentences'
    );

    public function setUp() {
        parent::setUp();
        $request = new ServerRequest([
            'environment' => [
                'HTTP_HOST' => 'example.net',
                'HTTPS' => 'on',
            ]
        ]);
        $View = new View($request);
        $this->Messages = new MessagesHelper($View);
    }

    public function tearDown() {
        unset($this->Messages);

        parent::tearDown();
    }

    public function formatContentProvider() {
        return [
            [ 'a simple http://example.com/ URL',
              'a simple <a href="http://example.com/" target="_blank" rel="nofollow">http://example.com/</a> URL' ],
            [ 'with entity & inside',
              'with entity &amp; inside' ],
            [ 'with entity http://example.com/foo?bar=1&baz=2',
              'with entity <a href="http://example.com/foo?bar=1&amp;baz=2" target="_blank" rel="nofollow">http://example.com/foo?bar=1&amp;baz=2</a>' ],
            [ 'long link http://example.com/some-page-there?p=yesAndThisParam23=no',
              'long link <a href="http://example.com/some-page-there?p=yesAndThisParam23=no" target="_blank" rel="nofollow">http://example.com/some-page-th...ThisParam23=no</a>' ],
            [ 'long link with entities http://example.com/some-page-there?p=yes&&&&&&&Param23=no',
              'long link with entities <a href="http://example.com/some-page-there?p=yes&amp;&amp;&amp;&amp;&amp;&amp;&amp;Param23=no" target="_blank" rel="nofollow">http://example.com/some-page-th...&amp;&amp;&amp;&amp;Param23=no</a>' ],
            [ 'link http://example.com/ends-with-question-mark?',
              'link <a href="http://example.com/ends-with-question-mark?" target="_blank" rel="nofollow">http://example.com/ends-with-question-mark?</a>' ],
            [ 'link http://example.com/parenthesis)',
              'link <a href="http://example.com/parenthesis)" target="_blank" rel="nofollow">http://example.com/parenthesis)</a>' ],
            [ 'link http://example.com/parenthesis http://example.com/parenthesis)',
              'link <a href="http://example.com/parenthesis" target="_blank" rel="nofollow">http://example.com/parenthesis</a> <a href="http://example.com/parenthesis)" target="_blank" rel="nofollow">http://example.com/parenthesis)</a>' ],
            [ 'link http://example.com/page&',
              'link <a href="http://example.com/page&amp;" target="_blank" rel="nofollow">http://example.com/page&amp;</a>' ],
            [ 'link http://example.com/page;',
              'link <a href="http://example.com/page" target="_blank" rel="nofollow">http://example.com/page</a>;' ],
            [ 'Link at the end #14',
              'Link at the end <a href="https://example.net/sentences/show/14" '
              .'title="An orphan sentence.">#14</a>' ],
            [ '#14 link at the beginning',
              '<a href="https://example.net/sentences/show/14" '
              .'title="An orphan sentence.">#14</a> link at the beginning' ],
            [ 'Link in #14 the middle',
              'Link in <a href="https://example.net/sentences/show/14" '
              .'title="An orphan sentence.">#14</a> the middle' ],
            [ 'Link to non existing sentence #9999999999999',
              'Link to non existing sentence '
              .'<a href="https://example.net/sentences/show/9999999999999" '
              .'title="">#9999999999999</a>' ],
            [ 'Escaped \#14 pound sign',
              'Escaped #14 pound sign' ],
        ];
    }

    /**
     * @dataProvider formatContentProvider
     */
    public function testFormatContent($text, $expected)
    {
        $result = $this->Messages->formatContent($text);
        $this->assertEquals($expected, $result);
    }
}
