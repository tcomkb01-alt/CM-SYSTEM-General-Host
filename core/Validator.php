<?php

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $fieldRules);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $params = [];
        if (str_contains($rule, ':')) {
            [$ruleName, $paramStr] = explode(':', $rule);
            $params = explode(',', $paramStr);
        } else {
            $ruleName = $rule;
        }

        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "The $field field is required.");
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The $field field must be a valid email address.");
                }
                break;
            case 'min':
                if ($value && strlen((string)$value) < (int)$params[0]) {
                    $this->addError($field, "The $field field must be at least {$params[0]} characters.");
                }
                break;
            case 'max':
                if ($value && strlen((string)$value) > (int)$params[0]) {
                    $this->addError($field, "The $field field must not exceed {$params[0]} characters.");
                }
                break;
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->addError($field, "The $field field must be numeric.");
                }
                break;
            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;
                
                if ($value && !$this->checkUnique($table, $column, $value, $exceptId)) {
                    $this->addError($field, "The $field has already been taken.");
                }
                break;
        }
    }

    private function checkUnique(string $table, string $column, mixed $value, ?string $exceptId): bool
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = :value";
        $params = [':value' => $value];

        if ($exceptId) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = $exceptId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)$result['count'] === 0;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
