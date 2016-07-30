<?php

namespace Coyote\Services\Stream\Render;

class Wiki extends Render
{
    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function object()
    {
        return trans('stream.' . $this->stream['object.objectType']);
    }

    /**
     * @return string
     */
    protected function title()
    {
        return $this->objectName();
    }
}