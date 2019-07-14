<?php

namespace Project;

use ReflectionClass;
use Exception;

abstract class Model
{
    private $errors;
    private $validators;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function formName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function attributes(): array
    {
        $names = [];
        $properties = (new ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [];
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($clearErrors) {
            $this->clearErrors();
        }

        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        foreach ($this->getActiveValidators() as $validator) {
            $validator->validateAttributes($this, $attributeNames);
        }

        return !$this->hasErrors();
    }

    /**
     * @return ArrayObject
     * @throws Exception
     */
    public function getValidators()
    {
        if ($this->validators === null) {
            $this->validators = $this->createValidators();
        }
        return $this->validators;
    }

    /**
     * @param null $attribute
     * @return array
     * @throws Exception
     */
    public function getActiveValidators($attribute = null): array
    {
        $validators = [];

        foreach ($this->getValidators() as $validator) {
            if ($validator->isActive() && ($attribute === null || in_array($attribute, $validator->attributes, true))) {
                $validators[] = $validator;
            }
        }

        return $validators;
    }

    /**
     * @return ArrayObject
     * @throws Exception
     */
    public function createValidators()
    {
        $validators = new ArrayObject;

        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) {
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new Exception('Invalid validation rule: a rule must specify both attribute names and validator type');
            }
        }
        return $validators;
    }

    /**
     * @param $values
     * @throws \ReflectionException
     */
    public function setAttributes($values)
    {
        if (is_array($values)) {
            $attributes = array_flip($this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                }
            }
        }
    }

    /**
     * @param $data
     * @param null $formName
     * @return bool
     * @throws \ReflectionException
     */
    public function load($data, $formName = null): bool
    {
        $scope = $formName === null ? $this->formName() : $formName;

        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);

            return true;
        }
        return false;
    }
}
