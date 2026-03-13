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
    
    public function testIsCorrectFirstCharacter()
    {
        $langs = ['eng', 'rus', 'jpn', 'heb', 'ukr', 'cmn', 'tat', 
                  'ara', 'tig', 'ell', 'hax', 'und', 'unknown'];
        $sentences = ['lower', 'Upper', 'אין צורך שנריב.', '這條小路沿著河走。', 
                      'トムは人だ。', 'Κρυώνετε;', '""', 'Әйдәгез.', 'زبك كبير.',
                      'Вы для'];
        $expected = [
            ['l',  true, 'א',  '這', 'ト', 'Κ',  true, 'Ә',  'ز',  'В' ], // eng
            ['l',  'U',  'א',  '這', 'ト', 'Κ',  true, 'Ә',  'ز',  true], // rus
            ['l',  'U',  'א',  true, true, 'Κ',  '"',  'Ә',  'ز',  'В' ], // jpn
            ['l',  'U',  true, '這', 'ト', 'Κ',  true, 'Ә',  'ز',  'В' ], // heb
            ['l',  'U',  'א',  '這', 'ト', 'Κ',  true, 'Ә',  'ز',  true], // ukr
            ['l',  'U',  'א',  true, 'ト', 'Κ',  '"',  'Ә',  'ز',  'В' ], // cmn
            ['l',  true, 'א',  '這', 'ト', 'Κ',  '"',  true, 'ز',  true], // tat
            ['l',  'U',  'א',  '這', 'ト', 'Κ',  true, 'Ә',  true, 'В' ], // ara
            ['l',  'U',  'א',  '這', 'ト', 'Κ',  '"',  'Ә',  'ز',  'В' ], // tig
            ['l',  'U',  'א',  '這', 'ト', true, true, 'Ә',  'ز',  'В' ], // ell
            [true, true, true, true, true, true, true, true, true, true], // hax
            [true, true, true, true, true, true, true, true, true, true], // und
            [true, true, true, true, true, true, true, true, true, true], // unknown
        ];
        
        foreach ($langs as $i=>$lang) {
            foreach ($sentences as $j=>$sentence) {
                $entity = new Sentence(['lang' => $lang, 'sentence' => $sentence]);
                $result = $entity->isCorrectFirstCharacter($sentence, $lang);
                $this->assertEquals($result, $expected[$i][$j]);
            }
        }
    }    
}
