<?php

class VehicleNotFoundException extends \RuntimeException
{

    public function __construct(int $id)
    {
        parent::__construct("Vehicle #" . $id . " was not found");
    }

}