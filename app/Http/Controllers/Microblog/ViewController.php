<?php

namespace Coyote\Http\Controllers\Microblog;

use Coyote\Http\Controllers\Controller;
use Coyote\Repositories\Contracts\MicroblogRepositoryInterface as Microblog;

class ViewController extends Controller
{
    /**
     * @param $id
     * @param Microblog $repository
     * @return $this
     */
    public function index($id, Microblog $repository)
    {
        $microblog = $repository->findOrFail($id);
        $excerpt = excerpt($microblog->text);

        $this->breadcrumb->push('Mikroblog', route('microblog.home'));
        $this->breadcrumb->push($excerpt, route('microblog.view', [$microblog->id]));

        $microblog->text = app()->make('Parser\Microblog')->parse($microblog->text);
        $parser = app()->make('Parser\Comment');

        foreach ($microblog->comments as &$comment) {
            $comment->text = $parser->parse($comment->text);
        }

        return parent::view('microblog.view')->with(['microblog' => $microblog, 'excerpt' => $excerpt]);
    }
}
