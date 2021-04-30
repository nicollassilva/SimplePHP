<?php

namespace SimplePHP\Model;

class ChildSimplePHP
{
    public $realName;
    public $completePath;

    public function __construct(String $child)
    {
        $getCompleteChild = $child;
        $getName = explode("\\", $getCompleteChild);

        $this->setRealName(end($getName));
        $this->setCompleteChild($getCompleteChild);
    }

    private function setRealName(String $name)
    {
        $this->realName = $name;
    }

    private function setCompleteChild(String $completeChild)
    {
        $this->completePath = $completeChild;
    }
}