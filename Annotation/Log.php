<?php

namespace Jall\AopMonologBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * This annotation only works on public and protected methods; private methods are ignored.
 */
class Log extends AbstractLogAnnotation
{

}
