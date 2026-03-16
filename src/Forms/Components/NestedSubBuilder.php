<?php

namespace Thiktak\FilamentNestedBuilderForm\Forms\Components;

use Filament\Forms\Components\Builder;
use Filament\Schemas\Schema;

class NestedSubBuilder extends Builder
{
    public NestedBuilder $nestedBuilder;

    public int $level = 1;

    public function nestedBuilder(NestedBuilder $nestedBuilder): self
    {
        $this->nestedBuilder = $nestedBuilder;

        return $this;
    }

    public function getNestedBuilder(): NestedBuilder
    {
        return $this->nestedBuilder ?? $this;
    }

    public function level(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getDefaultChildComponents(): array|Schema
    {
        if (empty($this->childComponents['default'] ?? null)) {
            $this->childComponents['default'] = (array) $this->evaluate(
                $this->getNestedBuilder()->getNestedNamedChildComponents(),
                [
                    'builder' => $this,
                    'parent' => $this->getNestedBuilder(),
                ]
            );
        }

        return parent::getDefaultChildComponents();
    }

    public function importNestedBlocks($make, ?string $name = null): Builder
    {
        $nestedComponents = $this->getNestedBuilder()->getNestedNamedChildComponents($name ?: 'default');

        $builder = NestedSubBuilder::make($make)
            ->nestedBuilder($this->getNestedBuilder())
            ->level($this->getLevel() + 1);

        $this->getNestedBuilder()
            ->getNestedConfiguration($builder);

        $builder = $builder
            ->schema(fn () => $this->evaluate(
                $nestedComponents,
                [
                    'builder' => $builder,
                    'parent' => $this->getNestedBuilder(),
                ]
            ));

        return $builder;
    }
}
