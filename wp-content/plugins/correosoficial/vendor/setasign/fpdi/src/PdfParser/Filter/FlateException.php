<?php

/**
 * This file is part of FPDI
 *
 * @package   cosafpdi\Fpdi
 * @copyright Copyright (c) 2020 cosafpdi GmbH & Co. KG (https://www.cosafpdi.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace cosafpdi\Fpdi\PdfParser\Filter;

/**
 * Exception for flate filter class
 */
class FlateException extends FilterException
{
    /**
     * @var integer
     */
    const NO_ZLIB = 0x0401;

    /**
     * @var integer
     */
    const DECOMPRESS_ERROR = 0x0402;
}
