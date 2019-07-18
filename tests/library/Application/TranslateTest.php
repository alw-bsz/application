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
 * @category    Application Unit Test
 * @package     Application
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2008-2018, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

class Application_TranslateTest extends ControllerTestCase
{

    private $translate;

    public function setUp()
    {
        parent::setUp();
        $this->translate = new Application_Translate();
    }

    public function testConstruct()
    {
        $translate = new Application_Translate([
            'content' => APPLICATION_PATH . '/modules/default/language/default.tmx',
        ]);

        $this->assertTrue($translate->isTranslated('home_menu_label'));
    }

    public function testLoadModule()
    {
        $this->assertFalse($this->translate->isTranslated('home_menu_label'));

        $this->translate->loadModule('default');

        $this->assertTrue($this->translate->isTranslated('home_menu_label')); // default immer noch geladen
    }

    public function testLoadModuleUnknown()
    {
        $logger = new MockLogger();

        $this->translate->setLogger($logger);

        $this->translate->loadModule('default');

        $this->assertTrue($this->translate->isTranslated('home_menu_label'));

        $this->translate->loadModule('unknown');

        $this->assertTrue($this->translate->isTranslated('home_menu_label')); // default immer noch geladen

        $messages = $logger->getMessages();

        $this->assertEquals(0, count($messages)); // warning normally suppressed

        $this->translate->loadLanguageDirectory(APPLICATION_PATH . '/modules/rewrite/language');
        $this->translate->loadLanguageDirectory(APPLICATION_PATH . '/modules/rewrite/language_custom');

        $messages = $logger->getMessages();

        $this->assertEquals(2, count($messages));
        $this->assertContains('not found', $messages[0]);
        $this->assertContains('not found', $messages[1]);
    }

    public function testLoadModuleTwice()
    {
        $this->assertFalse($this->translate->isTranslated('admin_document_index'));

        $logger = new MockLogger();

        $this->translate->setLogger($logger);
        $this->translate->loadModule('admin');

        $this->assertTrue($this->translate->isTranslated('admin_document_index'));
        $this->assertEquals(0, count($logger->getMessages()));

        $logger->clear();

        $this->translate->loadModule('admin'); // wird nicht noch einmal geladen

        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertEquals('Already loaded translations for module \'admin\'.', $messages[0]);
    }

    public function testLoadModules()
    {
        $this->assertFalse($this->translate->isTranslated('home_menu_label')); // 'default' module
        $this->assertFalse($this->translate->isTranslated('home_index_contact_pagetitle')); // 'home' module
        $this->assertFalse($this->translate->isTranslated('admin_index_title')); // 'admin' module

        $this->translate->loadModules();

        $this->assertTrue($this->translate->isTranslated('home_menu_label')); // 'default' module
        $this->assertTrue($this->translate->isTranslated('home_index_contact_pagetitle')); // 'home' module
        $this->assertTrue($this->translate->isTranslated('admin_index_title')); // 'admin' module
    }

    public function testGetLogger()
    {
        $logger = $this->translate->getLogger();

        $this->assertNotNull($logger);
        $this->assertInstanceOf('Zend_Log', $logger);
    }

    public function testSetLogger()
    {
        $logger = new MockLogger();

        $this->translate->setLogger($logger);

        $this->assertNotNull($this->translate->getLogger());
        $this->assertInstanceOf('MockLogger', $this->translate->getLogger());
    }

    public function testLoadLanguageDirectory()
    {
        $this->assertFalse($this->translate->isTranslated('admin_document_index'));

        $this->translate->loadLanguageDirectory(APPLICATION_PATH . '/modules/admin/language');

        $this->assertTrue($this->translate->isTranslated('admin_document_index'));
    }

    public function testLoadLanguageDirectoryNotFound()
    {
        $logger = new MockLogger();

        $this->translate->setLogger($logger);
        $this->assertFalse($this->translate->loadLanguageDirectory(APPLICATION_PATH . '/unknown'));

        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertContains('not found', $messages[0]);
    }

    public function testLoadLanguageDirectoryNoFiles()
    {
        $this->assertTrue($this->translate->loadLanguageDirectory(APPLICATION_PATH . '/modules'));
    }

    /**
     * Für Unit Tests ist das Logging von Untranslated normalerweise eingeschaltet.
     */
    public function testIsLogUntranslatedEnabledTrue()
    {
        $config = Zend_Registry::get('Zend_Config');
        $logUntranslated = $config->log->untranslated;
        $config->log->untranslated = true;
        $this->assertTrue($this->translate->isLogUntranslatedEnabled());
        $config->log->untranslated = $logUntranslated;
    }

    public function testIsLogUntranslatedEnabledFalse()
    {
        $config = Zend_Registry::get('Zend_Config');
        $config->log->untranslated = false;
        $this->assertFalse($this->translate->isLogUntranslatedEnabled());
        $config->log->untranslated = true; // Siehe testIsLogUntranslatedEnabledTrue
    }

    public function testGetOptionsLogEnabled()
    {
        $config = Zend_Registry::get('Zend_Config');
        $logUntranslated = $config->log->untranslated;
        $config->log->untranslated = true;

        $options = $this->translate->getOptions();

        $this->assertInternalType('array', $options);
        $this->assertEquals(10, count($options));
        $this->assertArrayHasKey('log', $options);
        $this->assertInstanceOf('Zend_Log', $options['log']);
        $this->assertTrue($options['logUntranslated']);

        $config->log->untranslated = $logUntranslated;
    }

    public function testGetOptionsLogDisabled()
    {
        $config = Zend_Registry::get('Zend_Config');
        $logUntranslated = $config->log->untranslated;
        $config->log->untranslated = false;

        $options = $this->translate->getOptions();

        $this->assertInternalType('array', $options);
        $this->assertEquals(10, count($options));
        $this->assertFalse($options['logUntranslated']);

        $config->log->untranslated = $logUntranslated;
    }

    public function testLoggingEnabled()
    {
        $config = Zend_Registry::get('Zend_Config');
        $logUntranslated = $config->log->untranslated;
        $config->log->untranslated = true;

        $logger = new MockLogger();

        $translate = new Application_Translate(['log' => $logger]);
        $translate->loadModule('default');

        $this->assertFalse($translate->isTranslated('nottranslated123'));
        $this->assertEquals('nottranslated123', $translate->translate('nottranslated123'));

        $messages = $logger->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('Unable to translate', $messages[0]);

        $config->log->untranslated = $logUntranslated;
    }

    public function testLoggingDisabled()
    {
        $config = Zend_Registry::get('Zend_Config');
        $logUntranslated = $config->log->untranslated;
        $config->log->untranslated = false;

        $logger = new MockLogger();

        $translate = new Application_Translate(['log' => $logger]);
        $translate->loadModule('admin');

        $this->assertFalse($translate->isTranslated('nottranslated123'));
        $this->assertEquals('nottranslated123', $translate->translate('nottranslated123'));

        $messages = $logger->getMessages();
        $this->assertEquals(0, count($messages));

        $config->log->untranslated = $logUntranslated;
    }

    public function enLanguageDataProvider()
    {
        return [
            ['deu', 'German'],
            ['eng', 'English'],
            ['fra', 'French'],
            ['rus', 'Russian'],
            ['spa', 'Spanish'],
            ['ita', 'Italian'],
            ['por', 'Portuguese'],
            ['mul', 'Multiple languages']
        ];
    }

    public function deLanguageDataProvider()
    {
        return [
            ['deu', 'Deutsch'],
            ['eng', 'Englisch'],
            ['fra', 'Französisch'],
            ['rus', 'Russisch'],
            ['spa', 'Spanisch'],
            ['ita', 'Italienisch'],
            ['por', 'Portugiesisch'],
            ['mul', 'Mehrsprachig']
        ];
    }

    /**
     * @dataProvider enLanguageDataProvider
     */
    public function testTranslateLanguageEnglish($langId, $translation)
    {
        $this->translate->setLocale('en');
        $this->translate->loadModule('default');
        $this->assertEquals(strtolower($translation), strtolower($this->translate->translateLanguage($langId)));
    }

    /**
     * @dataProvider deLanguageDataProvider
     */
    public function testTranslateLanguageGerman($langId, $translation)
    {
        $this->translate->setLocale('de');
        $this->translate->loadModule('default');
        $this->assertEquals($translation, $this->translate->translateLanguage($langId));
    }

    public function testMixedTranslations()
    {
        $key = 'admin_title_configuration';

        // clear custom translations from database
        $database = new Opus_Translate_Dao();
        $database->removeAll();

        $translate = Zend_Registry::get('Zend_Translate');

        // clear translation cache
        $translate->clearCache();

        $translate->loadModule('admin');

        $result = $translate->translate($key, 'de');

        $this->assertNotEquals($key, $result);
        $this->assertEquals('Konfiguration', $result);

        $result = $translate->translate($key, 'en');

        $this->assertNotEquals($key, $result);
        $this->assertEquals('Configure', $result);

        // clear cache again before modifying translations in database
        $translate->clearCache();

        // add database to translation mix
        $database->setTranslation($key, [
            'en' => 'Configuration',
            'de' => 'Einstellungen'
        ], 'admin');

        // load module again with changes in database
        $translate->loadModule('admin', true);

        $result = $translate->translate($key, 'en');
        $this->assertEquals('Configuration', $result);

        $result = $translate->translate($key, 'de');
        $this->assertEquals('Einstellungen', $result);
    }

    public function testGetTranslations()
    {
        $translate = Zend_Registry::get('Zend_Translate');

        $key = 'default_collection_role_ddc';

        $translations = $translate->getTranslations($key);

        $this->assertEquals([
            'de' => 'DDC-Klassifikation',
            'en' => 'Dewey Decimal Classification'
        ], $translations);

        $database = new Opus_Translate_Dao();

        $custom = [
            'de' => 'DDC-Sachgruppen',
            'en' => 'DDC'
        ];

        $database->setTranslation($key, $custom, 'default');

        $translate->clearCache();

        // new object necessary, because translation have already been loaded
        $translate = new Application_Translate();
        $translate->clearCache();
        $translate->loadModule('default', true);

        $translations = $translate->getTranslations($key);

        $this->assertEquals($custom, $translations);
    }

    public function testGetTranslationsUnknownKey()
    {
        $translate = Zend_Registry::get('Zend_Translate');

        $this->assertNull($translate->getTranslations('unknownkey9999'));
    }

    public function testSetTranslations()
    {
        $dao = new Opus_Translate_Dao();

        $dao->remove('testkey');

        $this->assertNull($dao->getTranslation('testkey'));

        $translate = Zend_Registry::get('Zend_Translate');

        $data = [
            'en' => 'test key',
            'de' => 'Testschüssel'
        ];

        $translate->setTranslations('testkey', $data);

        $this->assertEquals($data, $dao->getTranslation('testkey'));
    }
}
