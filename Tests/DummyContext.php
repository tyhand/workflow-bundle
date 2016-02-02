<?php

namespace TyHand\WorkflowBundle\Tests;

/**
 * Little class for testing library calls
 */
class DummyContext implements \TyHand\WorkflowBundle\Workflow\Context\ContextInterface
{
    ////////////
    // TRAITS //
    ////////////

    use \TyHand\WorkflowBundle\Workflow\Context\ContextTrait;

    ////////////////
    // PROPERTIES //
    ////////////////

    /**
     * A number
     *
     * @var int
     */
    private $num;

    /**
     * A string
     *
     * @var string
     */
    private $label;

    /////////////////
    // CONSTRUCTOR //
    /////////////////

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->num = 1;
        $this->label = 'awesome';
    }

    /////////////
    // METHODS //
    /////////////

    /**
     * Increase number by 1
     */
    public function increment()
    {
        $this->num++;
    }

    /**
     * Double the number
     */
    public function doublify()
    {
        $this->num = $this->num * 2;
    }

    /////////////////////////
    // GETTERS AND SETTERS //
    /////////////////////////

    /**
     * Get the value of A number
     *
     * @return int
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set the value of A number
     *
     * @param int num
     *
     * @return self
     */
    public function setNum($num)
    {
        $this->num = $num;
        return $this;
    }

    /**
     * Get the value of A string
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of A string
     *
     * @param string label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

}
