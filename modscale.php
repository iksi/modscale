<?php

/*

    MODSCALE

    base needs to be greater than 0
    ratio needs to be greater than 1
    1-4 bases + 1 ratio
    1 base + 1-4 ratios

    maybe think of the scale() function as
    a range between 1px and 1600px (0.0625 - 100)

*/

class Modscale {
    // defaults
    private $bases  = [1];
    private $ratios = [1.618];

    public function __construct($bases, $ratios) {
        // bases greater than 0
        // ratios greater than 1
        $bases  = $this->filter($bases, 0);
        $ratios = $this->filter($ratios, 1);

        // multiple bases use single ratio
        // multiple ratios use single base
        $ratios = count($bases) > 1 ? array_slice($ratios, 0, 1) : $ratios;
        $bases  = count($ratios) > 1 ? array_slice($bases, 0, 1) : $bases;

        // use inputs or defaults
        $this->bases = empty($bases) ? $this->bases : $bases;
        $this->ratios = empty($ratios) ? $this->ratios : $ratios;
    }

    public function filter($values, $minimum) {
        // unique floats
        $values = array_unique(array_map('floatval', (array) $values));

        // only values greater than the indicated minimum
        $values = array_filter($values, function($value) use($minimum) {
            return $value > $minimum;
        });

        // return a maximum of 4 results
        return array_slice($values, 0, 4);
    }

    public function bases() {
        return $this->bases;
    }

    public function ratios() {
        return $this->ratios;
    }

    public function calc($number) {
        $list = [];

        foreach ($this->bases as $base) {
            foreach ($this->ratios as $ratio) {
                if ($number >= 0) {
                    // Find values on a positive scale
                    for ($i = 0; pow($ratio, $i) * $base >= $this->bases[0]; $i--) {
                        $list[] = pow($ratio, $i) * $base;
                    }

                    for ($i = 0; pow($ratio, $i) * $base <= pow($ratio, $number + 1) * $base; $i++) {
                        $list[] = pow($ratio, $i) * $base;
                    }
                } else {
                    // Find values on a negative scale
                    for ($i = 0; pow($ratio, $i) * $base <= $this->bases[0]; $i++) {
                        $list[] = pow($ratio, $i) * $base;
                    }

                    for ($i = 0; pow($ratio, $i) * $base >= pow($ratio, $number - 1) * $base; $i--) {
                        if (pow($ratio, $i) * $base <= $this->bases[0]) {
                            $list[] = pow($ratio, $i) * $base;
                        }
                    }
                }
            }
        }

        $list = array_unique($list);
        sort($list);

        if ($number < 0) {
            $list = array_reverse($list);
        }

        $abs = abs($number);

        return round($list[$abs], 3);
    }

    public function scale($upper, $lower) {
        $scale = [];

        foreach (range($upper, $lower) as $number) {
            $scale[$number] = $this->calc($number);
        }

        return $scale;
    }
}
