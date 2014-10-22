<?php
namespace Sescandell\ContextRestrictorBundle\Filter;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Mapping\ClassMetadata;

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
    const PARAMETER_NAME = 'activeContextRestrictor';

    /**
     * @var string
     */
    protected $targetClass;

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @var array
     */
    protected $nullableMappings;

    /**
     * @param array $config
     */
    public function configure(array $config)
    {
        $this->targetClass = $config['targetClass'];
        $this->fieldName = $config['fieldName'];
        $this->nullableMappings = $config['nullableMappings'];
    }

    /**
     * (non-PHPdoc)
     * @see \Doctrine\ORM\Query\Filter\SQLFilter::addFilterConstraint()
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($this->targetClass == $targetEntity->getName()) {
            return $this->getConstraint($targetTableAlias, $targetEntity->columnNames[$this->fieldName]);
        }

        foreach ($targetEntity->associationMappings as $am) {
            if ($this->targetClass === $am['targetEntity'] && in_array($am['type'], array(ClassMetadata::ONE_TO_ONE, ClassMetadata::MANY_TO_ONE))) {
                // FIXME : manage multiple joinColumns
                return $this->getConstraint(
                    $targetTableAlias,
                    $am['joinColumns'][0]['name'], /*$am['fieldName']*/
                    in_array($targetEntity->getName(), $this->nullableMappings)
                );
            }
        }

        return '';
    }

    /**
     * Set restricted value to $value
     *
     * @param mixed $value
     */
    public function setRestrictedValue($value)
    {
        $this->setParameter(self::PARAMETER_NAME, $value);
    }

    /**
     * Get restricted value
     *
     * @return string
     * @throw \InvalidArgumentException
     */
    public function getRestrictedValue()
    {
        $value = $this->getParameter(self::PARAMETER_NAME);

        if (is_null($value)) {
            throw new \InvalidArgumentException();
        }

        return $value;
    }

    /**
     * Get constraint as string
     *
     * @param  string $targetTableAlias
     * @param  string $column
     * @return string
     */
    protected function getConstraint($targetTableAlias, $column, $orNull = false)
    {
        try {
            $restrictionValue = $this->getRestrictedValue();

            if (!empty($restrictionValue) && "''" != $restrictionValue) {
                $constraint = $targetTableAlias . '.' . $column . ' = ' . $restrictionValue;

                if ($orNull) {
                    $constraint .= ' OR ' . $targetTableAlias . '.' . $column . ' IS NULL ';
                }

                return $constraint;
            }
        } catch (\InvalidArgumentException $e) {
            // Parameter doesn't exists, or there is no restriction active
        }

        return '';
    }
}
