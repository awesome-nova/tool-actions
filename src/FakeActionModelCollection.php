<?php

namespace AwesomeNova;

use Laravel\Nova\Actions\ActionModelCollection;

class FakeActionModelCollection extends ActionModelCollection
{
    public function isEmpty()
    {
        return false;
    }
}
