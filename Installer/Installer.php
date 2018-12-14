<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformEEDemoQuickInstallerBundle\Installer;

use EzSystems\PlatformInstallerBundle\Installer\CleanInstaller;
use Symfony\Component\Filesystem\Filesystem;

class Installer extends CleanInstaller
{
    /** @var string */
    private $projectDir;

    public function setProjectDir($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function importSchema()
    {

    }

    public function importData()
    {
        $this->db->exec('SET FOREIGN_KEY_CHECKS=0');

        $this->runQueriesFromFile(
            'zip://' . __DIR__ . '/../Resources/data/demo_data.zip#demo_data.sql'
        );

        $this->db->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public function importBinaries()
    {
        $this->output->writeln('Copying storage directory contents...');
        $fs = new Filesystem();
        $fs->mkdir('web/var/site');

        $imagesMapping = file_get_contents(__DIR__ . '/../Resources/data/image_migration_list.txt');
        $imagesMapping = explode("\r\n", $imagesMapping);

        foreach($imagesMapping as $mapping) {
            if(!strlen($mapping)) {
                continue;
            }

            $path = $this->projectDir . '/';
            list($from, $to) = explode(' ', $mapping);

            if (strlen($from) == 0 or strlen($to) == 0) {
                continue;
            }

            $fs->copy($path . $from, $path . $to);
        }
    }
}
