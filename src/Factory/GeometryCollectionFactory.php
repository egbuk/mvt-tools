<?php

namespace HeyMoon\VectorTileDataProvider\Factory;

use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\Geometry;
use Brick\Geo\GeometryCollection;
use ReflectionClass;

class GeometryCollectionFactory
{
    private array $collectionClass = [];

    /**
     * @param Geometry[] $geometries
     * @return GeometryCollection
     * @throws CoordinateSystemException
     * @throws UnexpectedGeometryException
     */
    public function get(array $geometries): GeometryCollection
    {
        $first = array_shift($geometries);
        $collectionClass = $this->collectionClass[$first::class] ?? $this->getCollectionClass($first);
        return $collectionClass::of($first, ...$geometries)->withSRID($first->SRID());
    }

    protected function getCollectionClass(Geometry $geometry): string
    {
        $reflection = new ReflectionClass($geometry);
        /** @var GeometryCollection $collectionClass */
        return $this->collectionClass[$geometry::class] = "{$reflection->getNamespaceName()}\Multi{$reflection->getShortName()}";
    }
}
