<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class Validator {

    const REQUIRED = "required";
    const CHECK_EMAIL = "validateEmail";
    const CHECK_URL = "validateUrl";
    const CHECK_JSON = "validateJson";
    const CHECK_BOOL = "validateBool";
    const CHECK_NUMERIC = "validateNumeric";
    const CHECK_STRING = "validateString";
    const CHECK_INTEGER = "validateInteger";
    const CHECK_ARRAY = "validateArray";
    const CHECK_CNP = "validateCnp";
    const CHECK_MIN_LENGTH = "checkMinLength";
    const CHECK_MAX_LENGTH = "checkMaxLength";
    const CHECK_DATE = "validateDate";
    const CHECK_POSITIVE = "validatePositive";
    CONST CHECK_HTML_TAGS = "checkForHtmlTags";
    const CHECK_PHONE = "validatePhone";

    private $errorMessage = '';
    private $requestData = array();
    private $availableRulesArray = array(
        self::REQUIRED,
        self::CHECK_URL,
        self::CHECK_EMAIL,
        self::CHECK_JSON,
        self::CHECK_NUMERIC,
        self::CHECK_STRING,
        self::CHECK_INTEGER,
        self::CHECK_ARRAY,
        self::CHECK_BOOL,
        self::CHECK_CNP,
        self::CHECK_MIN_LENGTH,
        self::CHECK_MAX_LENGTH,
        self::CHECK_DATE,
        self::CHECK_POSITIVE,
        self::CHECK_HTML_TAGS,
        self::CHECK_PHONE,
    );

    public function __construct($requestData = array())
    {
        $this->requestData = $requestData;
    }

    /**
     * Receives a list of fields with an array associated that containes a list of validations to check
     * @param array $fieldsWithRules
     * @return string
     */
    public function validate($fieldsWithRules)
    {
        foreach ($fieldsWithRules as $field => $rules) {
            foreach ($rules as $rule) {
                if(is_array($rule)) {
                    foreach ($rule as $ruleName => $ruleRestriction) {
                        $this->doRuleVerification($ruleName, $field, $ruleRestriction);
                        if (!empty($this->errorMessage)) {
                            return $this->errorMessage;
                        }
                    }
                } else {
                    $this->doRuleVerification($rule, $field);
                    if (!empty($this->errorMessage)) {
                        return $this->errorMessage;
                    }
                }
            }
        }
        return $this->errorMessage;
    }

    private function doRuleVerification($rule, $field, $ruleRestriction = null)
    {
        if (in_array($rule, $this->availableRulesArray)) {
            $this->errorMessage = empty($ruleRestriction) ? $this->$rule($field) : $this->$rule($field, $ruleRestriction);
        } else {
            Log::error("atempt to use rule: " . $rule);
        }

        return true;
    }

    private function required($field)
    {
        $errorMessageCode = '';
        if (!isset($this->requestData[$field]) || (empty($this->requestData[$field]) && $this->requestData[$field] != "0")) {
            $errorMessageCode = "missing_" . $field;
        }
        return $errorMessageCode;
    }

    private function validateEmail($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (!filter_var($this->requestData[$field], FILTER_VALIDATE_EMAIL)) {
                $errorMessageCode = "invalid_email";
            }
        }

        return $errorMessageCode;
    }

    private function validateUrl($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            $regex = "((https?|ftp):\/\/)?"; // SCHEME
            $regex .= "([a-z0-9+!*(),;?&=\$_\.\-]+(:[a-z0-9+!*(),;?&=\$_\.\-]+)?@)?"; // User and Pass
            $regex .= "([a-z0-9\-\.]*)\.([a-z]{2,4})"; // Host or IP
            $regex .= "(:[0-9]{2,5})?"; // Port
            $regex .= "(\/([a-z0-9+\$_%\-]\.?)+)*\/?"; // Path
            $regex .= "(\?[a-z+&\$_\.\-][a-z0-9;:@&%=+\/\$_.\-\,]*)?"; // GET Query
            $regex .= "(#[a-z_.-\/][a-z0-9+$%_\.\-=\/]*)?"; // Anchor

            if(!preg_match("~^$regex$~i", $this->requestData[$field])) {
                $errorMessageCode = "invalid_url";
            }
        }
        return $errorMessageCode;
    }

    private function validateJson($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            try {
                json_decode($this->requestData[$field]);
                if (json_last_error() !== 0) {
                    $errorMessageCode = "invalid_json_" . $field;
                }
            } catch (\Exception $e ) {
                Log::error("validateJson: Error Info - " . $e->getMessage() . ' ' . $e->getFile() . ': ' . $e->getLine());
                $errorMessageCode = "invalid_json_" . $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateBool($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field])) {
            if ($this->requestData[$field] !== 0 && $this->requestData[$field] !== 1) {
                $errorMessageCode = "invalid_bool_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateNumeric($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (!is_numeric($this->requestData[$field])) {
                $errorMessageCode = "invalid_numeric_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateArray($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (!is_array($this->requestData[$field])) {
                $errorMessageCode = "invalid_array_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateString($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && (!empty($this->requestData[$field]) || $this->requestData[$field] === 0)) {
            if (!is_string($this->requestData[$field])) {
                $errorMessageCode = "invalid_string_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateInteger($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (!is_integer($this->requestData[$field])) {
                $errorMessageCode = "invalid_integer_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    public function validatePositive($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if ($this->requestData[$field] < 0) {
                $errorMessageCode = "negative_value_". $field;
            }
        }
        return $errorMessageCode;
    }

    private function validateCNP($field)
    {
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            $cnpString = $this->requestData[$field];
            // CNP must have 13 characters
            if (strlen($cnpString) != 13) {
                return "invalid_cnp_lenght";
            }
            $cnp = str_split($cnpString);
            $hashTable = array(2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9);
            $hashResult = 0;
            // All characters must be numeric
            for ($i = 0; $i < 13; $i++) {
                if (!is_numeric($cnp[$i])) {
                    return 'invalid_cnp_values';
                }
                $cnp[$i] = (int) $cnp[$i];
                if ($i < 12) {
                    $hashResult += (int) $cnp[$i] * (int) $hashTable[$i];
                }
            }
            unset($hashTable, $i);
            $hashResult = $hashResult % 11;
            if ($hashResult == 10) {
                $hashResult = 1;
            }
            // Check Year
            $year = ($cnp[1] * 10) + $cnp[2];
            switch ($cnp[0]) {
                case 1 :
                case 2 :
                    $year += 1900;
                    break; // cetateni romani nascuti intre 1 ian 1900 si 31 dec 1999
                case 3 :
                case 4 :
                    $year += 1800;
                    break; // cetateni romani nascuti intre 1 ian 1800 si 31 dec 1899
                case 5 :
                case 6 :
                    $year += 2000;
                    break; // cetateni romani nascuti intre 1 ian 2000 si 31 dec 2099
                case 7 :
                case 8 :
                case 9 : // rezidenti si Cetateni Straini
                    $year += 2000;
                    if ($year > (int) date('Y') - 14) {
                        $year -= 100;
                    }
                    break;
                default : {
                    return 'invalid_cnp';
                }
                    break;
            }
            if(!($year > 1800 && $year < 2099 && $cnp[12] == $hashResult)){
                return 'invalid_cnp';
            }
        }
        return '';
    }

    private function checkMinLength($field, $restriction)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (strlen($this->requestData[$field]) < $restriction) {
                $errorMessageCode = "length_" . $field . "_too_small";
            }
        }
        return $errorMessageCode;
    }

    private function checkForHtmlTags($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if($this->requestData[$field] != strip_tags($this->requestData[$field])) {
                $errorMessageCode = $field . "_has_html_tags";
            }
        }
        return $errorMessageCode;
    }

    private function checkMaxLength($field, $restriction)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (strlen($this->requestData[$field]) > $restriction) {
                $errorMessageCode = "length_" . $field . "_too_big";
            }
        }
        return $errorMessageCode;
    }

    private function validateDate($field, $format = 'Y-m-d H:i:s')
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            $date = \DateTime::createFromFormat($format, $this->requestData[$field]);
            if (!($date && $date->format($format) == $this->requestData[$field])) {
                $errorMessageCode = "invalid_date_format";
            }
        }
        return $errorMessageCode;
    }

    private function validatePhone($field)
    {
        $errorMessageCode = '';
        if (isset($this->requestData[$field]) && !empty($this->requestData[$field])) {
            if (!preg_match('/^[0-9\-\(\)\/\+\s]*$/', $this->requestData[$field])) {
                $errorMessageCode = "wrong_phone_format";
            }
        }
        return $errorMessageCode;
    }

}
