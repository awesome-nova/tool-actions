<?php
declare(strict_types=1);

namespace AwesomeNova\Actions;

use AwesomeNova\FakeActionModelCollection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionMethod;
use Laravel\Nova\Actions\DispatchAction;
use Laravel\Nova\Exceptions\MissingActionHandlerException;
use Laravel\Nova\Http\Requests\ActionRequest;

abstract class ToolAction extends Action
{
    public $toolAction = true;

    public $onlyOnIndex = true;

    public $onlyOnDetail = true;

    public function label()
    {
        return $this->name();
    }

    /**
     * Execute the action for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest $request
     * @return mixed
     */
    public function handleRequest(ActionRequest $request)
    {
        $method = ActionMethod::determine($this, $request->targetModel());

        if (! method_exists($this, $method)) {
            throw MissingActionHandlerException::make($this, $method);
        }

        $fields = $request->resolveFields();

        return DispatchAction::forModels(
            $request, $this, $method, new FakeActionModelCollection(), $fields
        );
    }

    public function jsonSerialize()
    {
        return parent::jsonSerialize() + [
                'toolAction' => $this->toolAction,
                'label' => $this->label(),
            ];
    }
}

