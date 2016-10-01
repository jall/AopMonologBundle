<?php

namespace Jall\AopMonologBundle\Service;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

class ExpressionLanguage extends BaseExpressionLanguage
{

    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register('count', function ($iterable) {
            return sprintf('count(%s)', $iterable);
        }, function (array $variables, $iterable) {
            return count($iterable);
        });
    }

}
