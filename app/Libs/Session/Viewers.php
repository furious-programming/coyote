<?php

namespace Coyote\Session;

use Coyote\Repositories\Contracts\SessionRepositoryInterface as Session;
use Illuminate\Http\Request;

/**
 * Generuje widok przedstawiajacy liste osob na danej stronie z podzialem na boty, zalogowane osoby itp
 *
 * Class Viewers
 * @package Coyote\Session
 */
class Viewers
{
    const USER = 'Użytkownik';
    const ROBOT = 'Robot';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Session $session
     * @param Request $request
     */
    public function __construct(Session $session, Request $request)
    {
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * Generuje widok userow online. W parametrze nalezy podac sciezke - np. /Forum
     *
     * @param null $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render($path = null)
    {
        $groups = [self::USER => [], self::ROBOT => []];
        $collection = $this->session->viewers($path);

        // zlicza liczbe userow
        $total = $collection->count();
        // zlicza gosci online (niezalogowani uzytkownicy)
        $guests = $collection->where('user_id', null)->count();

        // zlicza ilosc robotow na stronie
        $robots = $collection->filter(function ($item) {
            return $item->robot;
        })
        ->count();

        // liczba rzeczywistych osob z pominieciem botow
        $people = $total - $robots;

        foreach ($collection->groupBy('group') as $name => $rowset) {
            if ($name == '') {
                $name = self::USER;
            } elseif (!isset($groups[$name])) {
                $groups[$name] = [];
            }

            foreach ($rowset as $user) {
                if ($user->user_id) {
                    $groups[$name][] = $this->makeProfileLink($user->id, $user->name);
                }
            }
        }

        foreach ($collection->groupBy('robot') as $name => $rowset) {
            if ($name) {
                $groups[self::ROBOT][] = $name . (count($rowset) > 1 ? ' (' . count($rowset) . 'x)' : '');
            }
        }

        // moze sie okazac ze wsrod sesji nie ma ID sesji aktualnego requestu poniewaz tabela session
        // nie zostala jeszcze zaktualizowana. w takim przypadku bedziemy musieli dodac "recznie"
        // uzytkownika ktory aktualnie dokonal tego zadania
        if (!$collection->contains('id', $this->request->session()->getId())) {
            $people++;
            $total++;

            if ($this->request->user()) {
                $groupName = self::USER;

                if ($this->request->user()->group_id) {
                    $groupName = $this->request->user()->group->name;
                }

                $groups[$groupName] = $this->makeProfileLink($this->request->user()->id, $this->request->user()->name);
            }
        }

        return view('components/viewers', compact('groups', 'total', 'guests', 'people', 'robots'));
    }

    private function makeProfileLink($userId, $userName)
    {
        return link_to_route(
            'profile',
            $userName,
            [$userId],
            ['data-user-id' => $userId]
        );
    }
}
