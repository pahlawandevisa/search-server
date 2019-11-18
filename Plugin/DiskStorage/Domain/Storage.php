<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Apisearch\Plugin\DiskStorage\Domain;

use React\Filesystem\Filesystem;
use React\Filesystem\Node\Directory;
use React\Filesystem\Stream\WritableStream;
use React\Promise;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;
use React\Stream\WritableStreamInterface;

/**
 * Class Storage.
 */
class Storage
{
    /**
     * @var Filesystem
     *
     * Filesystem
     */
    private $filesystem;

    /**
     * @var string
     *
     * Path
     */
    private $path;

    /**
     * @var array
     *
     * Data
     */
    private $data = [];

    /**
     * @var bool
     *
     * Reload all reads
     */
    private $reloadReads;

    /**
     * TokenRedisRepository constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $path
     * @param bool       $reloadReads
     */
    public function __construct(
        Filesystem $filesystem,
        string $path,
        bool $reloadReads
    ) {
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->reloadReads = $reloadReads;
    }

    /**
     * Set.
     *
     * @param string $namespace
     * @param string $key
     * @param mixed  $value
     *
     * @return PromiseInterface
     */
    public function set(
        string $namespace,
        string $key,
        $value
    ): PromiseInterface {
        return $this
            ->getReadPromise()
            ->then(function () use ($namespace, $key, $value) {
                if (!isset($this->data[$namespace])) {
                    $this->data[$namespace] = [];
                }

                $this->data[$namespace][$key] = $value;

                return $this->saveDataToDisk($namespace);
            });
    }

    /**
     * Get.
     *
     * @param string $namespace
     * @param string $key
     *
     * @return PromiseInterface
     */
    public function get(
        string $namespace,
        string $key
    ): PromiseInterface {
        return $this
            ->getReadPromise()
            ->then(function () use ($namespace, $key) {
                return new FulfilledPromise(
                    isset($this->data[$namespace]) && isset($this->data[$namespace][$key])
                        ? $this->data[$namespace][$key]
                        : null
                );
            });
    }

    /**
     * Get all.
     *
     * @param string $namespace
     *
     * @return PromiseInterface
     */
    public function getAll(string $namespace): PromiseInterface
    {
        return $this
            ->getReadPromise()
            ->then(function () use ($namespace) {
                return new FulfilledPromise(
                    isset($this->data[$namespace])
                        ? $this->data[$namespace]
                        : []
                );
            });
    }

    /**
     * Delete.
     *
     * @param string $namespace
     * @param string $key
     *
     * @return PromiseInterface
     */
    public function del(
        string $namespace,
        string $key
    ): PromiseInterface {
        return $this
            ->getReadPromise()
            ->then(function () use ($namespace, $key) {
                if (
                    isset($this->data[$namespace]) &&
                    isset($this->data[$namespace][$key])
                ) {
                    unset($this->data[$namespace][$key]);
                }

                return $this->saveDataToDisk($namespace);
            });
    }

    /**
     * Delete all.
     *
     * @param string $namespace
     *
     * @return PromiseInterface
     */
    public function delAll(string $namespace): PromiseInterface
    {
        return $this
            ->getReadPromise()
            ->then(function () use ($namespace) {
                if (isset($this->data[$namespace])) {
                    unset($this->data[$namespace]);
                }

                return $this->saveDataToDisk($namespace);
            });
    }

    /**
     * Get read promise.
     *
     * @return PromiseInterface
     */
    private function getReadPromise(): PromiseInterface
    {
        $promise = new FulfilledPromise();
        if ($this->reloadReads) {
            $promise = $promise->then(function () {
                return $this->loadAllFromDisk();
            });
        }

        return $promise;
    }

    /**
     * Load all data.
     *
     * @return PromiseInterface
     */
    public function loadAllFromDisk(): PromiseInterface
    {
        return $this
            ->filesystem
            ->dir($this->path)
            ->createRecursive()
            ->then(null, function(\Exception $e) {
                // Directory creation failed because exists, or because is not
                // possible to make it. If is the first, continue. Otherwise
                // will fail in the next function
            })
            ->then(function() {

                return $this
                    ->filesystem
                    ->dir($this->path)
                    ->ls()
                    ->then(function (\SplObjectStorage $files) {
                        $promises = [];
                        foreach ($files as $file) {

                            if ($file instanceof Directory) {
                                continue;
                            }

                            $namespace = str_replace('.json', '', $file->getName());
                            $promises[] = $file
                                ->getContents()
                                ->then(function (string $content) use ($namespace) {
                                    $this->data[$namespace] = json_decode($content, true);
                                });
                        }

                        return Promise\all($promises);
                    });
            });
    }

    /**
     * Save data to disk.
     *
     * @param string $namespace
     *
     * @return PromiseInterface
     */
    private function saveDataToDisk(string $namespace): PromiseInterface
    {
        return $this
            ->filesystem
            ->file($this->path.'/'.$namespace.'.json')
            ->open('w')
            ->then(function (WritableStreamInterface $stream) use ($namespace) {
                return $stream->write(json_encode(
                    $this->data[$namespace] ?? []
                ));
            });

    }
}
