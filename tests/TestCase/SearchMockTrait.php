<?php

namespace App\Test\TestCase;

use Cake\Core\Configure;

trait SearchMockTrait
{
    private function forgeResults(array $sentencesIds, $total = null, $selectCursor = true)
    {
        // SphinxClient->Query() returns matches as an array
        // having document ids (sentence ids) as keys
        $matches = array_reduce($sentencesIds, function ($matches, $id) use ($selectCursor) {
            $doc = [];
            if ($selectCursor) {
                $doc['attrs'] = ['cursor' => "123456,$id"];
            }
            $matches[$id] = $doc;
            return $matches;
        });
        if (is_null($total)) {
            $total = count($sentencesIds);
        }
        $total_found = $total;
        return compact('matches', 'total', 'total_found');
    }

    public function enableMockedSearch(array $sentencesIds, $total = null, $selectCursor = true)
    {
        $results = $this->forgeResults($sentencesIds, $total, $selectCursor);

        $client = $this->getMockBuilder(\App\Lib\SphinxClient::class)
                       ->setMethods(['Query', 'UpdateAttributes', 'SetLimits'])
                       ->getMock();
        $client->expects($this->any())
               ->method('Query')
               ->will($this->returnValue($results));
        $numberOfUpdatedDocuments = 42;
        $client->expects($this->any())
               ->method('UpdateAttributes')
               ->will($this->returnValue($numberOfUpdatedDocuments));
        Configure::write('Sphinx.client', $client);

        Configure::write('Search.enabled', true);
    }

    public function enableMockedSearchError(string $errorMessage = 'search engine error goes here')
    {
        $client = $this->getMockBuilder(\App\Lib\SphinxClient::class)
                       ->setMethods(['Query', 'GetLastError'])
                       ->getMock();
        $client->expects($this->any())
               ->method('Query')
               ->will($this->returnValue(false));
        $client->expects($this->any())
               ->method('GetLastError')
               ->will($this->returnValue($errorMessage));
        Configure::write('Sphinx.client', $client);

        Configure::write('Search.enabled', true);
    }

    public function enableMockedQueryTimeExceededSearch(string $warningMessage = 'index und_index: query time exceeded max_query_time')
    {
        $results = $this->forgeResults([1, 2, 3]);

        $client = $this->getMockBuilder(\App\Lib\SphinxClient::class)
                       ->setMethods(['Query', 'GetLastWarning'])
                       ->getMock();

        $client->expects($this->any())
               ->method('Query')
               ->will($this->returnValue($results));
        $client->expects($this->any())
               ->method('GetLastWarning')
               ->will($this->returnValue($warningMessage));
        Configure::write('Sphinx.client', $client);

        Configure::write('Search.enabled', true);
    }
}
