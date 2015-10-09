<?php

namespace Mongrate\Model;

class Migration
{
    /**
     * @var Name
     */
    private $name;

    /**
     * @var boolean
     */
    private $isApplied;

    public function __construct(Name $name, $isApplied)
    {
        $this->name = $name;
        $this->isApplied = (bool) $isApplied;
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isApplied()
    {
        return $this->isApplied;
    }
}
