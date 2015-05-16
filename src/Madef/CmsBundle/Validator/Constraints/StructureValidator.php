<?php

/*
 * Copyright (c) 2014, de Flotte Maxence <maxence@deflotte.fr>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Madef\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StructureValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $array = json_decode($value);
        if ($array === null) {
            $this->context->addViolation($constraint->message);

            return;
        }

        if (!is_array($array)) {
            $this->context->addViolation($constraint->message);

            return;
        }

        foreach ($array as $subarray) {
            if (!is_array($subarray)) {
                $this->context->addViolation($constraint->message);

                return;
            }

            foreach ($subarray as $blockContainer) {
                if (!is_array($blockContainer)) {
                    $this->context->addViolation($constraint->message);

                    return;
                }
                if (count($blockContainer) !== 1) {
                    $this->context->addViolation($constraint->message);

                    return;
                }

                foreach ($blockContainer as $block) {
                    if (!is_object($block)) {
                        $this->context->addViolation($constraint->message);

                        return;
                    }

                    foreach ($block as $string => $numeric) {
                        if (!is_string($string)) {
                            $this->context->addViolation($constraint->message);

                            return;
                        }
                        if (!is_numeric($numeric)) {
                            $this->context->addViolation($constraint->message);

                            return;
                        }
                    }
                }
            }
        }
    }
}
