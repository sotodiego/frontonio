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
 * Exception for LZW filter class
 */
class LzwException extends FilterException
{
    /**
     * @var integer
     */
    const LZW_FLAVOUR_NOT_SUPPORTED = 0x0501;
}
