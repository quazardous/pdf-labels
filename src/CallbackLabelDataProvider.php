<?php

namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelDataProviderInterface;

/**
 * Provides data from a callback function.
 * 
 * the callback signature is function () return mixed|false
 * 
 * The callback is triggered only once for each label.
 * 
 */
class CallbackLabelDataProvider implements LabelDataProviderInterface
{
    protected $callback;
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    
    public function fetch()
    {
        $value = call_user_func($this->callback);
        if ($value !== false) return $value;
        return false;
    }
}
