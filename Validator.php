<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Exception;

abstract class Validator
{
    protected $rules = [];
    protected $messages = [];
    protected $data = [];

    private function validateActionData($action, $data)
    {
        if (!is_array($data)) {
            throw new Exception('Second parameter should be an array');
        }

        $this->assessAction($action);
    }

    /**
     * Assess if an action exists.
     *
     * @param [type] $action [description]
     *
     * @return [type] [description]
     */
    public function assessAction($action)
    {
        if (!isset($this->rules[$action])) {
            throw new Exception('Action '.$action.'in '.get_class($this).' does not exist');
        }
    }

    /**
     * Returns the rules of an action.
     *
     * @param [type] $action [description]
     *
     * @return [type] [description]
     */
    public function rules($action)
    {
        $this->assessAction($action);

        return $this->rules[$action];
    }

    /**
     * Returns the errors of input based on action.
     *
     * @param [type] $action [description]
     * @param [type] $data   [description]
     *
     * @return [type] [description]
     */
    public function errors($action, $data)
    {
        $validator = $this->create($action, $data);

        $errors = [];

        foreach (array_keys($this->rules[$action]) as $attr) {
            if (!empty($validator->errors()->get($attr))) {
                $errors[$attr] = $validator->errors()->get($attr);
            }
        }

        return $errors;
    }

    /**
     * Create a Validator object based on action and input.
     *
     * @param [type] $action [description]
     * @param [type] $data   [description]
     *
     * @return [type] [description]
     */
    public function create($action, $data)
    {
        $this->validateActionData($action, $data);

        return ValidatorFacade::make(
            $data,
            $this->rules[$action],
            $this->messages[$action]
        );
    }

    /**
     * Checks if the given input passes the validation based on action.
     *
     * @param [type] $action [description]
     * @param [type] $data   [description]
     *
     * @return [type] [description]
     */
    public function fails($action, $data = null)
    {
        $this->validateActionData($action, $data);

        return $this->create($action, $data)->fails();
    }
}
