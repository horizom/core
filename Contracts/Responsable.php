<?php

namespace Horizom\Core\Contracts;

interface Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Horizom\Http\Request  $request
     * @return \Horizom\Http\Response
     */
    public function toResponse($request);
}
