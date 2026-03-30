<?php

namespace App\Rules;

use App\Enums\ResultType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidResultTypeValue implements ValidationRule
{
    protected ?string $resultType;

    public function __construct(?string $resultType = null)
    {
        $this->resultType = $resultType;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->resultType) {
            $this->resultType = request()->input('result_type');
        }

        if (!$this->resultType) {
            $fail('Result type is not specified');
            return;
        }

        try {
            $type = ResultType::from($this->resultType);
        } catch (\ValueError $e) {
            $fail('Invalid result type');
            return;
        }

        match($type) {
            ResultType::SCORE => $this->validateScore($value, $fail),
            ResultType::GOALS => $this->validateGoals($value, $fail),
            ResultType::ASSISTS => $this->validateAssists($value, $fail),
            ResultType::POINTS => $this->validatePoints($value, $fail),
            ResultType::TIME => $this->validateTime($value, $fail),
            ResultType::DISTANCE => $this->validateDistance($value, $fail),
            ResultType::SETS => $this->validateSets($value, $fail),
            ResultType::CUSTOM => $this->validateCustom($value, $fail),
        };
    }

    private function validateScore($value, Closure $fail): void
    {
        if (!is_numeric($value) || $value < 0 || !is_int((int)$value)) {
            $fail('Score has to be a positive whole number');
        }
    }

    private function validateGoals($value, Closure $fail): void
    {
        if (!is_numeric($value) || $value < 0 || !is_int((int)$value)) {
            $fail('Goals has to be a positive whole number');
        }
    }

    private function validateAssists($value, Closure $fail): void
    {
        if (!is_numeric($value) || $value < 0 || !is_int((int)$value)) {
            $fail('Assists has to be a positive whole number');
        }
    }

    private function validatePoints($value, Closure $fail): void
    {
        if (!is_numeric($value) || $value < 0) {
            $fail('Points has to be a positive number');
        }
    }

    private function validateTime($value, Closure $fail): void
    {
        // Format: HH:MM:SS or MM:SS
        if (!preg_match('/^(\d{1,2}:)?\d{1,2}:\d{2}$/', $value)) {
            $fail('Time has to be in format HH:MM:SS or MM:SS');
        }

        $parts = explode(':', $value);
        $count = count($parts);

        if ($count === 2) {
            $minutes = (int)$parts[0];
            $seconds = (int)$parts[1];
            if ($seconds >= 60) {
                $fail('Seconds cannot be greater than 59');
            }
        } elseif ($count === 3) {
            $hours = (int)$parts[0];
            $minutes = (int)$parts[1];
            $seconds = (int)$parts[2];

            if ($minutes >= 60) {
                $fail('Minutes cannot be greater than 59');
            }
            if ($seconds >= 60) {
                $fail('Seconds cannot be greater than 59');
            }
        }
    }

    private function validateDistance($value, Closure $fail): void
    {
        // Format: number with optional decimal places (e.g., 100.5, 1500)
        if (!is_numeric($value) || $value <= 0) {
            $fail('Distance has to be a positive number');
        }
    }

    private function validateSets($value, Closure $fail): void
    {
        if (!preg_match('/^(\d{1,2})-(\d{1,2})(,(\d{1,2})-(\d{1,2}))*$/', $value)) {
            $fail('Sets has to be in format "6-4,6-3,7-5"');
            return;
        }

        $setArray = explode(',', $value);
        foreach ($setArray as $set) {
            [$score1, $score2] = explode('-', $set);
            $score1 = (int)$score1;
            $score2 = (int)$score2;

            if ($score1 < 0 || $score2 < 0) {
                $fail('Score cannot be negative');
            }
        }
    }

    private function validateCustom($value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('Custom value cannot be empty');
        }
    }
}