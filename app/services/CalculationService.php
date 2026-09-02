<?php
declare(strict_types=1);

class CalculationService {
    private float $percentage;

    public function __construct(?float $percentage = null) {
        if ($percentage !== null) {
            $this->percentage = $percentage;
        } else {
            // Fallback to loaded constant JASPEL_PERCENTAGE from config.php
            $this->percentage = defined('JASPEL_PERCENTAGE') ? JASPEL_PERCENTAGE : 20.0;
        }
    }

    /**
     * Calculate Jaspel based on Tarif and percentage.
     */
    public function calculate(float $tarif): float {
        return round($tarif * ($this->percentage / 100.0), 2);
    }

    /**
     * Get the current percentage setting value.
     */
    public function getPercentage(): float {
        return $this->percentage;
    }

    /**
     * Calculate Jaspel for Radiologi Kedokteran Gigi (RKG) at fixed 15%.
     */
    public function calculateRkg(float $radiologi): float {
        $rkg_pct = defined('RKG_JASPEL_PERCENTAGE') ? RKG_JASPEL_PERCENTAGE : 15.0;
        return round($radiologi * ($rkg_pct / 100.0), 2);
    }
}
