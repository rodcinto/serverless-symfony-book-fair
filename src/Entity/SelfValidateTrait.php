<?php

namespace App\Entity;

use LogicException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait SelfValidateTrait
{
  /**
   * @var string[]
   */
  private array $errors = [];

  /**
   * @param string[] $groups
   */
  private function validateSelf(array $groups = []): void
  {
    // enableAttributeMapping() is needed to validate $this with attributes.
    $validator = Validation::createValidatorBuilder()
      ->enableAttributeMapping()
      ->getValidator();
    $violations = $validator->validate($this, null, $groups);

    $this->errors = [
      ...$this->errors,
      ...$this->iterateViolations($violations)
    ];

    if (count($this->errors)) {
      throw new LogicException(implode(' ', $this->errors), 1);
    }
  }

  /**
   * @return string[]
   */
  private function iterateViolations(ConstraintViolationListInterface $violations): array
  {
    $errors = [];

    foreach ($violations as $violation) {
      $propertyPath = $violation->getPropertyPath();
      $errors[$propertyPath] = sprintf('%s: %s', $propertyPath, $violation->getMessage());
    }

    return $errors;
  }
}
