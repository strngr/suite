<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\AuthRestApi;

use Spryker\Glue\AuthRestApi\AuthRestApiDependencyProvider as SprykerAuthRestApiDependencyProvider;
use Spryker\Glue\CompanyUserAuthRestApi\Plugin\AuthRestApi\CompanyUserRestUserIdentifierExpanderPlugin;

/**
 * @method \Spryker\Glue\AuthRestApi\AuthRestApiConfig getConfig()
 */
class AuthRestApiDependencyProvider extends SprykerAuthRestApiDependencyProvider
{
    /**
     * @return \Spryker\Glue\AuthRestApiExtension\Dependency\Plugin\RestUserIdentifierExpanderPluginInterface[]
     */
    protected function getRestUserIdentifierExpanderPlugins(): array
    {
        return [
            new CompanyUserRestUserIdentifierExpanderPlugin(),
        ];
    }
}
