<?php
$calculator = [
    'displayValue' => '',
    'firstOperand' => null,
    'waitingForSecondOperand' => false,
    'operator' => null,
];

function inputDigit($digit) {
    global $calculator;

    if ($calculator['waitingForSecondOperand'] === true) {
        $calculator['displayValue'] = $digit;
        $calculator['waitingForSecondOperand'] = false;
    } else {
        $calculator['displayValue'] = ($calculator['displayValue'] === '0') ? $digit : $calculator['displayValue'] . $digit;
    }
}

function inputDecimal($dot) {
    global $calculator;

    if (!strpos($calculator['displayValue'], $dot)) {
        $calculator['displayValue'] .= $dot;
    }
}

function handleOperator($nextOperator) {
    global $calculator;

    $firstOperand = $calculator['firstOperand'];
    $displayValue = $calculator['displayValue'];
    $operator = $calculator['operator'];
    $inputValue = floatval($displayValue);

    if ($operator && $calculator['waitingForSecondOperand']) {
        $calculator['operator'] = $nextOperator;
        return;
    }

    if ($firstOperand == null) {
        $calculator['firstOperand'] = $inputValue;
    } elseif ($operator) {
        $currentValue = $firstOperand ?: '0';
        $result = performCalculation($operator, $currentValue, $inputValue);

        $calculator['displayValue'] = strval($result);
        $calculator['firstOperand'] = $result;
    }

    $calculator['waitingForSecondOperand'] = true;
    $calculator['operator'] = $nextOperator;
}

function performCalculation($operator, $firstOperand, $secondOperand) {
    switch ($operator) {
        case '/':
            return $firstOperand / $secondOperand;
        case '*':
            return $firstOperand * $secondOperand;
        case '+':
            return $firstOperand + $secondOperand;
        case '-':
            return $firstOperand - $secondOperand;
        case '=':
            return $secondOperand;
    }
}

function resetCalculator() {
    global $calculator;

    $calculator['displayValue'] = '';
    $calculator['firstOperand'] = null;
    $calculator['waitingForSecondOperand'] = false;
    $calculator['operator'] = null;
}

function updateDisplay() {
    global $calculator;

    echo '<input type="text" class="calculator-screen z-depth-1" value="' . $calculator['displayValue'] . '" disabled />';
}

updateDisplay();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operator'])) {
        handleOperator($_POST['operator']);
        updateDisplay();
    } elseif (isset($_POST['decimal'])) {
        inputDecimal($_POST['decimal']);
        updateDisplay();
    } elseif (isset($_POST['all-clear'])) {
        resetCalculator();
        updateDisplay();
    } elseif (isset($_POST['digit'])) {
        inputDigit($_POST['digit']);
        updateDisplay();
    }
}
?>


