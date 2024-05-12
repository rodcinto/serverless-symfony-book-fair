<?php

namespace App\Entity;

use Symfony\Component\Validator\Validation;
use RuntimeException;

trait SelfValidateTrait
{
  private function validateSelf(): void
  {
    // enableAttributeMapping() is needed to validate $this with attributes.
    $validator = Validation::createValidatorBuilder()
      ->enableAttributeMapping()
      ->getValidator();
    $violations = $validator->validate($this);

    $errors = [];
    foreach ($violations as $violation) {
      $propertyPath = $violation->getPropertyPath();
      $message = $violation->getMessage();
      $errors[$propertyPath] = $message;
    }

    if (count($errors)) {
      throw new RuntimeException(implode(', ', $errors), 1);
    }
  }
}
