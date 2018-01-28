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

/**
 * A simple dependency resolver. It is useful for keeping tracks of
 * inter-document dependencies.
 */
class DependencyResolver implements DependencyResolverInterface
{
    private $dependencies =  [];
    private $contentPathPrefix;
    private $layoutPathPrefix;

    /**
     * Constructor.
     *
     * @param array $dependencies Initial set of dependencies.
     */
    public function __construct(array $dependencies = [])
    {
        $this->dependencies = $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function registerDependency($idA, $idDependentOnA)
    {
        if (isset($this->dependencies[$idA]) === false) {
            $this->dependencies[$idA] = [];
        }

        if (isset($this->dependencies[$idDependentOnA]) === false) {
            $this->dependencies[$idDependentOnA] = [];
        }

        if (in_array($idDependentOnA, $this->dependencies[$idA]) === false) {
            $this->dependencies[$idA][] = $idDependentOnA;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If a circular reference is detected.
     */
    public function getIdsDependingOn($id)
    {
        $resolved = [];
        $unresolved = [];
        $this->dependencyResolve($id, $resolved, $unresolved);

        return array_keys($resolved);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDependencies()
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->dependencies = [];
    }

    private function dependencyResolve($id, array &$resolved, array &$unresolved)
    {
        $unresolved[$id] = 1;

        foreach ($this->dependencies[$id] as $idEdge) {
            if (isset($resolved[$idEdge]) === false) {
                if (isset($unresolved[$idEdge])) {
                    throw new \RuntimeException(sprintf('Circular reference detected: "%s" -> "%s".', $id, $idEdge));
                }

                $this->dependencyResolve($idEdge, $resolved, $unresolved);
            }
        }

        $resolved[$id] = 1;
        unset($unresolved[$id]);
    }
}
