<?php

namespace Coyote\Http\Controllers\Forum;

use Coyote\Http\Controllers\Controller;
use Coyote\Repositories\Contracts\ForumRepositoryInterface as Forum;
use Illuminate\Http\Request;
use Cache;

class HomeController extends Controller
{
    use Base;

    /**
     * @var Forum
     */
    private $forum;

    /**
     * @param Forum $forum
     */
    public function __construct(Forum $forum)
    {
        parent::__construct();

        $this->forum = $forum;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        $this->breadcrumb->push('Forum', route('forum.home'));

        $this->pushForumCriteria();
        // execute query: get all categories that user can has access
        $sections = $this->forum->groupBySections(auth()->id(), $request->session()->getId());

        // let's cache tags. we don't need to run this query every time
        $tags = Cache::remember('forum:tags', 60 * 24, function () {
            return $this->forum->getTagClouds();
        });

        // create view with online users
        $viewers = app()->make('Session\Viewers')->render($request->getRequestUri());

        return parent::view('forum.home')->with(compact('sections', 'viewers', 'tags'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function preview(Request $request)
    {
        $parser = app()->make('Parser\Post');
        return response($parser->parse($request->get('text')));
    }
}
