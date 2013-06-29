<?php
namespace Sescandell\ContextRestrictorBundle\Filter;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetaData;

/**
 * @author Stéphane Escandell
 *
 * @TODO
 *   > Manage cache: critical
 *   > Multi restriction management
 *
 */
class ContextRestrictorFilter extends SQLFilter
{
    /**
     * Define restricted entity and field to apply restriction
     *
     * @param string $entityName
     * @param string $fieldName
     */
    public function setTargetRestriction($entityName, $fieldName)
    {
        $this->setParameter('context_restrictor.entity_name', $entityName);
        $this->setParameter('context_restrictor.field_name', $fieldName);
    }

    /**
     * Set restricted value to $value
     *
     * @param mixed $value
     */
    public function setRestrictedValue($value)
    {
        $this->setParameter('context_restrictor.constraint', $value);
    }

    /**
     * Get restricted value
     *
     * @return string
     * @throw \InvalidArgumentException
     */
    public function getRestrictedValue()
    {
        return $this->getParameter('context_restrictor.constraint');
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\ORM\Query\Filter\SQLFilter::addFilterConstraint()
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($this->getParameter('context_restrictor.entity_name') == $targetEntity->getName()) {
            return $this->getConstraint($targetTableAlias, $targetEntity->columnNames[$this->getParameter('context_restrictor.field_name')]);
        }

        foreach ($targetEntity->associationMappings as $am) {
            if ($this->getParameter('context_restrictor.entity_name') === $am['targetEntity'] && in_array($am['type'], array(ClassMetadata::ONE_TO_ONE, ClassMetadata::MANY_TO_ONE))) {
                // FIXME : manage multiple joinColumns
                return $this->getConstraint($targetTableAlias, /*$am['fieldName']*/ $am['joinColumns'][0]['name']);
            }
        }

        return '';
    }

    /**
     * Get constraint as string
     *
     * @param string $targetTableAlias
     * @param string $column
     * @return string
     */
    protected function getConstraint($targetTableAlias, $column)
    {
        try {
            $restrictionValue = $this->getRestrictedValue();

            if (!empty($restrictionValue) && "''" != $restrictionValue) {
                return $targetTableAlias . '.' . $column . ' = ' . $restrictionValue;
            }
        } catch (\InvalidArgumentException $e) {
            // Parameter doesn't exists, or there is no restriction active
        }

        return '';
    }
}
