<?php

/*
 * This file is part of the Yosymfony\Spress.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yosymfony\Spress\Core\DependencyResolver;

use Yosymfony\Spress\Core\DataSource\ItemInterface;

/**
 * Iterface for a dependency resolver. A dependency resolver record item
 * dependencies.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
interface DependencyResolverInterface
{
    /**
     * Registers a dependency between files.
     *
     * @param string $idA
     * @param string $idDependentOnA
     */
    public function registerDependency($idA, $idDependentOnA);

    /**
     * Returns the list of identifiers that are depending on the id passed as argument.
     *
     * @param string $id The identifier on which dependencies are looked for.
     *
     * @return string[] A list of identifiers.
     */
    public function getIdsDependingOn($id);

    /**
     * Returns the current set of dependencies.
     *
     * @return array List of dependencies.
     */
    public function getAllDependencies();

    /**
     * Clears all the registered dependencies.
     */
    public function clear();
}
