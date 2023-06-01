<?php

namespace App\Test\TestCase;

use Cake\Core\Configure;

trait SearchMockTrait
{
    public function enableMockedSearch(array $sentencesIds, $total = null)
    {
        // SphinxClient->Query() returns matches as an array
        // having document ids (sentence ids) as keys
        $matches = array_reduce($sentencesIds, function ($matches, $id) {
            $matches[$id] = [];
            return $matches;
        });
        if (is_null($total)) {
            $total = count($sentencesIds);
        }
        $total_found = $total;
        $results = compact('matches', 'total', 'total_found');

        $client = $this->getMockBuilder(\App\Lib\SphinxClient::class)
                       ->setMethods(['Query'])
                       ->getMock();
        $client->expects($this->any())
               ->method('Query')
               ->will($this->returnValue($results));
        Configure::write('Sphinx.client', $client);

        Configure::write('Search.enabled', true);
    }
}
