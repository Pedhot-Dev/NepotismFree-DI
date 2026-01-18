<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Contract;

interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @template T
     * @param string|class-string<T> $id Identifier of the entry to look for.
     *
     * @throws Exception\ContainerExceptionInterface Error while retrieving the entry.
     * @throws Exception\NotFoundExceptionInterface  No entry was found for this identifier.
     *
     * @return mixed|T Entry.
     */
    public function get(string $id): mixed;

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;
    /**
     * @template T
     * @param string|class-string<T> $id
     * @return mixed|T
     */
    /**
     * Resolve a tag into an iterable of services.
     * 
     * @return iterable<mixed>
     */
    public function getTagged(string $tag): iterable;
}
