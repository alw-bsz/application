<?xml version="1.0" encoding="utf-8"?>
<!--
/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the North Rhine-Westphalian Library Service Center,
 * the Cooperative Library Network Berlin-Brandenburg, the Saarland University
 * and State Library, the Saxon State Library - Dresden State and University
 * Library, the Bielefeld University Library and the University Library of
 * Hamburg University of Technology with funding from the German Research
 * Foundation and the European Regional Development Fund.
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
 * @package     Module_CitationExport
 * @author      Oliver Marahrens <o.marahrens@tu-harburg.de>
 * @author      Gunar Maiwald <maiwald@zib.de>
 * @copyright   Copyright (c) 2010, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */
-->

<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xmlns:xml="http://www.w3.org/XML/1998/namespace"
    exclude-result-prefixes="php">

    <xsl:output method="text" omit-xml-declaration="yes" />

    <xsl:include href="utils/replace_nonascii.xslt"/>
    <xsl:include href="utils/bibtex_authors.xslt"/>
    <xsl:include href="utils/bibtex_editors.xslt"/>
    <xsl:include href="utils/bibtex_pages.xslt"/>

    <xsl:template match="/">
      <xsl:apply-templates select="Opus/Opus_Model_Filter" />
    </xsl:template>

    <!-- Suppress spilling values with no corresponding templates -->
    <xsl:template match="@*|node()" />

    <xsl:template match="Opus_Model_Filter">

        <!-- Preprocessing: some variables will be defined -->
        <xsl:variable name="year">
            <xsl:choose>
                <xsl:when test="string-length(normalize-space(CompletedDate/@Year)) > 0">
                    <xsl:value-of select="CompletedDate/@Year" />
                </xsl:when>
                <xsl:when test="normalize-space(@CompletedYear) != '0000'">
                    <xsl:value-of select="@CompletedYear" />
                </xsl:when>
                <xsl:when test="string-length(normalize-space(PublishedDate/@Year)) > 0">
                    <xsl:value-of select="PublishedDate/@Year" />
                </xsl:when>
                <xsl:when test="normalize-space(@PublishedYear) != '0000'">
                    <xsl:value-of select="@PublishedYear" />
                </xsl:when>
           </xsl:choose>
       </xsl:variable>

        <xsl:variable name="author">
            <xsl:apply-templates select="PersonAuthor">
                 <xsl:with-param name="type">author</xsl:with-param>
            </xsl:apply-templates>
        </xsl:variable>

        <xsl:variable name="identifier">
            <xsl:choose>
                <xsl:when test="string-length(normalize-space($author)) > 0">
                    <xsl:apply-templates select="PersonAuthor">
                         <xsl:with-param name="type">identifier</xsl:with-param>
                    </xsl:apply-templates>
                    <xsl:value-of select="$year" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>OPUS4-</xsl:text>
                    <xsl:value-of select="@Id" />
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="editor">
            <xsl:apply-templates select="PersonEditor" />
        </xsl:variable>

        <xsl:variable name="pages">
            <xsl:call-template name="Pages">
                <xsl:with-param name="first"><xsl:value-of select="@PageFirst" /></xsl:with-param>
                <xsl:with-param name="last"><xsl:value-of select="@PageLast" /></xsl:with-param>
            </xsl:call-template>
        </xsl:variable>

        <!--Output: print Opus-Document in bibtex -->
        <xsl:text>@incollection{</xsl:text><xsl:value-of select="$identifier" />,
<xsl:text></xsl:text>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">author   </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select="$author" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">title    </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="TitleMain/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">booktitle</xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="TitleParent/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">edition  </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="@Edition" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
	</xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">series   </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="TitleSub/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">editor   </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select="$editor" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">publisher</xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="PublisherName" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">address  </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="PublisherPlace" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">volume   </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="@Volume" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">number   </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="@Issue" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
	</xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">isbn     </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="IdentifierIsbn/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">doi      </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="IdentifierDoi/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">url      </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select ="IdentifierUrl/@Value" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">year     </xsl:with-param>
            <xsl:with-param name="value"><xsl:value-of select="$year" /></xsl:with-param>
            <xsl:with-param name="delimiter">,</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="outputFieldValue">
            <xsl:with-param name="field">note     </xsl:with-param>             
            <xsl:with-param name="value"><xsl:value-of select="Enrichment[@KeyName=$enrichment_note]/@Value" /></xsl:with-param>         
        </xsl:call-template>
<xsl:text>}</xsl:text>
     </xsl:template>

</xsl:stylesheet>