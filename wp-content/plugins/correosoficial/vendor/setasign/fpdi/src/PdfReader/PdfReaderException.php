<?php

/**
 * This file is part of FPDI
 *
 * @package   cosafpdi\Fpdi
 * @copyright Copyright (c) 2020 cosafpdi GmbH & Co. KG (https://www.cosafpdi.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace cosafpdi\Fpdi\PdfReader;

use cosafpdi\Fpdi\FpdiException;

/**
 * Exception for the pdf reader class
 */
class PdfReaderException extends FpdiException
{
    /**
     * @var int
     */
    const KIDS_EMPTY = 0x0101;

    /**
     * @var int
     */
    const UNEXPECTED_DATA_TYPE = 0x0102;

    /**
     * @var int
     */
    const MISSING_DATA = 0x0103;
}
