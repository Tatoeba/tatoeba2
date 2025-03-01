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
            $matches[$id] = ['attrs' => ['cursor' => "123456,$id"]];
            return $matches;
        });
        if (is_null($total)) {
            $total = count($sentencesIds);
        }
        $total_found = $total;
        $results = compact('matches', 'total', 'total_found');

        $client = $this->getMockBuilder(\App\Lib\SphinxClient::class)
                       ->setMethods(['Query', 'UpdateAttributes'])
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
}
