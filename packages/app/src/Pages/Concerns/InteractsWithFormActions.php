<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait InteractsWithFormActions
{
    protected array $cachedFormActions = [];

    public function bootedInteractsWithFormActions(): void
    {
        $this->cacheFormActions();
    }

    protected function cacheFormActions(): void
    {
        /** @var array<string, Action | ActionGroup> */
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getFormActions(),
        );

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    /** @var Action $groupedAction */

                    $groupedAction->livewire($this);

                    $this->cacheAction($groupedAction);
                }

                $this->cachedActions[$index] = $action;

                continue;
            }

            $this->cacheAction($action);
            $this->cachedFormActions[$action->getName()] = $action;
        }
    }

    /**
     * @return array<string, Action | ActionGroup>
     */
    public function getCachedFormActions(): array
    {
        return $this->cachedFormActions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}