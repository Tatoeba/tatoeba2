<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->set('title_for_layout', $this->Pages->formatTitle(__('Terms of use')));
?>
<div id="annexe_content">
    <div class="module">
        <h2><?php echo __('Table of contents'); ?></h2>
        <ul>
            <li>
                <?php
                echo $this->Html->link(__('Translated version'), '#translated-version');
                ?>
            </li>
            <li>
                <?php
                echo $this->Html->link(__('Original version (French)'), '#fre-version');
                ?>
            </li>
        </ul>
    </div>
</div>

<div id="main_content">


<div class="main_module" id="translated-version">
<?php
echo '<h2>' . __('Information for contributors of text to the Tatoeba project') .
    '</h2>';

echo '<p>' .__(
    'To grow the commons of free knowledge and free culture, all users '.
    'contributing to the Tatoeba project are required to grant broad permissions '.
    'to the general public to re-distribute and re-use their contributions freely, '.
    'as long as the use is attributed. '.
    'Therefore, for any text to which '.
    'you hold the copyright, by submitting it, you agree to license it under '.
    'the Creative Commons Attribution License 2.0 (fr). Please note that this '.
    'license does allow commercial uses of your contributions, as long as such '.
    'uses are compliant with the terms. ', true
) . '</p>';

echo '<p>' . __(
    'As an author, you agree to be attributed in any of the following fashions: '.
    'a) through a hyperlink (where possible) or URL to the sentence or sentences '.
    'to which you contributed, b) through a hyperlink (where possible) or URL to '.
    'an alternative, stable online copy which is freely accessible, which conforms '.
    'with the license, and which provides credit to the authors in a manner '.
    'equivalent to the credit given on this website, or c) through a list of '.
    'all authors. (Any list of authors may be filtered to exclude very small or '.
    'irrelevant contributions.)', true
) . '</p>';

echo '<h3>' . __('Importing text:') . '</h3>' ; 

echo '<p>' . __(
    'If you want to import text that you have found elsewhere or '.
    'that you have co-authored with others, you can only do so if it is available '.
    'under terms that are compatible with the CC-BY license.', true
) . '</p>';

echo '<p>' . __(
    'If you import text under a compatible license which requires attribution, '.
    'you must, in a reasonable fashion, credit the author(s). Where such credit '.
    'is commonly given through sentence comments, it is sufficient to give '.
    'attribution in the edit summary, which is recorded in the sentence history '.
    'when importing the text. Regardless of the license, the text you import may '.
    'be rejected if the required attribution is deemed too intrusive.', true
) . '</p>';

echo '<h3>' . __('Information for re-users:') . '</h3>' ;


echo '<h4>' . __('Re-use of text:') . '</h4>' ;

echo '<ul>';
    echo '<li>';
        echo '<p>';
            echo __(
                'Attribution: To re-distribute a text page in any form, provide '.
                'credit to the authors either by including a) a hyperlink '.
                '(where possible) or URL to the page or pages you are re-using, '.
                'b) a hyperlink (where possible) or URL to an alternative, stable '.
                'online copy which is freely accessible, which conforms with '.
                'the license, and which provides credit to the authors in a manner '.
                'equivalent to the credit given on this website, or c) a list '.
                'of all authors. (Any list of authors may be filtered to exclude '.
                'very small or irrelevant contributions.) This applies to text '.
                'developed by the Tatoeba project community. Text from external '.
                'sources may attach additional attribution requirements to the '.
                'work, which we will strive to indicate clearly to you.', true
            );
        echo '</p>';
    echo '</li>';

    echo '<li>';
        echo '<p>';
            echo __(
                'Indicate changes: If you make modifications or additions, you '.
                'must indicate in a reasonable fashion that the original work has '.
                'been modified. If you are re-using the sentence in a wiki, for '.
                'example, indicating this in the page history is sufficient.', true
            );
     
        echo '</p>';
    echo '</li>';
        
    echo '<li>';
        echo '<p>';
            echo __(
                '  Licensing notice: Each copy or modified version that you '.
                'distribute must include a licensing notice stating that the work '.
                'is released under CC-BY and either a) a hyperlink or URL to the '.
                'text of the license or b) a copy of the license. For this '.
                'purpose, a suitable URL is: '.
                'http://creativecommons.org/licenses/by/2.0/fr/', true
            );    
        echo '</p>';
    echo '</li>';
    echo '<li>';
        echo '<p>';
            echo __(
                'For further information, please refer to the legal code of '.
                'the CC-BY License.', true
            ); 
        echo '</p>';
    echo '</li>';
echo '</ul>';

echo '<h3>' . __('Precedence of French terms') . '</h3>';

echo '<p>';
    echo __(
        'These site terms are not to be modified. If there is any inconsistency '.
        'between the French terms and any translation into other languages, the '.
        'French language version takes precedence.', true
    );
echo '</p>';
?>
</div>

<hr/>


<div class="main_module" id="fre-version">
    <h2>
        Information destinée aux contributeurs d'informations textuelles au 
        projet Tatoeba
    </h2>

    <p>
    Pour élargir la base de connaissances de Tatoeba, tout utilisateur contribuant 
    à ce projet doit impérativement donner de vastes permissions au
    public pour qu'il puisse redistribuer et réutiliser son contenu librement, 
    pourvu que la source de ce contenu soit clairement indiquée. Ainsi,
    lorsque vous soumettez un texte dont vous détenez les droits d'auteur,
    vous consentez à le soumettre sous le contrat
    de licence Creative Commons Paternité 2.0 (CC-BY 2.0). Veuillez noter que ce 
    contrat permet l'utilisation commerciale de vos contributions, tant que 
    les utilisations respectent les conditions de la licence.
    </p>

    <p>
    Comme auteur, vous consentez à ce que vos contributions vous soient créditées 
    d'une des façons suivantes : a) via un hyperlien (là où possible) ou une URL 
    pointant vers la liste de vos contributions textuelles, b) via un hyperlien 
    (là où possible) ou une URL pointant vers une copie alternative en ligne qui 
    est stable, qui est librement accessible, qui adhère au contrat de licence et 
    qui crédite ses auteurs d'une manière similaire à ce site, ou c) via une liste
    de tous les auteurs. (Toute liste d'auteurs peut omettre les petites 
    contributions et celles qui ne s'appliquent pas à la partie du texte copiée.)
    </p>

    <h3>Importer du texte :</h3>

    <p>
    Si vous voulez importer du texte que vous avez trouvé ailleurs ou que vous avez 
    rédigé avec d'autres personnes, vous ne pouvez le faire que si
    ses conditions d'utilisation sont compatibles avec la CC-BY 2.0. En d'autres 
    mots, vous ne pouvez importer du texte que s'il est (a) soumis avec
    des conditions d'utilisation compatibles avec la CC-BY 2.0, ou (b) soumis 
    sous un autre contrat dont les conditions d'utilisation sont compatibles
    avec la CC-BY 2.0.
    </p>

    <p>
    Si vous importez du texte sous un contrat compatible qui requiert une 
    attribution, vous devez, de manière raisonnable, créditer les auteurs. 
    Il suffit de donner cette attribution dans les commentaires du texte, lesquels 
    sont enregistrés dans l'historique du texte. Peu importe le contrat de licence,
    le texte que vous importez peut être rejeté si l'attribution requise est jugée
    trop intrusive.
    </p>


    <h3>Information pour les ré-utilisateurs</h3>

    <p>
    Vous pouvez réutiliser librement les contenus du projets Tatoeba.
    Veuillez suivre les instructions suivantes :
    </p>

    <h4>Ré-utilisation de texte :</h4>

    <ul>
        <li> Attribution/Paternité : pour redistribuer de quelque façon que ce 
        soit un texte, créditez les auteurs en incluant a) un hyperlien 
        (si possible) ou une URL pointant vers la page que vous réutilisez ou 
        b) un hyperlien (si possible) ou une URL pointant vers une autre version 
        en ligne stable, librement accessible, conforme à la licence et qui 
        crédite les auteurs d'une manière équivalente à ce qui est pratiqué sur 
        le présent site ou c) une liste de tous les auteurs. (Toute liste 
        d'auteurs pourra être filtrée pour exclure les contributions très petites 
        ou non pertinentes.) Ceci s'applique aux textes écrits par la communauté 
        Tatoeba. Les textes de sources externes peuvent exiger des conditions 
        d'attribution additionnelles, que nous nous efforcerons de vous indiquer 
        clairement. Par exemple, une page peut avoir un bandeau indiquant que
        tout ou partie de son contenu a déjà été publié par ailleurs. Partout 
        où une telle indication est présente sur la page elle-même, elle doit
        être conservée par les ré-utilisateurs.
        </li>

        <li> Indiquez les modifications : si vous effectuez des modifications ou 
        des ajouts, vous devez indiquer de façon raisonnable que l'œuvre originale 
        a été modifiée et en expliquant l'apport de la modification (correction 
        orthographique, continuité du dialogue par exemple)
        </li>
        
        <li> Notification de la licence : chaque copie que vous distribuez doit 
        inclure une notification indiquant que l'œuvre est sous licence CC-BY
        2.0 et fournir soit a) un hyperlien ou une URL pointant vers le texte 
        de la licence soit b) une copie de la licence. À cet effet une URL 
        utile est : http://creativecommons.org/licenses/by/2.0/fr/
        </li>

        <li>
            Pour plus d'information merci de vous référer au code légal de la 
            licence CC-BY 2.0(Français).
        </li>
    </ul>
    
    <h3>Prééminence de la version française</h3>
    
    <p>
        Seule la version française originale de ces conditions d'utilisation 
        fait autorité.
    </p>
    </div>

</div>
