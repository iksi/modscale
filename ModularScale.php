<?php

/*

    base needs to be greater than 0
    ratio needs to be greater than 1
    1-4 bases + 1 scale
    1 base + 1-4 scales

*/

class ModularScale {

    private $bases = [1];
    private $ratios = [1.618];
    private $decimals = 3;

    public function __construct($bases = null, $ratios = null, $decimals = null) {
        if ($decimals !== null) {
            $this->decimals = (int) $decimals;
        }

        if ($ratios !== null) {
            $this->ratios = array_map(function($value) {
                return round( (float) $value, $this->decimals);
            }, (array) $ratios);
        }

        if ($bases !== null) {
            $this->bases = array_map(function($value) {
                return round( (float) $value, $this->decimals);
            }, (array) $bases);

        }
    }

    public function bases() {
        return $this->bases;
    }

    public function ratios() {
        return $this->ratios;
    }

    public function calc($value) {
        $list = [];

        foreach ($this->bases as $base) {
            foreach ($this->ratios as $ratio) {
                if ($value >= 0) {
                    // Find values on a positive scale
                    for ($i = 0; pow($ratio, $i) * $base >= $this->bases[0]; $i--) {
                        $list[] = pow($ratio, $i) * $base;
                    }

                    for ($i = 0; pow($ratio, $i) * $base <= pow($ratio, $value + 1) * $base; $i++) {
                        $list[] = pow($ratio, $i) * $base;
                    }
                } else {
                    // Find values on a negative scale
                    for ($i = 0; pow($ratio, $i) * $base <= $this->bases[0]; $i++) {
                        $list[] = pow($ratio, $i) * $base;
                    }

                    for ($i = 0; pow($ratio, $i) * $base >= pow($ratio, $value - 1) * $base; $i--) {
                        if (pow($ratio, $i) * $base <= $this->bases[0]) {
                            $list[] = pow($ratio, $i) * $base;
                        }
                    }
                }
            }
        }

        $list = array_unique($list);
        sort($list);

        if ($value < 0) {
            $list = array_reverse($list);
        }

        return round($list[abs($value)], $this->decimals);
    }

    public function scale($upper, $lower) {
        $scale = [];

        foreach (range($upper, $lower) as $value) {
            $scale[$value] = $this->calc($value);
        }

        return $scale;
    }

}
