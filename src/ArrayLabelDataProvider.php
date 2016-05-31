<?php

namespace Quazardous\PdfLabels;

use Quazardous\PdfLabels\LabelDataProviderInterface;

/**
 * Provides data from an array.
 *
 */
class ArrayLabelDataProvider implements LabelDataProviderInterface
{
    protected $data;
//     protected $idx;
    public function __construct(array $data)
    {
//         $this->data = array_values($data);
        $this->data = $data;
//         $this->rewind();
    }
    
    public function fetch()
    {
        $res = list(,$value) = each($this->data);
        if ($res !== false) return $value;
        return false;
    }
    
//     public function current()
//     {
//         return $this->data[$this->idx];
//     }
    
//     public function key()
//     {
//         return $this->idx;
//     }
    
//     public function next()
//     {
//         ++$this->idx;
//     }
    
//     public function rewind()
//     {
//         $this->idx = 0;
//     }
    
//     public function valid()
//     {
//         return isset($this->data[$this->idx]);
//     }
}
