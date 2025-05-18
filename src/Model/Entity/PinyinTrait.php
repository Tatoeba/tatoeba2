<?php
namespace App\Model\Entity;

trait PinyinTrait
{

    private $_constTone2ToneConsti_search = array(
        'n1',
        'n2',
        'n3',
        'n4',
        'ng1',
        'ng2',
        'ng3',
        'ng4',
        'r1',
        'r2',
        'r3',
        'r4'
    );

    private $_constTone2ToneConsti_replace = array(
        '1n',
        '2n',
        '3n',
        '4n',
        '1ng',
        '2ng',
        '3ng',
        '4ng',
        '1r',
        '2r',
        '3r',
        '4r'
    );


    private $_vowelVowelTone2VowelToneVowel_search = array(
        'ai1',
        'ai2',
        'ai3',
        'ai4',
        'ao1',
        'ao2',
        'ao3',
        'ao4',
        'ei1',
        'ei2',
        'ei3',
        'ei4',
        'ou1',
        'ou2',
        'ou3',
        'ou4',
        'Ai1',
        'Ai2',
        'Ai3',
        'Ai4',
        'Ao1',
        'Ao2',
        'Ao3',
        'Ao4',
        'Ei1',
        'Ei2',
        'Ei3',
        'Ei4',
        'Ou1',
        'Ou2',
        'Ou3',
        'Ou4'
    );

    private $_vowelVowelTone2VowelToneVowel_replace = array(
        'a1i',
        'a2i',
        'a3i',
        'a4i',
        'a1o',
        'a2o',
        'a3o',
        'a4o',
        'e1i',
        'e2i',
        'e3i',
        'e4i',
        'o1u',
        'o2u',
        'o3u',
        'o4u',
        'A1i',
        'A2i',
        'A3i',
        'A4i',
        'A1o',
        'A2o',
        'A3o',
        'A4o',
        'E1i',
        'E2i',
        'E3i',
        'E4i',
        'O1u',
        'O2u',
        'O3u',
        'O4u'
    );

    private $_vowelTone2Unicode_search = array(
        'a1',
        'a2',
        'a3',
        'a4',
        'e1',
        'e2',
        'e3',
        'e4',
        'i1',
        'i2',
        'i3',
        'i4',
        'o1',
        'o2',
        'o3',
        'o4',
        'u1',
        'u2',
        'u3',
        'u4',
        'v1',
        'v2',
        'v3',
        'v4',
        'A1',
        'A2',
        'A3',
        'A4',
        'E1',
        'E2',
        'E3',
        'E4',
        'I1',
        'I2',
        'I3',
        'I4',
        'O1',
        'O2',
        'O3',
        'O4',
        'U1',
        'U2',
        'U3',
        'U4',
        'V1',
        'V2',
        'V3',
        'V4'
    );

    private $_vowelTone2Unicode_replace = array(
        'ā',
        'á',
        'ǎ',
        'à',
        'ē',
        'é',
        'ě',
        'è',
        'ī',
        'í',
        'ǐ',
        'ì',
        'ō',
        'ó',
        'ǒ',
        'ò',
        'ū',
        'ú',
        'ǔ',
        'ù',
        'ǖ',
        'ǘ',
        'ǚ',
        'ǜ',
        'Ā',
        'Á',
        'Ǎ',
        'À',
        'Ē',
        'É',
        'Ě',
        'È',
        'Ī',
        'Í',
        'Ǐ',
        'Ì',
        'Ō',
        'Ó',
        'Ǒ',
        'Ò',
        'Ū',
        'Ú',
        'Ǔ',
        'Ù',
        'Ǖ',
        'Ǘ',
        'Ǚ',
        'Ǜ'
    );


    private $_remove5thToneNumber_search = array(
        'a5',
        'e5',
        'i5',
        'o5',
        'u5',
        'v5',
        'n5',
        'g5',
        'r5',
        'A5',
        'E5',
        'I5',
        'O5',
        'U5',
        'V5',
        'N5',
        'G5',
        'R5'
    );


    private $_remove5thToneNumber_replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'v',
        'n',
        'g',
        'r',
        'A',
        'E',
        'I',
        'O',
        'U',
        'V',
        'N',
        'G',
        'R'
    );

    /**
     * Replace numeric pinyin by diacritical marks
     * Convert to php from a python script of Brian Vaughan
     * (http://brianvaughan.net)
     *
     * @param string $text Pinyin with tones in numeric notation (she3)
     *
     * @return string
     */

    public function numeric2diacritic($text)
    {
        $text = str_replace(
            $this->_constTone2ToneConsti_search,
            $this->_constTone2ToneConsti_replace,
            $text
        );

        $text = str_replace(
            $this->_vowelVowelTone2VowelToneVowel_search,
            $this->_vowelVowelTone2VowelToneVowel_replace,
            $text
        );

        $text = str_replace(
            $this->_vowelTone2Unicode_search,
            $this->_vowelTone2Unicode_replace,
            $text
        );


        $text = str_replace(
            $this->_remove5thToneNumber_search,
            $this->_remove5thToneNumber_replace,
            $text
        );

        return $text;
    }

}
?>
