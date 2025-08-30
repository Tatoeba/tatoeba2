<?php
namespace App\ORM\Association;

use App\ORM\Association\Loader\SelectWithDoublePivotLoader;
use Cake\ORM\Association\BelongsToMany;

/**
 * Represents an M - N - O relationship where there exists one junction - or join - table
 * that contains the association fields between the source and the target table.
 * The relationship is chained two times on the same join table.
 */
class BelongsToManyMany extends BelongsToMany
{
    /**
     * {@inheritDoc}
     *
     * @return \Closure
     */
    public function eagerLoader(array $options)
    {
        $name = $this->_junctionAssociationName();
        $loader = new SelectWithDoublePivotLoader([
            'alias' => $this->getAlias(),
            'sourceAlias' => $this->getSource()->getAlias(),
            'targetAlias' => $this->getTarget()->getAlias(),
            'foreignKey' => $this->getForeignKey(),
            'bindingKey' => $this->getBindingKey(),
            'strategy' => $this->getStrategy(),
            'associationType' => $this->type(),
            'sort' => $this->getSort(),
            'junctionAssociationName' => $name,
            'junctionProperty' => $this->_junctionProperty,
            'junctionAssoc' => $this->getTarget()->getAssociation($name),
            'junctionConditions' => $this->junctionConditions(),
            'finder' => [$this, 'find'],
        ]);

        return $loader->buildEagerLoader($options);
    }
}
