<?php

namespace App\Test\TestCase;

use Cake\TestSuite\TestCase;

class BootstrapTestStringifiableObject {
    public function __toString() {
        return 'stringified!';
    }
}

class BootstrapTest extends TestCase {
    function testFormat() {
        # Basic {} functionality
        $this->assertEquals('Begin with...', format('{}...', 'Begin with'));
        $this->assertEquals('...end with', format('...{}', 'end with'));
        $this->assertEquals('With zero', format('With {0}', 'zero'));
        $this->assertEquals('With one', format('With {1}', 'zero', 'one'));
        $this->assertEquals('one two three', format('{0} {1} {2}', 'one', 'two', 'three'));
        $this->assertEquals('He love birds.', format('He {} {}.', 'love', 'birds'));
        $this->assertEquals('He love birds.', format('He {} {1}.', 'love', 'birds'));
        $this->assertEquals('He birds love.', format('He {1} {}.', 'love', 'birds'));

        # Silently return an empty string if not found
        $this->assertEquals('Unspecified .', format('Unspecified {}.'));
        $this->assertEquals('Unspecified .', format('Unspecified {42}.'));
        $this->assertEquals('Unspecified .', format('Unspecified {something}.'));

        # Support named parameters
        $this->assertEquals('I love Tatoeba.', format('I {what} Tatoeba.', array('what' => 'love')));
        $this->assertEquals('I  Tatoeba.', format('I {what} Tatoeba.', array('what' => null)));
        $this->assertEquals('I  Tatoeba.', format('I {what} Tatoeba.', array('what' => '')));

        # Support lists, fallback on first key
        $loveConjugation = '; first: love; third: loves';
        $this->assertEquals('I love.',   format('I {0.first}.',  $loveConjugation));
        $this->assertEquals('He loves.', format('He {0.third}.', $loveConjugation));
        $this->assertEquals('He loves.', format('He {.third}.',  $loveConjugation));
        $this->assertEquals('He love.',  format('He {0}.',       $loveConjugation));
        $this->assertEquals('He love.',  format('He {}.',        $loveConjugation));
        $this->assertEquals('He love.',  format('He {0.ohnoes}.',$loveConjugation));
        $this->assertEquals('He love.',  format('He {.ohnoes}.', $loveConjugation));
        $this->assertEquals('He loves and I love.',
                           format('He {0.third} and I {0.first}.',
                           $loveConjugation));
        $this->assertEquals('I Love and he loves.',
                           format('I {1.First} and he {0.third}.',
                           $loveConjugation,
                           ucwords($loveConjugation)));
        $this->assertEquals('He Loves and I love.',
                           format('He {.Third} and I {.first}.',
                           ucwords($loveConjugation),
                           $loveConjugation));

        # Avoid printing '; list' crap if malformed
        $this->assertEquals('orz .', format('orz {0.aaa}.', ';aaa'));
        $this->assertEquals('orz .', format('orz {0.aaa}.', ';aaa;gasp'));
        $this->assertEquals('orz .', format('orz {0.aaa}.', ';aaa:'));
        $this->assertEquals('orz b.', format('orz {0.aaa}.', ';aaa:b'));

        # Avoid printing '; list' crap if malformed, and take
        # the first subkey if not found
        $this->assertEquals('orz .', format('orz {0}.', ';aaa'));
        $this->assertEquals('orz .', format('orz {0}.', ';gasp;aaa'));
        $this->assertEquals('orz .', format('orz {0}.', ';aaa:'));
        $this->assertEquals('orz b.', format('orz {0}.', ';gasp;aaa:b'));
        $this->assertEquals('orz b.', format('orz {0}.', ';aaa:b'));

        # Allow spaces, basically anything but ; and : in keys,
        # and anything but ; in values
        $this->assertEquals('He uses spaces.', format('He {0.k}.', '; k: uses spaces'));
        $this->assertEquals('He does.', format('He {0.uses spaces}.', '; uses spaces: does'));
        $this->assertEquals('He does.', format('He {0.uses.dots}.', '; uses.dots: does'));
        $this->assertEquals('He uses:colons.', format('He {0.k}.', '; k: uses:colons'));

        # Support passing lists as arrays
        $asArray = array('a' => '; k: a_v', 'b' => '; k: b_v');
        $this->assertEquals('a_v-b_v.', format('{a.k}-{b.k}.', $asArray));
        $this->assertEquals('b_v-a_v.', format('{b.k}-{a.k}.', $asArray));

        # Support passing object that implements __toString()
        $this->assertEquals(
            'object: stringified!',
            format('object: {}', new BootstrapTestStringifiableObject())
        );
    }
}
