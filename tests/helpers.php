<?php

if (! function_exists('activity')) {
    function activity()
    {
        return new class {
            public function causedBy($user)
            {
                return $this;
            }

            public function performedOn($model)
            {
                return $this;
            }

            public function log($message)
            {
                return true;
            }
        };
    }
}
