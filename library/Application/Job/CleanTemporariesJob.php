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
 * @copyright   Copyright (c) 2021, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\Common\Document;
use Opus\Common\Repository;
use Opus\Job\TaskAbstract;

/**
 * Class for cleaning temporary documents.
 */
class Application_Job_CleanTemporariesJob extends TaskAbstract
{
    /** @var string Duration of the temporary document */
    private $duration;

    /**
     * @param string $duration Duration e.g., P2D, P4M
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function run()
    {
        $output     = $this->getOutput();
        $dateString = $this->getPreviousDate();
        $finder     = Repository::getInstance()->getDocumentFinder();
        $finder->setServerState('temporary')
            ->setServerDateModifiedBefore($dateString);

        foreach ($finder->getIds() as $id) {
            $doc = Document::get($id);
            if ($doc->getServerState() === 'temporary') {
                $output->writeln("deleting document: $id");
                $doc->delete();
            } else {
                $output->writeln("NOT deleting document: $id because it has server state " . $doc->getServerState());
            }
        }

        return 0;
    }

    /**
     * Returns the previous date of the duration set.
     *
     * @return string date
     */
    private function getPreviousDate()
    {
        $date = new DateTime();

        if ($this->duration !== null) {
            return $date->sub(new DateInterval($this->duration))->format('Y-m-d');
        }

        return $date->format('Y-m-d');
    }
}
