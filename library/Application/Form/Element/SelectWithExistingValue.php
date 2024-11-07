<?php

/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * @copyright   Copyright (c) 2024, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

/**
 * Select with option to selecting no option (null).
 */
class Application_Form_Element_SelectWithExistingValue extends Application_Form_Element_Select
{
    /** @var string */
    private $existingValue = 'Null';

    /**
     * @param string|null $value
     * @return $this
     */
    public function setValue($value = null)
    {
        if ($value === null) {
            return parent::setValue($this->existingValue);
        } else {
            return parent::setValue($value);
        }
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === $this->existingValue) {
            return null;
        } else {
            return $value;
        }
    }

    /**
     * @param string|null $value
     */
    public function setExistingValue($value = null)
    {
        $this->existingValue = $value;
    }

    /**
     * @return string|null
     */
    public function getExistingValue()
    {
        return $this->existingValue;
    }
}
