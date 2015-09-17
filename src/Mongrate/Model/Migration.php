<?php

namespace Mongrate\Model;

use Mongrate\Migration\Name;

class Migration
{
    /**
     * @var Name
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isApplied;


    public function __construct(Name $name, $isApplied)
    {
        $this->setName($name);
        $this->setIsApplied($isApplied);
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Name $name
     *
     * @return $this
     */
    public function setName(Name $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isApplied()
    {
        return $this->isApplied;
    }

    /**
     * @param boolean $isApplied
     *
     * @return $this
     */
    public function setIsApplied($isApplied)
    {
        $this->isApplied = (bool)$isApplied;

        return $this;
    }
}
