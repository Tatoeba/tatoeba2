<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Sentence;
use Cake\TestSuite\TestCase;

class SentenceTest extends TestCase
{
    public function testIsCorrectLastCharacter() 
    {
        $langs = ['epo', 'cmn', 'ara', 'asm', 'tig', 'hye', 'ell',
                  'yid', 'zgh', 'jbo', 'hax', 'und', 'unknown'];
        $sentences = ['no', 'OK.', 'Good?', 'Bad!', 'トムは人だ。',
                      'Κρυώνετε;', '""', 'and now:', 'last…' ];
        $expected = [
            ['o',  true, true, true, '。', ';',  true, true, true], // epo
            ['o',  '.',  '?',  '!',  true, ';',  '"',  ':',  true], // cmn
            ['o',  true, '?',  true, '。', ';',  true, ':',  '…' ], // ara
            ['o',  true, true, true, '。', ';',  true, ':',  '…' ], // asm
            ['o',  true, true, true, '。', ';',  '"',  true, '…' ], // tig
            ['o',  '.',  '?',  '!',  '。', ';',  '"',  true, '…' ], // hye
            ['o',  true, '?',  true, '。', true, true, true, true], // ell
            ['o',  true, true, true, '。', ';',  true, true, '…' ], // yid
            ['o',  true, true, true, '。', ';',  true, ':',  '…' ], // zgh
            [true, true, '?',  '!',  '。', ';',  '"',  ':',  '…' ], // jbo
            [true, true, true, true, true, true, true, true, true], // hax
            [true, true, true, true, true, true, true, true, true], // und
            [true, true, true, true, true, true, true, true, true], // unknown
        ];
        
        foreach ($langs as $i=>$lang) {
            foreach ($sentences as $j=>$sentence) {
                $entity = new Sentence(['lang' => $lang, 'sentence' => $sentence]);
                $result = $entity->isCorrectLastCharacter($sentence, $lang);
                $this->assertEquals($result, $expected[$i][$j]);
            }
        }
    }
}
