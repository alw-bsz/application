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
 * @category    Application
 * @package     Module_Admin
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2008-2013, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */

class Admin_Form_Document_Files extends Admin_Form_AbstractDocumentSubForm {

    private $header = array(
        array('label' => null, 'class' => 'file'),
        array('label' => 'files_column_size', 'class' => 'size'),
        array('label' => 'files_column_mimetype', 'class' => 'mimetype'),
        array('label' => 'files_column_language', 'class' => 'language'),
        array('label' => 'files_column_frontdoor', 'class' => 'visiblefrontdoor'),
        array('label' => 'files_column_oai', 'class' => 'visibleoai')
    );
    
    public function init() {
        parent::init();
        
        $this->setLegend('admin_document_section_files');

        $header = new Application_Form_TableHeader($this->header);

        $this->addSubForm($header, 'Header');

        $this->setDecorators(array(
            'FormElements',
            array(array('table' => 'HtmlTag'), array('tag' => 'table')),
            array(array('fieldsWrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'fields-wrapper')),
            'Fieldset',
            array(array('divWrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'subform'))
        ));
    }

    public function populateFromModel($document) {
        foreach ($document->getFile() as $file) {
            $this->addFileSubForm($file);
        }
    }

    protected function addFileSubForm($file) {
        $form = new Admin_Form_Document_File();
        $form->populateFromModel($file);
        $index = count($this->getSubForms()) - 1;
        $form->setOrder($index + 1);
        $this->addSubForm($form, 'File' . $index);
    }


}