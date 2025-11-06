<?php

/**
 * This file contains the InputGuard class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare;

use Aura\Filter\FilterFactory;
use Aura\Filter\ValueFilter;
use Lunr\Corona\Request;
use Lunr\Corona\RequestData;
use Lunr\Flare\Exceptions\UninitializedException;

/**
 * InputGuard class.
 */
class InputGuard
{

    /**
     * Shared instance of the Request class.
     * @var Request
     */
    protected readonly Request $request;

    /**
     * Shared instance of the FilterFactory class
     * @var FilterFactory
     */
    protected readonly FilterFactory $factory;

    /**
     * Instance of a ValueFilter class
     * @var ValueFilter
     */
    protected readonly ValueFilter $filter;

    /**
     * Input value.
     * @var InputValue
     */
    protected readonly InputValue $subject;

    /**
     * Constructor.
     *
     * @param Request         $request Shared instance of the Request class
     * @param FilterFactory   $factory Shared instance of the FilterFactory class
     * @param InputValue|null $subject Input value subject that should be inspected
     */
    final public function __construct(Request $request, FilterFactory $factory, ?InputValue $subject = NULL)
    {
        $this->request = $request;
        $this->factory = $factory;

        if ($subject !== NULL)
        {
            $this->subject = $subject;
        }

        $this->filter = $factory->newValueFilter();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // no-op
    }

    /**
     * Get a value and inspect it.
     *
     * @param string      $name     Name of the input value to inspect
     * @param RequestData $type     Type of the input value
     * @param bool        $optional Whether the input value is optional or required
     *
     * @return static|null Instance of the input guard or NULL if the input value is optional and missing
     */
    public function getAndInspect(string $name, RequestData $type = RequestData::Get, bool $optional = FALSE): ?static
    {
        $data = $this->request->getData($name, $type);

        switch ($type)
        {
            case RequestData::Get:
            case RequestData::Post:
            case RequestData::CliArgument:
                // We JSON encode data here to make it show up like a list, even though the input isn't actually JSON
                $rawValue = is_array($data) ? json_encode($data) : $data;
                return $this->inspect($name, $data, $rawValue, $optional);
            case RequestData::Upload:
                // The original file name is probably the closest we get to the original input value,
                // short of base64 encoding the actual file content
                if (is_array($data) && isset($data['name']))
                {
                    $filename = is_array($data['name']) ? current($data['name']) : $data['name'];

                    // The structure is determined by PHP internally. The filename can't be anything but a string.
                    // The asserts helps phpstan take that into account.
                    assert(is_string($filename));
                }
                else
                {
                    $filename = NULL;
                }

                return $this->inspect($name, $data, $filename, $optional);
            default:
                return $this->inspect($name, $data, optional: $optional);
        }
    }

    /**
     * Inspect an input value.
     *
     * @param string                     $name     Name of the input value to inspect
     * @param mixed                      $value    Value of the input value to inspect
     * @param bool|float|int|string|null $rawValue Raw, original input value if it's different from the current one
     * @param bool                       $optional Whether the input value is optional or required
     *
     * @return static|null Instance of the input guard or NULL if the input value is optional and missing
     */
    public function inspect(string $name, mixed $value, bool|float|int|string|null $rawValue = NULL, bool $optional = FALSE): ?static
    {
        $subject = new InputValue($name, $value, $rawValue);

        if ($optional === TRUE && $subject->value === NULL)
        {
            return NULL;
        }

        return new static($this->request, $this->factory, $subject);
    }

    /**
     * Verify that a subject is set.
     *
     * @throws UninitializedException In case the InputGuard was initialized without an InputValue.
     *
     * @return void
     */
    protected function verifySubjectIsSet(): void
    {
        if (!isset($this->subject))
        {
            throw new UninitializedException();
        }
    }

    /**
     * Get the value of the inspected input value.
     *
     * @return mixed The inspected input value
     */
    public function getValue(): mixed
    {
        $this->verifySubjectIsSet();

        return $this->subject->value;
    }

    /**
     * Validate that the input value complies with a validation rule.
     *
     * @param non-empty-string $rule    The rule the input value should comply with.
     * @param mixed[]          $args    Arguments to pass to the rule.
     * @param string|null      $message Custom error message for the rule in case it fails
     *
     * @return static Instance of the input guard
     */
    public function is(string $rule, array $args = [], ?string $message = NULL): static
    {
        $this->verifySubjectIsSet();

        if (!$this->filter->validate($this->subject->value, $rule, ...$args))
        {
            $this->subject->isInvalid($message ?? ucfirst($this->subject->name) . ' did not validate as "' . $rule . '"!');
        }

        return $this;
    }

    /**
     * Validate that the input value does not comply with a validation rule.
     *
     * @param non-empty-string $rule    The rule the input value should not comply with.
     * @param mixed[]          $args    Arguments to pass to the rule.
     * @param string|null      $message Custom error message for the rule in case it fails
     *
     * @return static Instance of the input guard
     */
    public function not(string $rule, array $args = [], ?string $message = NULL): static
    {
        $this->verifySubjectIsSet();

        if ($this->filter->validate($this->subject->value, $rule, ...$args))
        {
            switch ($rule)
            {
                case $message !== NULL:
                    $reason = $message;
                    break;
                case 'null':
                    $reason = ucfirst($this->subject->name) . ' is missing!';
                    break;
                default:
                    $reason = ucfirst($this->subject->name) . ' should not have validated as "' . $rule . '"!';
            }

            $this->subject->isInvalid($reason);
        }

        return $this;
    }

    /**
     * Decode the input value from Base64.
     *
     * @return static Instance of the input guard
     */
    public function fromBase64(): static
    {
        $this->not('null')
             ->not('blank')
             ->is('string');

        // Technically this is not correct, since is('string') only verifies that it's a scalar
        assert(is_string($this->subject->value));

        $decoded = base64_decode($this->subject->value, strict: TRUE);

        if ($decoded === FALSE)
        {
            $this->subject->isInvalid(ucfirst($this->subject->name) . ' is invalid Base64!');
        }

        return new static($this->request, $this->factory, new InputValue($this->subject->name, $decoded, $this->subject->rawValue));
    }

    /**
     * Decode the input value from JSON.
     *
     * @param bool $object Whether to decode into an object (TRUE) or an associative array (FALSE)
     *
     * @return static Instance of the input guard
     */
    public function fromJson(bool $object = FALSE): static
    {
        $this->not('null')
             ->not('blank')
             ->is('string');

        // Technically this is not correct, since is('string') only verifies that it's a scalar
        assert(is_string($this->subject->value));

        $decoded = json_decode($this->subject->value, associative: !$object);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            $this->subject->isInvalid(ucfirst($this->subject->name) . ' is invalid JSON!');
        }

        return new static($this->request, $this->factory, new InputValue($this->subject->name, $decoded, $this->subject->rawValue));
    }

}

?>
