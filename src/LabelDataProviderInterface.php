<?php

namespace Quazardous\PdfLabels;

/**
 * Describe the data provider class.
 *
 */
interface LabelDataProviderInterface
{
    /**
     * Fetch data for one label.
     * @return data for one label or false if no more label
     */
    public function fetch();
}
