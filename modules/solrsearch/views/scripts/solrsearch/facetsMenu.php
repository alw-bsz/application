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
 * @category    TODO
 * @author      Julian Heise <heise@zib.de>
 * @copyright   Copyright (c) 2008-2010, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */
?>

<div id="facets">
    <h3><?= $this->translate('facets_menu_title') ?></h3>

    <?php foreach($this->facets as $key=>$facet) : ?>

    <div id="<?= $key ?>_facet" class="facet">
        <?= $this->translate($key."_facet_heading") ?>
        <ul>
            <?php foreach($facet as $facetItem) :
                $fqUrl = $this->firstPage;
                $fqUrl[$key.'fq'] = $facetItem->getText();
            ?>
                <li <?= array_key_exists($key, $this->selectedFacets) ? 'class="activeFacet"' : '' ?>><a href="<?= $this->url($fqUrl) ?>"><?= $facetItem->getText() ?></a> (<?= $facetItem->getCount() ?>)
                <?php if(array_key_exists($key, $this->selectedFacets)) :
                    $fqUrl = $this->firstPage;
                    $fqUrl[$key.'fq'] = '';
                ?>
                <a href="<?= $this->url($fqUrl) ?>" class="removeFacetLink">(<?= $this->translate('facets_remove') ?>)</a>
                <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>

    <?php endforeach ?>
</div>