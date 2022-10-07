<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserCheckerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value == null) {
            return;
        }

        if ($value->getUsername() == $value->getPassword()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{val_A}}', $value->getEmail())
                ->setParameter('{{val_B}}', $value->getUsername())
                ->setParameter('{{do}}', 'と異なる値にする必要があります。')
                ->addViolation();
        } elseif (strpos($value->getUsername(), $value->getPassword()) !== false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{val_A}}', 'パスワード')
                ->setParameter('{{val_B}}', $value->getUsername())
                ->setParameter('{{do}}', 'に含まれてはいけません。')
                ->addViolation();
        } elseif (strpos($value->getPassword(), $value->getUsername()) !== false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{val_A}}', $value->getUsername())
                ->setParameter('{{val_B}}', 'パスワード')
                ->setParameter('{{do}}', 'に含まれてはいけません。')
                ->addViolation();
        }
    }
}