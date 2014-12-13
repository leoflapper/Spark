<?php

namespace Framework\Sort\Comparator;

use Framework\Collection\ArrayList;

/**
 * A CompositeComparator stores zero or more comparators and allows them to be treated uniformly.
 *
 * So when the {@link CompositeComparator::compare($firstValue, $secondValue)} method is called the
 * CompositeComparator will traverse through all of it's comparators and determine which comparator
 * is actually capable of comparing the two values.
 *
 * @author Chris Harris
 * @version 1.0.0
 */
class CompositeComparator implements ComparatorInterface
{
    /**
     * A collection of zero or more comparators.
     *
     * @var ArrayList
     */
    private $comparators;

    /**
     * The previous comparator that was able to compare both values.
     *
     * @var AbstractComparator
     */
    private $prevComparator = null;

    /**
     * Construct a CompositeComparator.
     *
     * @param array|Traversable|null $comparators (optional) a collection containing one or more comparators.
     */
    public function __construct($comparators = null)
    {
        $this->comparators = new ArrayList();
        if ($comparators !== null) {
            $this->addComparators($comparators);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function compare($firstValue, $secondValue)
    {
        // first try the stored comparator.
        if ((null !== $this->prevComparator) && $this->prevComparator->accepts($firstValue, $secondValue)) {
            return $this->prevComparator->compare($firstValue, $secondValue);
        } 
        
        // remove the stored comparator.
        $this->prevComparator = null;
        // find a new comparator since the previous one can't be used.
        foreach ($this->comparators as $comparator) {
            if ($comparator->accepts($firstValue, $secondValue)) {
                // store this comparator for next comparison.
                $this->prevComparator = $comparator;
                // compare both values.
                return $comparator->compare($firstValue, $secondValue);
            }
        }

        return 0;
    }
    
    /**
     * Add the given comparator to this comparator.
     *
     * @param AbstractComparator $comparator the comparator to add.
     * @param int $index optional index to insert the comparator at the specified position.
     * @throws InvalidArgumentException if the given argument is not of type AbstractComparator.
     * @throws InvalidArgumentException if the $index argument is not a numeric value.
     */
    public function addComparator(AbstractComparator $comparator, $index = -1)
    {
        $this->comparators->add($comparator, $index);
    }
    
    /**
     * Add one or more comparators to this comparator.
     *
     * @param array|Traversable $comparators a collection of comparators.
     * @param int $index optional index to insert the comparators at the specified position.
     * @throws InvalidArgumentException if the given argument is not a numeric value.
     * @throws OutOfRangeException if the index is out of range ($index < 0 || $index >= List::count()).
     * @throws InvalidArgumentException if the given argument is not an array of instance of Traversable.
     */
    public function addComparators($comparators, $index = -1)
    {        
        $this->comparators->addAll($comparators, $index);
    }
    
    /**
     * Remove the given from comparator from this comparator.
     *
     * @param AbstractComparator $comparator the comparator to remove.
     * @return AbstractComparator|null the comparator that was removed, or null if the given comparator was not present in this composite.
     */
    public function removeComparator(AbstractComparator $comparator)
    {
        return $this->comparators->remove($comparator);
    }
    
    /**
     * Returns true if this composite contains the specified comparator.
     *
     * @param mixed $comparator the comparator whose presence will be tested.
     * @return bool true if this composite contains the specified comparator, false otherwise.
     */
    public function containsComparator(AbstractComparator $comparator)
    {
        return $this->comparators->contains($comparator);
    }
    
    /**
     * Removes all comparators from this composite. The composite will have zero composite objects after this call returns.
     *
     * @return void
     */
    public function clearComparators()
    {
        $this->comparators->clear();
    }
    
    /**
     * Returns if present a comparator at the given index.
     *
     * @param int $index the index at which a comparator should exist.
     * @return AbstractComparator|null the comparator at the given index, or null.
     */
    public function getComparator($index)
    {
        $retval = null;
        if (isset($this->comparators[$index])) {
            $retval = $this->comparators[$index];
        }
        return $retval;
    }
}
