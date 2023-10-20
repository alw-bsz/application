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
 * @copyright   Copyright (c) 2017, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\Common\Repository;

/**
 * Class for the "document type" set type
 */
class Oai_Model_Set_DocumentTypeSets extends Application_Model_Abstract implements Oai_Model_Set_SetTypeInterface
{
    /**
     * Returns oai sets for document types.
     *
     * @return array
     */
    public function getSets()
    {
        $logger         = $this->getLogger();
        $setSpecPattern = Oai_Model_Set_SetSpec::SET_SPEC_PATTERN;

        $sets = [];

        $dcTypeHelper = new Application_View_Helper_DcType();

        $finder = Repository::getInstance()->getDocumentFinder();
        $finder->setServerState('published');

        foreach ($finder->getDocumentTypes() as $doctype) {
            if (0 === preg_match("/^$setSpecPattern$/", $doctype)) {
                $msg = "Invalid SetSpec (doctype='" . $doctype . "')."
                    . " Allowed characters are [$setSpecPattern].";
                $logger->err("OAI-PMH: $msg");
                continue;
            }

            $dcType = $dcTypeHelper->dcType($doctype);

            $setSpec        = "doc-type:$dcType";
            $sets[$setSpec] = ucfirst($dcType);
        }

        return $sets;
    }

    /**
     * Returns the document ids of this set type.
     *
     * @param DocumentFinderInterface $finder
     * @param string $set
     * @return array|int[]
     */
    public function getDocuments($finder, $set)
    {
        $setArray = explode(':', $set);

        if (count($setArray) !== 2 || empty($setArray[1])) {
            return [];
        }

        $finder->setDocumentType($setArray[1]);

        return $finder->getIds();
    }
}
