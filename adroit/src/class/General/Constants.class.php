<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

class Constants
{
    // Variable Handling
    public function __set($constant_name, $constant_value)
    {
        define($constant_name, $constant_value);
    }
    
    public function __get($constant_name)
    {
        return constant($constant_name);
    }
    
    public function __isset($constant_name)
    {
        return defined($constant_name);
    }
}
