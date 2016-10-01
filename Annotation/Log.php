<?php

namespace Jall\AopMonologBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * This annotation only works on public and protected methods; private methods are ignored.
 */
class Log extends AbstractLogAnnotation
{

}
