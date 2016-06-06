<?php

class Modscale {
    // defaults
    private $base = 1;
    private $ratio = 9/8;

    public function __construct( array $settings) {
        foreach ($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function calc($number) {
        $base = (array) $this->base;
        $ratio = $this->ratio;

        // Fast calc if not multi stranded
        if (count($base) === 1) {
            return pow($ratio, $number) * $base[0];
        }

        // Normalize bases
        // Find the upper bounds for base values
        $baseHigh = pow($ratio, 1) * $base[0];

        for ($i = 1; $i < count($base); $i++) {
            // shift up if value too low
            while ($base[$i] < $base[0]) {
                $base[$i] = pow($ratio, 1) * $base[$i];
            }
            // Shift down if too high
            while ($base[$i] >= $baseHigh) {
                $base[$i] = pow($ratio, -1) * $base[$i];
            }
        }

        // Sort bases
        sort($base);

        $step = floor($number / count($base));

        // Figure out what base to use with modulo
        $rBase = round(($number / count($base) - $step) * count($base));

        // Return
        return pow($ratio, $step) * $base[$rBase];
    }

}
