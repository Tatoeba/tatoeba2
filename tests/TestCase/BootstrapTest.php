<?php

App::uses('CakeTestCase', 'TestSuite');

class BootstrapTest extends CakeTestCase {
    function testFormat() {
        # Basic {} functionality
        $this->assertEqual('Begin with...', format('{}...', 'Begin with'));
        $this->assertEqual('...end with', format('...{}', 'end with'));
        $this->assertEqual('With zero', format('With {0}', 'zero'));
        $this->assertEqual('With one', format('With {1}', 'zero', 'one'));
        $this->assertEqual('one two three', format('{0} {1} {2}', 'one', 'two', 'three'));
        $this->assertEqual('He love birds.', format('He {} {}.', 'love', 'birds'));
        $this->assertEqual('He love birds.', format('He {} {1}.', 'love', 'birds'));
        $this->assertEqual('He birds love.', format('He {1} {}.', 'love', 'birds'));

        # Silently return an empty string if not found
        $this->assertEqual('Unspecified .', format('Unspecified {}.'));
        $this->assertEqual('Unspecified .', format('Unspecified {42}.'));
        $this->assertEqual('Unspecified .', format('Unspecified {something}.'));

        # Support named parameters
        $this->assertEqual('I love Tatoeba.', format('I {what} Tatoeba.', array('what' => 'love')));
        $this->assertEqual('I  Tatoeba.', format('I {what} Tatoeba.', array('what' => null)));

        # Support lists, fallback on first key
        $loveConjugation = '; first: love; third: loves';
        $this->assertEqual('I love.',   format('I {0.first}.',  $loveConjugation));
        $this->assertEqual('He loves.', format('He {0.third}.', $loveConjugation));
        $this->assertEqual('He loves.', format('He {.third}.',  $loveConjugation));
        $this->assertEqual('He love.',  format('He {0}.',       $loveConjugation));
        $this->assertEqual('He love.',  format('He {}.',        $loveConjugation));
        $this->assertEqual('He love.',  format('He {0.ohnoes}.',$loveConjugation));
        $this->assertEqual('He love.',  format('He {.ohnoes}.', $loveConjugation));
        $this->assertEqual('He loves and I love.',
                           format('He {0.third} and I {0.first}.',
                           $loveConjugation));
        $this->assertEqual('I Love and he loves.',
                           format('I {1.First} and he {0.third}.',
                           $loveConjugation,
                           ucwords($loveConjugation)));
        $this->assertEqual('He Loves and I love.',
                           format('He {.Third} and I {.first}.',
                           ucwords($loveConjugation),
                           $loveConjugation));

        # Avoid printing '; list' crap if malformed
        $this->assertEqual('orz .', format('orz {0.aaa}.', ';aaa'));
        $this->assertEqual('orz .', format('orz {0.aaa}.', ';aaa;gasp'));
        $this->assertEqual('orz .', format('orz {0.aaa}.', ';aaa:'));
        $this->assertEqual('orz b.', format('orz {0.aaa}.', ';aaa:b'));

        # Avoid printing '; list' crap if malformed, and take
        # the first subkey if not found
        $this->assertEqual('orz .', format('orz {0}.', ';aaa'));
        $this->assertEqual('orz .', format('orz {0}.', ';gasp;aaa'));
        $this->assertEqual('orz .', format('orz {0}.', ';aaa:'));
        $this->assertEqual('orz b.', format('orz {0}.', ';gasp;aaa:b'));
        $this->assertEqual('orz b.', format('orz {0}.', ';aaa:b'));

        # Allow spaces, basically anything but ; and : in keys,
        # and anything but ; in values
        $this->assertEqual('He uses spaces.', format('He {0.k}.', '; k: uses spaces'));
        $this->assertEqual('He does.', format('He {0.uses spaces}.', '; uses spaces: does'));
        $this->assertEqual('He does.', format('He {0.uses.dots}.', '; uses.dots: does'));
        $this->assertEqual('He uses:colons.', format('He {0.k}.', '; k: uses:colons'));

        # Support passing lists as arrays
        $asArray = array('a' => '; k: a_v', 'b' => '; k: b_v');
        $this->assertEqual('a_v-b_v.', format('{a.k}-{b.k}.', $asArray));
        $this->assertEqual('b_v-a_v.', format('{b.k}-{a.k}.', $asArray));
    }
}
