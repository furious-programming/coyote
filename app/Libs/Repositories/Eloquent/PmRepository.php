<?php

namespace Coyote\Repositories\Eloquent;

use Coyote\Repositories\Contracts\PmRepositoryInterface;
use Coyote\Repositories\Contracts\UserRepositoryInterface as User;
use Illuminate\Http\Request;
use Illuminate\Container\Container as App;
use Coyote\Pm;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Class PmRepository
 * @package Coyote\Repositories\Eloquent
 */
class PmRepository extends Repository implements PmRepositoryInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param App $app
     * @param User $user
     */
    public function __construct(App $app, User $user)
    {
        parent::__construct($app);

        $this->user = $user;
    }
    /**
     * @return \Coyote\Pm
     */
    public function model()
    {
        return 'Coyote\Pm';
    }

    /**
     * Get last messages
     *
     * @param int $userId
     * @param int $limit
     * @return mixed
     */
    public function takeForUser($userId, $limit = 10)
    {
        return $this->prepare($userId)->limit($limit)->get();
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($userId, $perPage = 10)
    {
        $count = $this->model
                ->select(\DB::raw('COUNT(*)'))
                ->where('user_id', $userId)
                ->groupBy('root_id')
                ->get()
                ->count();

        $result = $this->prepare($userId)
                ->limit($perPage)
                ->skip(request('page') * $perPage)
                ->get();

        return new LengthAwarePaginator($result, $count, $perPage, request('page'));
    }

    /**
     * Gets conversation
     *
     * @param int $userId
     * @param string $rootId
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function talk($userId, $rootId, $limit = 10, $offset = 0)
    {
        $result = $this->model->select([
                    'pm.*',
                    'pm_text.text',
                    'pm_text.created_at',
                    'name',
                    'is_active',
                    'is_blocked',
                    'photo'
                ])
                ->join('pm_text', 'pm_text.id', '=', 'text_id')
                ->join('users', 'users.id', '=', 'author_id')
                ->where('user_id', $userId)
                ->where('root_id', $rootId)
                ->take($limit)
                ->skip($offset)
                ->orderBy('pm.id', 'DESC')
                ->get();

        return $result->reverse();
    }

    /**
     * Submit a new message
     *
     * @param \Coyote\User $user
     * @param Request $request
     * @throws \Exception
     */
    public function submit(\Coyote\User $user, Request $request)
    {
        $rootId = $request->get('root_id', dechex(mt_rand(0, 0x7fffffff)));
        $author = $this->user->findByName($request->get('author'));

        if (!$author) {
            throw new \Exception('Can not get recipient ID.');
        }

        $text = Pm\Text::create(['text' => $request->get('text')]);
        $fill = [
            'root_id' => $rootId,
            'text_id' => $text->id
        ];

        // we need to create two records. one for recipient and one for message author
        $this->model->create($fill + ['user_id' => $author->id, 'author_id' => $user->id, 'folder' => Pm::INBOX]);
        $pm = $this->model->create($fill + ['user_id' => $user->id, 'author_id' => $author->id, 'folder' => Pm::SENTBOX]);

        return $pm;
    }

    /**
     * Mark notifications as read
     *
     * @param int $id
     */
    public function markAsRead($id)
    {
        $this->model->where('id', $id)->update(['read_at' => Carbon::now()]);
    }

    /**
     * Prepare statement with subquery
     *
     * @param int $userId
     * @return mixed
     */
    private function prepare($userId)
    {
        return \DB::table(\DB::raw('(' . $this->subSql($userId)->toSql() . ') AS m'))
            ->select([
                'm.id',
                'm.author_id',
                'm.folder',
                'm.read_at',
                'pm_text.text',
                'pm_text.created_at',
                'name',
                'is_active',
                'is_blocked',
                'photo'
            ])
            ->join('pm_text', 'pm_text.id', '=', 'text_id')
            ->join('users', 'users.id', '=', 'author_id')
            ->orderBy('m.id', 'DESC');
    }

    /**
     * Returns subquery
     *
     * @param int $userId
     * @return \Coyote\Pm
     */
    private function subSql($userId)
    {
        return $this->model
            ->select([\DB::raw('DISTINCT ON (root_id) pm.*')])
            ->whereRaw("user_id = $userId")
            ->orderBy('root_id')
            ->orderBy('pm.id', 'DESC');
    }
}
