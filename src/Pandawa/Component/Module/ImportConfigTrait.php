<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Module;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ImportConfigTrait
{
    public function bootConfig(): void
    {
        $basePath = $this->getCurrentPath() . '/Resources/config';

        if (is_dir($basePath)) {
            $finder = new Finder();
            $configs = [];

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->name('*.php') as $file) {
                $configs[(string) $file] = config_path('modules/' . $file->getBasename());
                $this->mergeConfigFrom(
                    (string) $file,
                    sprintf('modules.%s', pathinfo($file->getBasename(), PATHINFO_FILENAME))
                );
            }

            $this->publishes($configs, 'config');
        }
    }
}
